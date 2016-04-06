<?
	include ($SKLEP_INCLUDE_PATH."/opis.php");
	if (!strlen($LIST[sort_f])) $LIST[sort_f]="to_indeks";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$qs = $LIST[szukaj];
	/*
	if (!strlen(trim($qs)) || $qs=="znajdМ") 
	{
		$qs = $CIACHO[szukaj];
		$LIST[szukaj] = $qs;
	}
	*/
	
	if (!strlen($qs))
	{
		echo "brak";
		return;
	}

	echo "
	<script>
		//document.cookie = 'ciacho[szukaj]=$qs';
	</script>
	";

	$qs = trim($qs);
	if (!strlen($qs)) return;
	$qs = ereg_replace("  ","",$qs);
//	$qs = ereg_replace("\+ ","\+",$qs);
	$qs = strtolower($qs);

	function checkForPolish($str)
	{
		if (strpos(" ".$str,"Ж")) return true;
		if (strpos(" ".$str,"Б")) return true;
		if (strpos(" ".$str,"М")) return true;
		if (strpos(" ".$str,"І")) return true;
		if (strpos(" ".$str,"Ё")) return true;
		if (strpos(" ".$str,"Ќ")) return true;
		if (strpos(" ".$str,"ъ")) return true;
		if (strpos(" ".$str,"Ъ")) return true;
		if (strpos(" ".$str,"ё")) return true;
		if (strpos(" ".$str,"б")) return true;
		if (strpos(" ".$str,"Г")) return true;
		if (strpos(" ".$str,"Ѓ")) return true;
		if (strpos(" ".$str,"ц")) return true;
		if (strpos(" ".$str,"Ц")) return true;
		if (strpos(" ".$str,"П")) return true;
		if (strpos(" ".$str,"Џ")) return true;
		return false;
	}

	$slowa = explode(" ",$qs);
	if (count($slowa)) $add_sql = "AND (";
	for ($i=0; $i < count($slowa); $i++)
	{
		if (!strlen(trim($slowa[$i]))) continue;
		if (!strlen(trim($slowa[$i])) == "+") continue;
		if (substr($slowa[$i],0,1) == "+")
		{			
			$operator = "AND";
			$slowa[$i] = substr($slowa[$i],1);
		}
		else
			$operator = "OR";
 
		if (!strlen(trim($slowa[$i]))) continue;
		if (!$i) $operator = "";

		$add_stage = "";		
		if (checkForPolish($slowa[$i]))
			$add_stage = " OR to_nazwa ~* '".strtoupper($slowa[$i])."' OR to_indeks ~* '".strtoupper($slowa[$i])."'";

		$add_sql.= $operator." (to_nazwa ~* '".$slowa[$i]."' OR to_indeks ~* '".$slowa[$i]."' $add_stage) ";
	}
	if (count($slowa)) $add_sql.= ")";

	$FROMWHERE = "FROM towar LEFT JOIN towar_parametry ON tp_to_id = to_id
			WHERE 1=1 $add_sql";

	$sql = "SELECT towar.* $FROMWHERE ORDER BY ".$sort;

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}
	$navi=$size?navi($self,$LIST,$size):"";
		
	if (strlen($navi))
		$result = $projdb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($sql);	
	
	if (!is_object($result)) 
	{
		echo "UПyto nieprawidГowych znakѓw przy wyszukiwaniu !!!<br>"; 
		return;

	}

	if (!$result->RecordCount()) return;

	$n_wymiar=sysmsg("th_name","system");
	$i_wymiar=sysmsg("th_index","system");

	$table ="
	$navi
	<table id=\"wydruk\" cellspacing=0 cellpadding=0 class=\"list_table\">
	<tr>
		<th>".sysmsg("Lp.","system")."</th>
		<th sort=\"to_indeks\">$i_wymiar</th>
		<th sort=\"to_nazwa\">$n_wymiar</th>
		<th>".sysmsg("th_options","system")."</th>
	</tr>";

	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));
		$options="
		<A HREF=\"$more${next_char}list[id]=$to_id&".sort_navi_qs($LIST)."\"><img src=\"$UIMAGES/autoryzacja/i_editmode_n.gif\" hspace=5 vspace=0 border=0 style=\"cursor:hand\" alt=\"edycja ogѓlnych parametrѓw towaru - nazwy, wymiarѓw, itp\"></A>
		<A HREF=\"$next${next_char}list[id]=$to_id&".sort_navi_qs($LIST)."\"><img src=\"$UIMAGES/autoryzacja/i_tree_n.gif\" hspace=5 vspace=0 border=0 style=\"cursor:hand\" alt=\"zmiana cen i innych parametrѓw zwiБzanych z handlem\"></A>
		";

		$table.="<tr dbid=\"$to_id\">
			<td>".($i+1+$LIST[start])."</th>
			<td>".stripslashes($to_indeks)."</th>
			<td>".stripslashes($to_nazwa)."</th>
			<td nowrap>$options</th>
		</tr>";
	}

	$table.="</table>$navi";
	echo $table."<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>	
	<script id=\"qscript\" src=\"\"></script>
	<FORM METHOD=POST ACTION=\"$self\" name=\"cartForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KoszykDodaj\">
	<INPUT TYPE=\"hidden\" id=\"towar_id\" name=\"form[towar_id]\">
	<INPUT TYPE=\"hidden\" id=\"quantity\" name=\"form[quantity]\">
	<INPUT TYPE=\"hidden\" name=\"list[ile]\" value=\"$LIST[ile]\">
	<INPUT TYPE=\"hidden\" name=\"list[sort_f]\" value=\"$LIST[sort_f]\">
	<INPUT TYPE=\"hidden\" name=\"list[sort_d]\" value=\"$LIST[sort_d]\">
	<INPUT TYPE=\"hidden\" name=\"list[start]\" value=\"$LIST[start]\">
	</FORM>";



	include("$SKLEP_INCLUDE_PATH/js.h");
	include("$SKLEP_INCLUDE_PATH/list.h");

?>
<script>
	list_table_init('wydruk','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_selected_item()
	{
		table=getObject('wydruk');
		if (!table.selectedId) return;

		document.list_fwd_form.s_id.value=table.selectedId;
		document.list_fwd_form.submit();
//		//opisProduktu('<? echo $next_char ?>list[to_id]='+table.selectedId+'');
	}

	function list_selected_item()
	{		
	}

	function addItem2Cart(item,def)
	{
		document.cartForm.towar_id.value = item;		
		if (def == 0) def = 1;
		ilosc = prompt('<? echo sysmsg("Quantity","cart") ?>',def);
		if (ilosc == null) return;
		ilosc = ilosc.replace(",",".");
		if (isNaN(ilosc))
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}
		document.cartForm.quantity.value = ilosc;
		document.cartForm.submit();
	}

	var art_add = '<? echo sysmsg("Article added to offer","cart") ?>';
	function chageItemQuantity(id,quant,kwant)
	{
		if (quant == 0) quant = 1;
		quant = prompt('<? echo sysmsg("Quantity","cart") ?>',quant);
		if (quant == null) return;
		quant = quant.replace(",",".");

		if (isNaN(quant))
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}

//		qobj = getObject('qscript');
//		qobj.src = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+id+'&tquant='+quant+'&tadd=1&kwant='+kwant+'&randSID='+Math.random();		

		var file_path = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+id+'&tquant='+quant+'&tadd=1&kwant='+kwant+'&randSID='+Math.random();
		loadContent(file_path,'qscript');

	}


</script>
