<?
	$pm_id  = $CIACHO[admin_pm_id];

	$sql = "SELECT * FROM promocja WHERE pm_id = $pm_id";
	parse_str(ado_query2url($sql));

	$indeks="<b>$pm_symbol</b><br>";
?>
<table cellspacing=0 width="100%">
<tr>
	<td valign="top"><?echo $indeks." ($pm_id)" ?></td>
</tr>
</table>
