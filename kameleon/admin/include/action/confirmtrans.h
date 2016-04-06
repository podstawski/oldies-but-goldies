<?
	$action="";

	$query="SELECT * FROM webtrans WHERE wt_server=$server AND wt_lang='$_lang' AND wt_verification>0 AND wt_parent IS NULL";
	$res=$adodb->Execute($query);
	$trans_count=$res->RecordCount();
	for ($i=0;$i<$trans_count;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$txt=addslashes(stripslashes($wt_t_html));

		$sql="UPDATE $wt_table SET $wt_table_field='$txt' WHERE sid=$wt_table_sid";
		$adodb->execute($sql);
	}
?>