<?
	$page=0;
	$lang='p';
	$ver=1;
	$SERVER_ID=0;

	$images_ver=$ver;
	$uimages_ver=$ver;

	for ($i=1;$i<$argc;$i++) parse_str($argv[$i]);
	
	$IMAGES="images/$images_ver";
	$UIMAGES="uimages/$SERVER_ID/$uimages_ver";
	$mybasename="index";
	chdir("..");

	include ("index.php");
?>
