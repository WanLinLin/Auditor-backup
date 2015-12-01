<?php
header("Content-Type:text/html; charset=utf-8");

$input = "";
if(isset($_POST['sentence'])) {
	$input = $_POST['sentence'];
}

$UTF8sentence = unicode2utf8($input);
$BIG5sentence = iconv("UTF-8", "big5", $UTF8sentence);
// $sentence = iconv("UTF-8", "big5", $input);

$command = "directCut.py \"$BIG5sentence\"";
// echo $command. '<br/>';
$output = shell_exec($command);

echo $output;

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