<?
	$KATS = $SKLEP_SESSION[kategorie];
	include ($SKLEP_INCLUDE_PATH."/opis.php");
	if (!strlen($LIST[sort_f])) $LIST[sort_f]="to_indeks";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$qs = $LIST[szukaj];

	if (!strlen(trim($qs))) 
	{
		$qs = $CIACHO[szukaj];
		$LIST[szukaj] = $qs;
	}
	$qs = trim($qs);
	if (!strlen($qs)) return;
	$qs = ereg_replace("  ","",$qs);
//	$qs = ereg_replace("+ ","+",$qs);
	$qs = strtolower($qs);

	function checkForPolish($str)
	{
		if (strpos(" ".$str,"¶")) return true;
		if (strpos(" ".$str,"±")) return true;
		if (strpos(" ".$str,"¼")) return true;
		if (strpos(" ".$str,"¦")) return true;
		if (strpos(" ".$str,"¡")) return true;
		if (strpos(" ".$str,"¬")) return true;
		if (strpos(" ".$str,"ê")) return true;
		if (strpos(" ".$str,"Ê")) return true;
		if (strpos(" ".$str,"ñ")) return true;
		if (strpos(" ".$str,"Ñ")) return true;
		if (strpos(" ".$str,"³")) return true;
		if (strpos(" ".$str,"£")) return true;
		if (strpos(" ".$str,"æ")) return true;
		if (strpos(" ".$str,"Æ")) return true;
		if (strpos(" ".$str,"¿")) return true;
		if (strpos(" ".$str,"¯")) return true;
		return false;
	}

	echo "
	<script>
		document.cookie = 'ciacho[szukaj]=$qs';
	</script>
	";

	$slowa = explode(" ",$qs);
	$_slowa = $slowa;
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
			$add_stage = " OR to_nazwa ~* '".strtoupper($slowa[$i])."' 
							OR to_indeks ~* '".strtoupper($slowa[$i])."'
							OR to_klucze ~* '".strtoupper($slowa[$i])."'";

		$add_sql.= $operator." (to_nazwa ~* '".$slowa[$i]."' OR to_indeks ~* '".$slowa[$i]."' OR to_klucze ~* '".$slowa[$i]."' $add_stage) ";
	}
	if (count($slowa)) $add_sql.= ")";

	$slowa = $_slowa;
	$add_kat = "";
	$first_pass = array();
	while (list($key,$val) = each($KATS))
	{
		for ($i=0; $i < count($slowa); $i++)
		{
			if (!strlen(trim($slowa[$i]))) continue;
			if (!strlen(trim($slowa[$i])) == "+") continue;
			if (substr($slowa[$i],0,1) == "+") continue;
			if (strpos($key,strtolower($slowa[$i])))
				$first_pass[$key] = $val;
		}
	}
	$last_pass = array();
	$any = 0;
	reset($first_pass);
	while (list($key,$val) = each($first_pass))
	{
		for ($i=0; $i < count($slowa); $i++)
		{
			if (!strlen(trim($slowa[$i]))) continue;
			if (!strlen(trim($slowa[$i])) == "+") continue;
			if (substr($slowa[$i],0,1) != "+") continue;
			$slowa[$i] = substr($slowa[$i],1);
			$any = 1;
			if (strpos($key,strtolower($slowa[$i])))
				$last_pass[$key] = $val;
		}
	}

	if (!$any) $last_pass = $first_pass;
	reset($last_pass);
	while (list($key,$val) = each($last_pass))
		echo "<img src=\"$SKLEP_IMAGES/arr_r.gif\">&nbsp;<A HREF=\"".$val[0]."\">".$val[1]."</A><br>";
	echo "<br>";
	
	$FROMWHERE = "FROM towar LEFT JOIN towar_parametry ON tp_to_id = to_id
				LEFT JOIN towar_sklep ON ts_to_id = to_id AND ts_sk_id=$SKLEP_ID
				WHERE ts_sk_id = $SKLEP_ID 
				AND ts_aktywny>0
				$add_sql";

	$sql = "SELECT towar.* $FROMWHERE ORDER BY ".$sort;

	//echo $sql;

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
	
	if (!is_object($result) || !$result->RecordCount()) 
	{
		if (!count($last_pass)) echo sysmsg("No matches","system");
		return;
	}

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
		$link = $next_char."list[to_id]=$to_id";
		if ($AUTH[id]>0)
			$options="<img src=\"$SKLEP_IMAGES/sb/i_koszyk.gif\" width=19 height=16 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Add article to offer cart","system")."\" onClick=\"addItem2Cart('$to_id',".$WM->kwant_towaru($to_id).",'".sysmsg("$to_jm","cart")."')\" style=\"cursor:hand\">";
		else
			$options="<img src=\"$SKLEP_IMAGES/sb/i_zamow.gif\" width=19 height=14 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Add article to offer cart","system")."\" onClick=\"chageItemQuantity('$to_id',".$WM->kwant_towaru($to_id).",".$WM->kwant_towaru($to_id).",'".sysmsg("$to_jm","cart")."')\" style=\"cursor:hand\">";
		$options.="<img src=\"$UIMAGES/system/lupa.gif\" width=12 height=12 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Show picture","system")."\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">";
		$table.="<tr dbid=\"$to_id\">
			<td>".($i+1+$LIST[start])."</th>
			<td>".stripslashes($to_indeks)."</th>
			<td>".stripslashes($to_nazwa)."</th>
			<td>$options</th>
		</tr>";
	}

	$table.="</table>$navi";

	
	$cart_next=$self;
	if ($SYSTEM[koszyk]) $cart_next=$KOSZYK_NEXT;

	echo $table."<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>	
<!-- 	<script id=\"qscript\" src=\"\"></script> -->
	<FORM METHOD=POST ACTION=\"$cart_next\" name=\"cartForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KoszykDodaj\">
	<INPUT TYPE=\"hidden\" id=\"towar_id\" name=\"form[towar_id]\">
	<INPUT TYPE=\"hidden\" id=\"quantity\" name=\"form[quantity]\">
	".sort_navi_options($LIST)."
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

		//document.list_fwd_form.s_id.value=table.selectedId;
		//document.list_fwd_form.submit();
		opisProduktu('<? echo $next_char ?>list[to_id]='+table.selectedId+'');
	}

	function list_selected_item()
	{		
	}

	function addItem2Cart(item,def,jm)
	{
		document.cartForm.towar_id.value = item;		
		if (def == 0) def = 1;
		ilosc = prompt('<? echo sysmsg("Quantity","cart") ?> ['+jm+']',def);
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
	function chageItemQuantity(id,quant,kwant,jm)
	{
		if (quant == 0) quant = 1;
		quant = prompt('<? echo sysmsg("Quantity","cart") ?> ['+jm+']',quant);
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
