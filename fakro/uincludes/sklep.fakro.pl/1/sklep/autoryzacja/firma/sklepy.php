<?
//	$CIACHO[admin_su_id]

	$jakie_sklepy = array();
	
	if (!strlen($CIACHO[admin_su_id])) return;

	$sql = "SELECT ks_sk_id FROM kontrahent_sklep WHERE ks_su_id = ".$CIACHO[admin_su_id];
	$res = $projdb->execute($sql);
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$jakie_sklepy[] = $ks_sk_id;
	}


	echo "
	<FORM METHOD=POST ACTION=\"$self\">		
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"".$CIACHO[admin_su_id]."\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszSklep\">
	<table class=\"sys_table\" cellspacing=0 cellpadding=0 border=0  width=\"100%\">	
	<tr><th>Sklepy</th></tr>
	<tr><td>";

	$sql = "SELECT * FROM sklep ORDER BY sk_nazwa";
	$res = $projdb->execute($sql);
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$chck = "";
		if (in_array($sk_id,$jakie_sklepy)) $chck = "checked";
		echo "<INPUT TYPE=\"checkbox\" NAME=\"SKLEPY[$sk_id]\" value=\"1\" $chck> $sk_nazwa<br>";
	}


	echo "</td></tr>
	<tfoot>
	<tr><td>
		<INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"sys_button\">
	</td></tr>
	</tfoot>
	</table></FORM>";

?>
