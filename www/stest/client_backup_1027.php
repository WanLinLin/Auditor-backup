<?php
header("Content-Type:text/html; charset=utf-8");

if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg <br/>");
}
 
//Connect socket to remote server
if(!socket_connect($sock , '127.0.0.1' , 1242))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not connect: [$errorcode] $errormsg <br/>");
}

$input_sentence = "";
if(isset($_POST['sentence'])) { $input_sentence = $_POST['sentence']; }
else { exit("no input sentence!"); }
$UTF8sentence = unicode2utf8($input_sentence);

// Send the message to the server
if(!socket_send ( $sock , $input_sentence , strlen($input_sentence) , 0))
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not send data: [$errorcode] $errormsg <br/>");
}
 
// Now receive reply from server
if(socket_recv ( $sock , $buf , 2045 , MSG_WAITALL ) === FALSE)
{
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not receive data: [$errorcode] $errormsg <br/>");
}

socket_close($sock);


$rhyme = "";
if(isset($_POST['rhyme'])) { $rhyme = $_POST['rhyme']; }
else { exit("no input rhyme!"); }

$num = "";
if(isset($_POST['number'])) { $num = intval($_POST['number']); }
else { exit("no input number!"); }

$rest = substr($buf, 0, -1); // delete the last "/"
$tags = explode("/", $rest); // split string by "/"
$tag = $tags[count($tags) - 1];
$array = array();
$count = 0;
$sql_num = "";
$sql_orTags = "";
$sql_andTags = "";

for( $i = 0; $i < $num; $i++)
{
    $sql_num = $sql_num."_";
}

for($i = count($tags)-1; $i >= 0; $i--)
{
    $sql_orTags = $sql_orTags."'".$tags[$i]."',";
}
$sql_orTags = substr($sql_orTags,0,strlen($sql_orTags)-1);

for($i = count($tags)-2; $i >= 0; $i--)
{
    $sql_andTags = $sql_andTags." and wId IN (SELECT wId FROM `tag` WHERE tags = '". $tags[$i] ."')";
}

/* INCLUDE RHYME */
$sql = "(SELECT * FROM (SELECT  text FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link >= 1 $sql_andTags ORDER BY link DESC limit 20) text)
     UNION   
     (SELECT * FROM (SELECT  text FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link >= 1 ORDER BY link DESC limit 15) text)
     UNION
     (SELECT * FROM (SELECT  text FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link < 1 $sql_andTags GROUP BY wId ORDER BY distance DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT  text FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags in ($sql_orTags) ORDER BY SUM(distance) DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT  text FROM `word` join `rhyme` using (wId) WHERE text like '$sql_num' and rhymes like '$rhyme%' ORDER BY rand() limit 20) text)
     UNION
     (SELECT * FROM (SELECT  text FROM `word` join `rhyme` using (wId) WHERE text like '$sql_num' ORDER BY rand() limit 20) text) limit 20";

/* NO RHYME */

	// $sql = "(SELECT * FROM (SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and link >= 1 $sql_andTags ORDER BY link DESC limit 5) text)
 //     UNION
 //     (SELECT * FROM (SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and link >= 1 ORDER BY link DESC limit 4) text)
 //     UNION
 //     (SELECT * FROM (SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and link < 1 $sql_andTags GROUP BY wId ORDER BY distance DESC limit 5) text)
 //     UNION
 //     (SELECT * FROM (SELECT text FROM `word` join `tag` using (wId)  WHERE tags in ($sql_orTags) ORDER BY distance DESC limit 5) text)
 //     UNION
 //     (SELECT * FROM (SELECT text FROM `word` ORDER BY rand() limit 5) text) limit 5";

/* FIRST AND SECOND METHOD LIMIT 30 */
// $sql = "(SELECT * FROM (SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and link >= 1 $sql_andTags ORDER BY link DESC) text)
//         UNION
//         (SELECT * FROM (SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and link >= 1 ORDER BY link DESC) text) limit 30";

$dsn = "mysql:dbname=auditor;host=localhost";

$start_time = microtime(true);
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));

// while(true)
// {
// 	$sql = "SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and text like '$sql_num' and  link >= 1 $sql_andTags ORDER BY link DESC limit 5";
// 	foreach( $db->query( $sql ) as $row )
// 	{
// 		$array[$count] = array("id" => $count, "tag" => $row[0]);
// 		$count++;
// 	}
	
// 	if(count(array_unique($array))> 4)break;

// 	$sql = "SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and text like '$sql_num' and link >= 1 ORDER BY link DESC limit 4";
// 	foreach( $db->query( $sql ) as $row )
// 	{
// 		$array[$count] = array("id" => $count, "tag" => $row[0]);
// 		$count++;
// 	}
// 	if(count($array)> 4)break;
	
// 	$sql_test = "SELECT text FROM `word` join `tag` using (wId) WHERE tags = '$tag' and text like '$sql_num'and link < 1 $sql_andTags GROUP BY wId ORDER BY distance DESC limit 5";
// 	foreach( $db->query( $sql ) as $row )
// 	{
// 		$array[$count] = array("id" => $count, "tag" => $row[0]);
// 		$count++;
// 	}
// 	if(count($array)> 4)break;
	
// 	$sql_test = "SELECT text FROM `word` join `tag` using (wId) WHERE text like '$sql_num' and tags in ($sql_orTags) ORDER BY distance DESC limit 5";
// 	foreach( $db->query( $sql ) as $row )
// 	{
// 		$array[$count] = array("id" => $count, "tag" => $row[0]);
// 		$count++;
// 	}
// 	if(count($array)> 4)break;
	
// 	$sql_test = "SELECT text FROM `word` WHERE text like '$sql_num' ORDER BY rand() limit 5";
// 	foreach( $db->query( $sql ) as $row )
// 	{
// 		$array[$count] = array("id" => $count, "tag" => $row[0]);
// 		$count++;
// 	}
// 	if(count($array)> 4)break;
// }
foreach( $db->query( $sql ) as $row )
    {
        $array[$count] = array("id" => $count, "tag" => $row[0]);
        $count++;
    }

$end_time = microtime(true);
$duration = $end_time - $start_time;
$array[$count] = array("db_query_time" => $duration);
echo json_encode($array);

function unicode2utf8($str){
    if(!$str) return $str;

    $decode = json_decode($str);
    if($decode) return $decode;

    $str = '["' . $str . '"]';
    $decode = json_decode($str);
    if(count($decode) == 1){
            return $decode[0];
    }

    return $str;
}
?>