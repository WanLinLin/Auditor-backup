<?php
$json_array = array();

if(isset($_POST['id'])) { 
	$json_array[0] = array("id" => $_POST['id']);
}

if(isset($_POST['score_name'])){
	$json_array[1] = array("score_name" => $_POST['score_name']);
}

if(isset($_POST['lyric'])){
	$json_array[2] = array("lyric" => $_POST['lyric']);
}

echo json_encode($json_array);
?>