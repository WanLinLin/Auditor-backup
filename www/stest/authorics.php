<?php
header("Content-Type:text/html; charset=utf-8");

$text = "";
if(isset($_POST['text'])) { $text = $_POST['text']; }
else { exit("no input text!"); }

$count = 0;

$sql = "SELECT author,lyrics FROM `lyric` join `word` using(wId) WHERE text = '$text'";

$dsn = "mysql:dbname=auditor;host=localhost";
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
foreach( $db->query( $sql ) as $row )
{
	$array[$count] = array("author" => $row[0], "lyrics" => $row[1]);
	$count++;
}
echo json_encode($array);

?>