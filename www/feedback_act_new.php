<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>FeedBack Act</title>
</head>

<body
>
<?php

if(isset($_POST['cId'])) { $cId = $_POST['cId']; }
else { exit("no input cId"); }
$sql_cId = "";
$count = 0;
for($i = count($cId)-1; $i >= 0; $i--)
{
    $sql_cId = $sql_cId.$cId[$i].",";
}
$sql_cId = substr($sql_cId,0,strlen($sql_cId)-1);

$sql = "SELECT content FROM `user` WHERE cId in ($sql_cId)";
$dsn = "mysql:dbname=auditor_user;host=localhost";	
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
echo"$sql";
foreach( $db->query( $sql ) as $row )
{
    $lyrics[$count] = $row[0];
    $count++;

}

print_r ($lyrics);
$biglyrics = implode ("", $lyrics);
$BIG5_lyrics = iconv("UTF-8","big5",$biglyrics);
echo "<br/>";
echo "$biglyrics";
echo "<br />";
echo "exec command: ";
echo "<br />";
echo 'python lyricUpdate.py "'.$BIG5_lyrics.'"';
echo "<br />";
echo "<br />";

echo "output:<br />";
$unicode_string = utf8_encode($biglyrics);

// putenv('LANG=en_US.UTF-8');
$output = shell_exec("python lyricUpdate.py $BIG5_lyrics");
$sql_update = "UPDATE `auditor_user`.`user` SET value = 1 where cId in ($sql_cId)";
$db->query($sql_update)->fetch();
// echo "output encodeing: ".mb_detect_encoding($output)."<br />";
// echo mb_convert_encoding($output, "UTF-8", "ASCII")."<br />";
echo $output;

?>
</body>
</html>