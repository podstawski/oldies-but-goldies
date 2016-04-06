<?
	include_once('include/search.h');

	$sql="SELECT sid,plain,title FROM webtd";

	$rs=$adodb->Execute($sql);

	for ($wtd=0;$wtd<$rs->RecordCount();$wtd++)
	{
		parse_str(ado_explodeName($rs,$wtd));

		$nohtml=polishtolower(wordsFromHtml(stripslashes("$title $plain")));

		$sql="UPDATE webtd SET nohtml='$nohtml' WHERE sid=$sid";
		$adodb->execute($sql);
	}

?>