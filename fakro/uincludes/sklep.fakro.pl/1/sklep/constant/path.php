<table cellspacing=0 cellpadding=0 width="100%">
<tr>
	<td><? include("$SKLEP_INCLUDE_PATH/path.php") ?></td>
	<td style="text-align:right"><?
			if ($KAMELEON_MODE) include("$SKLEP_INCLUDE_PATH/constant/szpalta_opisowa.php");
			else echo "<? include(\"\$SKLEP_INCLUDE_PATH/constant/szpalta_opisowa.php\");?>";
	?></td>
</tr>
</table>
