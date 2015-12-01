<?php
$array = array();
$count = 0;
$score_name = array();

$dsn = "mysql:dbname=auditor_user;host=localhost";
$db = new PDO($dsn, 'root', 'mis105RAY',array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));

$sql = "SELECT * FROM `user`";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>回饋總覽</title>
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
	function toggle(source) {
		checkboxes = document.getElementsByName('lyric[]');
		for(var i = 0, n = checkboxes.length; i < n; i++) {
			checkboxes[i].checked = !checkboxes[i].checked;
		}
	}
</script>

<style type="text/css">
	.jumbotron {
		background-image: url('img/DSC_0221.JPG');
		background-size:     cover;
		background-repeat:   no-repeat;
		background-position: 30% 50%;
		height: 450px;
		color: #FFF;
	}

	body {
		font-family: "Roboto","微軟正黑體",sans-serif;
	}
</style>
</head>

<body>
	<div class="container">
		<div class="jumbotron">
			<h1>回饋總覽</h1>
			<p><h4 style="padding-left: 50px;"><i>Auditor&nbsp;-&nbsp;歌曲創作&nbsp;app for android</i></h4></p>
		</div>

		<form method="post" action="feedback_act.php" >
			<table class="table table-striped">
				<tr align="center" class="info">
					<td width="15%"><h4>uId</h4></th>
					<td width="15%"><h4>歌名</h4></td>
					<td width="65%"><h4>歌詞</h4></td>
					<td width="5%"><h4>確認</h4></td>
				</tr>

				<?php
					foreach( $db->query( $sql ) as $row )
					{
						// $row[0] = uId
						// $row[1] = 歌名
						// $row[2] = 歌詞
						echo '
						<tr>
							<td align="center">
							'.$row[1].' 
							</td>

							<td align="center">
							'.$row[2].'
							</td>

							<td>
							'.$row[3].'
							</td>

							<td align="center">
								<input type="checkbox" name="lyric[]" value="'.$row[3].'" />
							</td>
						</tr>';

						// $array[$count] = array("uId" => $row[0], "filename" => $row[1], "content" => $row[2]);
						$count++;
					}
				?>

				<tr>
					<td colspan="4" align="center" style="background-color: #FFF;">
						<input class="btn btn-primary" type="submit" name = "submit" id = "submit" value = "送入推薦詞庫" />
						<input class="btn btn-default" type="reset" name = "reset" id = "reset" value = "清除" />
						<button class="btn btn-default" type="button" onclick="toggle(this)">全選</button>	
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
