<?
	if ($_GET['close']) 
	{
		//echo '<script type="text/javascript">window.close();</script>';
		return;
	}

	if (is_array($_POST) && count($_POST))
	{
		$_ju_uploadRoot='../../../'.$adodb->GetCookie('jUploadDir');
		$_ju_fileDir='';
		
		//die('upload to: '.$_ju_uploadRoot);
		chdir('jupload/scripts/php');
		include('./jupload-post.php');
		return;
	}


	if (isset($_GET['dir'])) $adodb->SetCookie('jUploadDir',$_GET['dir']);
?>
<html>
<head>
	<title>JUpload</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
</head>

<body style="margin:0px;" onUnload="opener.location.href=opener.location.href">




<textarea style="display:none" id="ta_swf_applet">
<applet
		title="JUpload"
		name="JUpload"
		code="com.smartwerkz.jupload.classic.JUpload"
		codebase="."
		archive="jupload/dist/jupload.jar,
				jupload/dist/commons-codec-1.3.jar,
				jupload/dist/commons-httpclient-3.0-rc4.jar,
				jupload/dist/commons-logging.jar,
				jupload/dist/skinlf/skinlf-6.2.jar"
		width="640"
		height="480"
		mayscript="mayscript"
		alt="JUpload by www.jupload.biz">

	<param name="Config" value="jupload/cfg/jupload.kameleon.config.php?lang=<?=$lang?>">

	Your browser does not support Java Applets or you disabled Java Applets in your browser-options.
	To use this applet, please install the newest version of Sun's Java Runtime Environment (JRE).
	You can get it from <a href="http://www.java.com/">java.com</a>

</applet>

</textarea>

<script type="text/javascript">
var sid='applet';
</script>
<script type="text/javascript" src="remote/swf.js">
</script>

</body>
</html>	