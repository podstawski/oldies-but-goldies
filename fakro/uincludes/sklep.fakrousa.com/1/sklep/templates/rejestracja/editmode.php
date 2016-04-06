<?
	if ( "$FORM[sid]" == "$WEBTD->sid")
	{
		$pos=strpos($costxt,"&sg=");
		if ($pos) $costxt=substr($costxt,0,$pos);

		$sg=":";
		foreach (array_keys($FORM) AS $k)
		{
			if (substr($k,0,3)!="sg_") continue;
			$sg.=substr($k,3).":";
		}
		$costxt.="&sg=$sg";


		if (!strlen($FORM[confirm])) $FORM[confirm] = "0";
		$sql = "UPDATE webtd SET cos = ".$FORM[confirm].",costxt='$costxt' 
				WHERE sid = $WEBTD->sid";
		$kameleon_adodb->execute($sql);
		$cos = $FORM[confirm];
	}

	$grupy="";

	$query="SELECT sg_nazwa,sg_id FROM system_grupa WHERE sg_server=$SERVER_ID ORDER BY sg_nazwa";
	$result = pg_exec($query);
	for ($i=0;$i<pg_numrows($result);$i++)
	{
		parse_str(pg_ExplodeName($result,$i));

		$ch=strstr($sg,":$sg_id:")?"checked":"";

		$grupy.="<tr><td>Po rejestracji dopisz do: <b>$sg_nazwa</b></td><td><input type=checkbox value=1 name=\"form[sg_$sg_id]\" $ch></td></tr>";
	}

	if ($cos) $check = "checked";

	echo "
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"form[sid]\" value=\"$WEBTD->sid\">
	<TABLE>
	<TR>
		<TD>Wymagane potwierdzenie wprowadzonych danych ?</TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"form[confirm]\" value=1 $check></TD>
	</TR>
	$grupy
	<TR>
		<TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
	</TR>
	</TABLE>
	</FORM>";

?>
