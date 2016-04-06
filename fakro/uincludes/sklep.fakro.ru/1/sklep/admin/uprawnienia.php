<?
	global $oddzial_id;

	if (!strlen($LIST[id])) return;

	$_grupy = array();

	$sql = "SELECT sag_grupa_id FROM system_acl_grupa WHERE sag_user_id = ".$LIST[id];
	$res = $projdb->execute($sql);
	for ($g=0; $g < $res->recordCount(); $g++)
	{
		parse_str(ado_explodename($res,$g));
		$_grupy[] = $sag_grupa_id;
	}
	
	if ($AUTH[p_admin] || $KAMELEON_MODE)
	{
		echo "
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"".$LIST[id]."\">
		<INPUT TYPE=\"hidden\" name=\"list[id]\" value=\"".$LIST[id]."\">
		<INPUT TYPE=\"hidden\" name=\"oddzial_id\" value=\"$oddzial_id\">
		<INPUT TYPE=\"hidden\" name=\"action\" value=\"OsobaUprawnienia\">

		<table class=\"sys_table\" width=\"100%\">
		<col align=\"right\">
		<tbody>
		<tr><td>".sysmsg("Rights","admin")."</td><td>";
		$query="SELECT * FROM system_grupa WHERE sg_server=$SERVER_ID ORDER BY sg_nazwa";
		$result = $projdb->Execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($result,$i));
		
			$ch=(in_array($sg_id,$_grupy))?"checked":"";
			echo "<input $ch type=\"checkbox\" name=\"upraw[$sg_id]\" value=\"1\"> $sg_nazwa<br>";

		}
		echo "</td></tr>
		</tbody>
		<tfoot>
		<tr><td colspan=\"2\">
			<INPUT TYPE=\"submit\" class=\"sys_button\" value=\"".sysmsg("Save","admin")."\"></td></tr>
		</tfoot>
		</table>
		</FORM>
		";
	}
	else echo "Niestety, nie posiadasz prawa do zarzБdzania tБ zakГadkБ";

?>
