<?
	$query="SELECT su_blokady FROM system_user WHERE su_id=$LIST[id]";
	parse_str(ado_query2url($query));

	$query="SELECT sa_action FROM system_action 
			GROUP BY sa_action";
	$res=$projdb->Execute($query);

	$table="";

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));


		$ch=(strstr($su_blokady,":$sa_action:")) ? "checked":"";

		$action_name=sysmsg($sa_action,"action");
		
		if ($action_name==$sa_action && !strlen($ch)) continue;

		$table.="<tr>
					<td>$action_name</td>
					<td align=\"right\">
						<input type=\"checkbox\" name=\"form[$sa_action]\" value=1 $ch>
					</td>
				</tr>";

	}
	
?>
<form method="post" action="<?echo $self?>">
<input type="hidden" name="list[id]" value="<?echo $LIST[id]?>">
<input type="hidden" name="action" value="ZapiszBlokady">
<table> 
<?
	echo $table;
?>

<tr><td colspan=2 ><input type="submit" class="but" value="Zapisz"></td></tr>
</table>
</form>
