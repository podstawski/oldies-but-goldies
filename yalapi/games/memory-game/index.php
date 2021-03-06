<?php

    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : false;
    $quizId = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : false;

    if ($userId === false || $quizId === false) {
        die("Invalid params passed");
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Memory Game</title>
		<script type="text/javascript" src="js/swfobject.js"></script>
		<script type="text/javascript" src="js/swffit.js"></script>
		<script type="text/javascript">
			var vars = {
				user_id: <?php echo $userId ?>,
				quiz_id: <?php echo $quizId ?>
			};
			swfobject.embedSWF("memory01.swf", "game", "1280", "674", "10.0.2", null, vars);
		</script>
		<style type="text/css">
			body{
				background-color: #F99501;
			}
		</style>
	</head>
	<body>
		<div id="game">
			<a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" title="Get Adobe Flash player" /></a><br />
			You need <a href="http://www.adobe.com/go/getflashplayer">Flash Player 8</a> and allow javascript to see the content of this site..
		</div>
	</body>
</html>