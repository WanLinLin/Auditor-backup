<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<title>FeedBack Act</title>
</head>

<body
>
<?php
if(isset($_POST['lyric'])) { $lyrics = $_POST['lyric']; }
else { exit("no input lyric!"); }

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
// echo "output encodeing: ".mb_detect_encoding($output)."<br />";
// echo mb_convert_encoding($output, "UTF-8", "ASCII")."<br />";
echo $output;

?>
</body>
</html>