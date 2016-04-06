<?php
	include ("$SKLEP_INCLUDE_PATH/autoryzacja/osoby_fields.h");
	include ("$SKLEP_INCLUDE_PATH/js.h");

	$DELETE_ICON = "<img src=\"$SKLEP_IMAGES/del.gif\" border=0 alt=\"".$WM->_sysmsg("Delete","system")."\">";
	

	global $oddzial_id;
	if (!strlen($oddzial_id)) return;

	$sg=0;
	$sj=0;
	parse_str($costxt);
	


	$query="SELECT *,oid AS sg_oid FROM system_grupa WHERE sg_id=$sg";
	if (strlen($sg))
		parse_str(ado_query2url($query));
	$sg_coid=crypt($sg_oid);


	if (!strlen($LIST[sort_f]) || substr($LIST[sort_f],0,3)!="su_" ) $LIST[sort_f]="su_nazwisko";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROM="system_user";
	$WHERE="su_parent = $oddzial_id";

	if ($su&32)
	{
		include("$SKLEP_INCLUDE_PATH/wyszukiwarka.h");
		if (strlen($szukaj)) 
		{
			$sz="";
			for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
			{
				$p=pow(2,$ofi);
				if (!($p&$suf)) continue;
				$of=$osoby_fields[$ofi];
				if ("su_pass"==$of[1]) continue;

				if (strlen($sz)) $sz.=" OR ";
				$sz.="$of[1] ~* '$szukaj'";
				
			}

			$WHERE .=" AND ($sz)";
		}
	}

	//echo $WHERE;

	if ($su&64 && !strlen($szukaj)) return;

/*
	if ($sj && $su&16)
	{
		if (!$CIACHO[organizacja]) return;
		$WHERE.=" AND uo_organizacja_id=".$CIACHO[organizacja];

		$query="SELECT * FROM baza_organizacji WHERE bo_id=".$CIACHO[organizacja];
		parse_str(ado_query2url($query));
		if ($bo_przedstawiciel_id)
		{
			$query="SELECT * FROM system_user WHERE su_id=$bo_przedstawiciel_id";
			parse_str(ado_query2url($query));
		}
		else
		{
			$su_imiona="";
			$su_nazwisko="";
		}
		include("$SKLEP_INCLUDE_PATH/raport/template.h");
	}

*/

	$FROMWHERE="FROM $FROM WHERE $WHERE";

	//echo $FROMWHERE;

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	$navi=$size?navi($self,$LIST,$size):"";
	
	$query="SELECT * $FROMWHERE ORDER BY $sort";
//	$projdb->debug=1;
	if (strlen($navi))
		$result = $projdb->SelectLimit($query,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($query);
//	$projdb->debug=0;
	
	
	if ($su&1) $navi="<br><a href=\"$more${next_char}oddzial_id=$oddzial_id\">".sysmsg('Add person','system').": </a><br>$navi<br>";
	echo $navi;
?>

<table id="organizacje" class="list_table" width="100%">
<col class="c1">
<tr>
	<?
		echo "<th sort=\"$of[1]\">".sysmsg('No','order')."</th>\n";
		for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
		{
			$p=pow(2,$ofi);
			if (!($p&$suf)) continue;
			$of=$osoby_fields[$ofi];
			echo "<th sort=\"$of[1]\">$of[0]</th>\n";
		}
		if ($su&8) echo "<th>Przedstawiciel</th>";

		if ($sg<0) echo "<th>Rola</th>";
	?>
	<th>&nbsp;</th>
</tr>


<?
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));

		if ($su&2) $usun="<a href=\"javascript:list_delete_item('OsobaUsun','$su_id:$sg:$sg_coid')\">$DELETE_ICON</a>";
		$tr="";
		$tr.="	<td>".($i+1)."</td>\n";
		for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
		{
			$p=pow(2,$ofi);
			if (! ($p&$suf)) continue;
			$of=$osoby_fields[$ofi];
			eval("\$val=\$$of[1];");
			$tr.="	<td>$val</td>\n";
		}

		if ($_OSOBY[$su_id]) continue;
		$_OSOBY[$su_id]=1;

		$przedstawiciel="";
/*
		$rola="";
		if ($sg<0) 
		{
			$query="SELECT sg_nazwa FROM system_grupa WHERE sg_id=$sag_grupa_id";
			parse_str(ado_query2url($query));
			$rola.="	<td>$sg_nazwa</td>\n";
		}
*/

		echo "<tr dbid=\"$su_id\" style=\"cursor:hand\">
			$tr $przedstawiciel $rola
			<td>$usun</td>	
		      </tr>";

	}
	
?>
</table>


<?
	echo $navi;
	include("$SKLEP_INCLUDE_PATH/list.h");
?>

<script>

	list_table_init('organizacje','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_selected_item()
	{
		table=document.all['organizacje'];
		if (!table.selectedId) return;
		<? if ($su&4) {?>
		document.list_fwd_form.s_id.value=table.selectedId;
		document.list_fwd_form.submit();
		<? } ?>
		
	}

	function list_selected_item()
	{		
	}

</script>

