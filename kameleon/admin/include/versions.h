<?

	echo "
	<div style=\"width: 500px; margin: 60px auto 60px auto; border: 1px solid #d3d3d3; \">	
	  <div class=\"secname\">Opis wersji</div>
	  <table class=\"tabelka\" cellpadding=\"0\" cellspacing=\"0\">\n";

	$query="SELECT * FROM kameleon
		  ORDER BY version DESC";

	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$ver_bgcolor="class=\"line_0\"";
		if (($i&1)==0) $ver_bgcolor="class=\"line_1\"";
		echo "<tr $ver_bgcolor>\n";

		echo "<td valign=top nowrap>";
		echo "<b>$version</b>";
		echo "<br><i>".FormatujDate($nd_issue)."</i>";
		echo "</td>\n";

		echo "<td valign=top>";
		$opis=stripslashes($opis);
		$opis=ereg_replace("\n\n","\n<hr size=1>",$opis);

		echo nl2br($opis);
		echo "</td>\n";

		echo "</tr>\n";

	}
	echo "</table></div>\n";

?>
