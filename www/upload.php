<?php

if(isset($_POST['id'])) { 
	$id = $_POST['id'];
} else { exit("no input id"); }


if(isset($_POST['score_name'])){
	$score_name = $_POST['score_name'];
} else { exit("no input score name"); }

if(isset($_POST['lyric'])){
	$lyric = $_POST['lyric'];
} else { exit("no lyric"); }

$dsn = "mysql:dbname=auditor_user;host=localhost";
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));

$sql = "INSERT INTO `auditor_user`.`user` VALUES (DEFAULT,'$id','$score_name','$lyric',DEFAULT) ON DUPLICATE KEY UPDATE content = '$lyric'";

$db->prepare($sql)->execute();	

?>