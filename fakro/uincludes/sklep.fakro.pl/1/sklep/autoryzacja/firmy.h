<?php
	include ("$SKLEP_INCLUDE_PATH/autoryzacja/firmy_fields.h");
	include ("$SKLEP_INCLUDE_PATH/js.h");

	$DELETE_ICON = "<img src=\"$SKLEP_IMAGES/del.gif\" border=0 alt=\"".$WM->_sysmsg("Delete","system")."\">";
	
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
	$WHERE="su_parent IS NULL";

	if ($su&32)
	{
		echo "<div align=\"right\">";
		include("$SKLEP_INCLUDE_PATH/autoryzacja/kontrahent_wyszuk.h");
		echo "</div>";

		$sz="";
		if (strlen($LIST[miasto]))
			$sz.= "su_miasto ~* '".addslashes(stripslashes($LIST[miasto]))."'";
		if (strlen($LIST[nazwa]))
		{
			$LIST[nazwa] = addslashes(stripslashes($LIST[nazwa]));
			if (strlen($sz)) $sz.=" OR ";
			$sz.= "su_login ~* '".$LIST[nazwa]."' OR su_nazwisko ~* '".$LIST[nazwa]."'";
		}

		$od=unixdate($_REQUEST[k_data_od],0);
		$do=unixdate($_REQUEST[k_data_do],1)-1;
		
		$WHERE .=" AND su_data_dodania >= $od AND su_data_dodania <= $do";
		if (strlen($sz))
			$WHERE.=" AND($sz)";
	}

	if ($su&64 && !strlen($szukaj)) return;

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
	
	
	$add="<input type=\"button\" class=\"addbut\" value=\"".sysmsg("Add company","admin")."\" onclick=\"location.href='$next${next_char}oddzial_id=$oddzial_id'\">";
	if ($su&1) $navi="<br>$add<br>$navi<br>";
	echo $navi;
?>


<table id="organizacje" class="list_table">
<tr>
	<?
		echo "<th class=\"c1\" sort=\"$of[1]\">Lp</th>\n";
		for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
		{
			$p=pow(2,$ofi);
			if (!($p&$suf)) continue;
			$of=$osoby_fields[$ofi];
			echo "<th sort=\"$of[1]\">$of[0]\n";
		}
		if ($su&8) echo "<th>Przedstawiciel";

		if ($sg<0) echo "<th>Rola";
	?>
	<th class="co">Akcje
<?
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));

		$buttons = "<img src=\"$SKLEP_IMAGES/i_zobacz.gif\" alt=\"Zobacz\" style=\"cursor:hand\" onClick=\"show_item('$su_id');\">&nbsp;";

		if ($nextpage!=$self) $buttons = "<a href=\"$nextpage${next_char}sc[kontrahent_id]=$su_id&".sort_navi_qs($LIST)."\"><img src=\"$SKLEP_IMAGES/i_lista.gif\" alt=\"Zamówienia\" border=0></a>&nbsp;";

		if ($su&2) $buttons.="<img src=\"$SKLEP_IMAGES/i_delete.gif\" style=\"cursor:hand\" onClick=\"list_delete_item('FirmaUsun','$su_id:$sg:$sg_coid')\">";

		if (file_exists("$SOAP_PATH/WS_KartotekaKontrahenta.h")) 
		{
			$buttons.=" <a href='$self${next_char}$qs&list[su_id]=$su_id&action=WS_KartotekaKontrahenta'>
				<img src=\"$SKLEP_IMAGES/i_ws.gif\" border=\"0\" style=\"cursor:hand\"></a>";
		}


		$tr="";
		$tr.="	<td class=\"c1\">".($i+1)."\n";
		for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
		{
			$p=pow(2,$ofi);
			if (! ($p&$suf)) continue;
			$of=$osoby_fields[$ofi];
			eval("\$val=\$$of[1];");
			
			$al = "";
			if ($of[1]=="su_saldo" && file_exists("$SOAP_PATH/WS_SaldoKontrahenta.h"))
			{
				$val="<a href='$self${next_char}$qs&list[su_id]=$su_id&action=WS_SaldoKontrahenta&".sort_navi_qs($LIST)."'>".u_Cena($val)."</a>";
				$al = "align=\"right\"";
			}
			elseif($of[1]=="su_saldo")
			{
				$al = "align=\"right\"";
				$val = u_Cena($val);
			}


			$tr.="	<td $al>".stripslashes($val)."\n";
		}

		if ($_OSOBY[$su_id]) continue;
		$_OSOBY[$su_id]=1;


		echo "<tr dbid=\"$su_id\">
			$tr 
			<td class=\"co\">".$buttons;

	}
?>

</table>


<?
	echo $navi;
	include("$SKLEP_INCLUDE_PATH/list.h");
?>

<script>

	list_table_init('organizacje','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);
	
	function show_item(seldId)
	{
		table=getObject('organizacje');
		table.selectedId = seldId;
		
		document.cookie='ciacho[admin_su_id]='+table.selectedId;
		kartoteka_popup('<? echo $more?>','kontrahent');
	}	

	function show_selected_item()
	{
		table=document.all['organizacje'];
		if (!table.selectedId) return;

		document.cookie='ciacho[admin_su_id]='+table.selectedId;
		kartoteka_popup('<? echo $more?>','kontrahent');
	}

	function list_selected_item()
	{		
	}

</script>
