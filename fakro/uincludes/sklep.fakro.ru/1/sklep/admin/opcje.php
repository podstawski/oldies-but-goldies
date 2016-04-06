<form method="post" action="<?echo $self?>">
<input type="hidden" name="action" value="SystemOpcjeZmiana">
<table class="list_table" cellspacing="0">
<tbody>
<?

	$query="SELECT * FROM system_opcje ORDER BY so_nazwa2";
	$result = $projdb->Execute($query);

	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));

		

		$wart="";
		$_lista=explode(",",$so_lista);

		if (is_array($_lista) && count($_lista)>1)
		{
			$wart="<select name=\"form[$so_id]\" style=\"width:131px\">
					<option value=\"\">".sysmsg('Choose','system')."</option>";

			foreach( $_lista AS $v)
			{
				$sel=("$v" == "$so_wart")?"selected":"";
				$wart.="<option $sel value=\"$v\">".stripslashes($v)."</option>";
			}
			

			$wart.="</select>";
		}


		if ($so_lista=="0,1")
		{
			$checked=($so_wart==1)?"checked":"";
			$wart="<input type=checkbox $checked name=\"form[$so_id]\" value=1>";
		}

		if (substr($so_lista,0,6)=="SELECT")
		{
			$wart="<select name=\"form[$so_id]\" style=\"width:300px\">
					<option value=\"\">Wybierz</option>";

			$so_res=$projdb->Execute($so_lista);
			if ($so_res) for ($l=0;$l<$so_res->RecordCount();$l++)
			{
				parse_str(ado_explodeName($so_res,$l));
				if (!strlen($option)) $opt=$value;
				
				$sel=("$value" == "$so_wart")?"selected":"";
				$wart.="<option $sel value=\"$value\">".stripslashes($option)."</option>";

			}

			$wart.="</select>";
		}


		if (!strlen($wart))
		{
			$wart="<input size=20 name=\"form[$so_id]\" value=\"$so_wart\">";
		}
		
		$label=sysmsg("option_$so_nazwa2",'options');
		if ($label!="option_$so_nazwa2") $so_nazwa=$label;

		echo "<Tr class=\"bg".($i%2)."\">
				<td>$so_nazwa [$so_nazwa2]
				<td>$wart";

	}

?>
</tbody>
<tfoot>
<tr><td colspan="2"><input type=submit value="Zapisz" class=button>
<tfoot>
</table>

</form>
