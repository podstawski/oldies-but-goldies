<?
	$action="";

	if (!$_ver || !strlen($_lang))
	{
		$error=label('Choose language and version');
		return;
	}

	$sql="DELETE FROM webpage WHERE server=$server AND lang='$_lang' AND ver=$_ver;
			DELETE FROM webtd WHERE server=$server AND lang='$_lang' AND ver=$_ver;
			DELETE FROM weblink WHERE server=$server AND lang='$_lang' AND ver=$_ver;
			";

	$adodb->execute($sql);

?>