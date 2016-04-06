<html>

<head>
    <title><?echo label("SVN Confirm");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
</head>
<body bgcolor="#c0c0c0" topmargin=5 leftmargin=5 marginwidth=5 marginheight=5>

<?
	echo $SVN_LOG;
?>


<form method=post name=link name="svn">
<input type=hidden name=action value="SvnEnd">
<input type=hidden name=what value="<?echo $what?>">
<input type=hidden name=page value="<?echo $page?>">

<?
	include_once("include/svnfun.h");

	switch ($what)
	{
		case 'inc':
			eval("\$dir=\"$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN\";");	
			eval("\$dest=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
			break;
	}

	$files=getFileTree($dir);
	if (!count($files))
	{
		echo label("No files to update");
	}


?>


<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="2" class="k_text">
<?
	foreach ($files AS $file)
	{
		$ser_bgcolor="bgcolor=\"#D0D0D0\"";
		if ((($i++)&1)==0) $ser_bgcolor="bgcolor=\"#E0E0E0\"";

		$thesame=fcmp("$dir/$file","$dest/$file");
		
		
		$tit=$thesame ? label("File was not edited"):label("Modification").": ".date("d-m-Y, H:i:s",filemtime ("$dir/$file"));
		$dis=$thesame ? "disabled":"";
		$hid=$thesame ? "<input type=\"hidden\" name=\"svn[$file]\" value=1>":"";

		echo " $hid<tr $dis $ser_bgcolor > \n";
		echo " <td width=\"40px\" title=\"$tit\"><input type=\"checkbox\" class=\"k_checkbox\" name=\"svn[$file]\" value=1 checked ></td>";
		echo " <td title=\"$tit\">$file</td>";
		echo " </tr>\n";
	}

?>


</table>
<div style="text-align:left; padding:5px;">
<input type="submit" class="k_button" value=" SVN " >
</div>
</form>

</body>
</html>
