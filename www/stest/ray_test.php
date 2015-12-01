 <?php
$num = 2;
$tag = " 安安";
$tags = array("");
$rhyme = "ㄕ";
$array = array();
$array_wId = array();
$array_tmp = array();

$count = 0;
$count2 = 0;
$sql_num = "";
$sql_orTags = "";
$sql_andTags = "";
$array_wId = "";
$wId = array();
$array_tmp = array();

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

$sql = "(SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) join `lyric` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link >= 1 $sql_andTags ORDER BY link DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) join `lyric` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link < 1 $sql_andTags GROUP BY wId ORDER BY distance DESC limit 15) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) join `lyric` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags = '$tag' and link >= 1 ORDER BY link DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `tag` using (wId) join `rhyme` using (wId) join `lyric` using (wId) WHERE rhymes like '$rhyme%' and text like '$sql_num' and tags in ($sql_orTags) ORDER BY distance DESC limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `rhyme` using (wId) join `lyric` using (wId) WHERE text like '$sql_num' and rhymes like '$rhyme%' ORDER BY rand() limit 20) text)
     UNION
     (SELECT * FROM (SELECT distinct text,wId FROM `word` join `rhyme` using (wId) join `lyric` using (wId) WHERE text like '$sql_num' ORDER BY rand() limit 20) text) limit 20";


// $sql_next = "(SELECT * FROM `lyric` WHERE wId IN ($array_next))";

$dsn = "mysql:dbname=auditor;host=localhost";
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));

foreach( $db->query( $sql ) as $row )
{
	$sql_count = "SELECT COUNT(*) FROM ( SELECT DISTINCT author FROM `lyric` WHERE wId = $row[1] )a";
	echo $row[1]."<br/>";
	// echo $sql_count;
	// $result = $db->prepare($sql_count);
	// $result->execute();
	// $count_author = $result->fetch(PDO::FETCH_OBJ);
	$count_author = $db->query($sql_count)->fetch();
	$array[$count] = array("id" => $count, "tag" => $row[0], "count_author" => $count_author[0]);
	$count++;
}


// 
// print_r($array);
// $sql_count = "SELECT COUNT(*) FROM ( SELECT DISTINCT author FROM `lyric` WHERE wId = array_wId[count1]))a";
// foreach( $db->query( $sql ) as $row )
// {

// 	$count2++;
// }

// $sql_wId = substr($sql_wId,0,strlen($sql_wId)-1);

// $sql_count = "SELECT COUNT(*) FROM ( SELECT DISTINCT author FROM `lyric` WHERE wId IN (".$sql_wId."))a";
	
// 		$sql_wId .= $row[1].",";

// 	echo ($sql_count);

	// echo json_encode($array);




// $time_end = microtime(true);

// $time_res = $time_end - $time_start;

// echo "the query took $time_res seconds";


?>
