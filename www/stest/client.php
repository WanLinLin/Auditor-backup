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
// $UTF8sentence = unicode2utf8($input_sentence);

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
$sql = "(SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link >= 1 $sql_andTags ORDER BY link DESC limit 20) text)
     UNION   
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link >= 1 ORDER BY link DESC limit 15) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link < 1 $sql_andTags GROUP BY wId ORDER BY distance DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags in ($sql_orTags) ORDER BY distance DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `rhyme` using (wId) WHERE text like '$sql_num' and rhymes like '$rhyme%' ORDER BY rand() limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `rhyme` using (wId) WHERE text like '$sql_num' ORDER BY rand() limit 20) text) limit 20";

$dsn = "mysql:dbname=auditor;host=localhost";
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));

foreach( $db->query( $sql ) as $row )
{
    $sql_count = "SELECT COUNT(*) FROM ( SELECT DISTINCT author FROM `lyric` WHERE wId = $row[1] )a";
    $count_author = $db->query($sql_count)->fetch();
    $array[$count] = array("id" => $count, "tag" => $row[0], "count_author" => $count_author[0]);
    $count++;
}

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