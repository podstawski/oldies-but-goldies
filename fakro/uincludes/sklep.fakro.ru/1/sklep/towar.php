<?
	include ($SKLEP_INCLUDE_PATH."/opis.php");
	$query="SELECT ka_id FROM kategorie WHERE ka_kod='$page'";
	parse_str(ado_query2url($query));
	if (!$ka_id) return;


	global $WYMIARY,$KALKULATOR, $FILTR;

	if (is_array($FILTR))
	{
		$add_sql = "";
		while(list($key,$val) = each($FILTR))
			if (strlen($val))
			{
				$pola = explode(",",$key);
				$wartosci = explode("x",$val);
				for ($i=0; $i<count($pola);$i++)
				{
					if ($pola[$i] != "tp_gatunek" && $pola[$i] != "tp_stan")
						$add_sql.= "AND ".$pola[$i]." = ".$wartosci[$i]." ";
					else
						$add_sql.= "AND ".$pola[$i]." = '".$wartosci[$i]."' ";
				}
			}
	}
	$index=sysmsg("th_index","system");
	$table="<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>";
	$table.="\n<tr>
			<th>$index</th>";

	if (is_array($WYMIARY)) foreach ($WYMIARY AS $w)
	{
		$wymiar=sysmsg("th_$w","system");
		$title=sysmsg("title_$w","system");
		$table.="\n<th title=\"$title\">$wymiar</th>";
	}
	else
	{
		$nazwa=sysmsg("th_name","system");
		$table.="\n<th>$nazwa</th>";
	}
	$options=sysmsg("th_options","system");
	$table.="\n<th>$options</th>\n</tr>";


	if (is_array($WYMIARY)) $order="ORDER BY ".implode(",",$WYMIARY);
	else $order="ORDER BY to_indeks";


	$query="SELECT * FROM towar
			LEFT JOIN towar_parametry ON tp_to_id = to_id
			LEFT JOIN towar_sklep ON ts_to_id = to_id AND ts_sk_id=$SKLEP_ID
			,towar_kategoria 
			WHERE tk_ka_id=$ka_id  
			AND tk_to_id=to_id AND ts_sk_id=$SKLEP_ID $add_sql
			$order";
	$result=$projdb->Execute($query);


	for($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_Explodename($result,$i));
		
		
		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";

		$table.="<tr $_tr>
			<td class=\"c1\" title=\"$to_nazwa\">$to_indeks</td>";

		if (is_array($WYMIARY)) foreach ($WYMIARY AS $w)
		{
			eval("\$wymiar=\$$w ;");
			if (!strlen($wymiar)) $wymiar="&nbsp;";
			$table.="\n<td class=\"c2\">$wymiar</td>";
		}
		else $table.="\n<td class=\"c2\">$to_nazwa</td>";

		$options="";
		if ($KALKULATOR) $options.="<a href='javascript:kalkulator($to_id)'>
				<img src=\"$SKLEP_IMAGES/i_kalk.gif\" width=19 height=14 hspace=0 vspace=0 border=0></a>";
		
		$link = $next_char."list[to_id]=$to_id";
			
		if ($AUTH[id]>0)
			$options.="<img src=\"$UIMAGES/sb/i_koszyk.gif\" width=19 height=16 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Add article to offer cart","system")."\" onClick=\"addItem2Cart('$to_id',".$WM->kwant_towaru($to_id).")\" style=\"cursor:hand\">";
		else
			$options.="<img src=\"$SKLEP_IMAGES/i_info.gif\" width=11 height=11 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Add article to offer cart","system")."\" onClick=\"chageItemQuantity('$to_id',".$WM->kwant_towaru($to_id).",".$WM->kwant_towaru($to_id).",0)\" style=\"cursor:hand\">";
		$options.="<img src=\"$UIMAGES/autoryzacja/ikona_szukaj_b.gif\" width=12 height=12 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Show picture","system")."\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">";
		$table.="
			<td class=\"c4\" nowrap>$options</td>";
				

			
		$table.="\n</tr>";

	}
	$table.="</table>
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>	
//	<script id=\"qscript\" src=\"\"></script>
	<SCRIPT id=\"qscript\" src=\"\" type=\"text/javascript\" LANGUAGE=\"JavaScript\"></SCRIPT>
	<FORM METHOD=POST ACTION=\"$self\" name=\"cartForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KoszykDodaj\">
	<INPUT TYPE=\"hidden\" id=\"towar_id\" name=\"list[towar_id]\">
	<INPUT TYPE=\"hidden\" id=\"quantity\" name=\"list[quantity]\">
	</FORM>
	";
	

	if ($result->RecordCount()) echo $table

?>
<script>

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
	function chageItemQuantity(id,quant,kwant,iscalc)
	{
		if (quant == 0) quant = 1;
		if (!iscalc) quant = prompt('<? echo sysmsg("Quantity","cart") ?>',quant);
		if (quant == null) return;
		quant = quant.replace(",",".");

		if (isNaN(quant))
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}
//		qobj = getObject('qscript');
//		qobj.src = url('<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+id+'&tquant='+quant+'&tadd=1&kwant='+kwant+'&randSID='+Math.random());		

		var file_path = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+id+'&tquant='+quant+'&tadd=1&kwant='+kwant+'&randSID='+Math.random();
		
		loadContent(file_path,'qscript');

	}

	function calcAddToCart(id,val)
	{
		ilosc = val;
		ilosc = ilosc.replace(",",".");
		if (isNaN(ilosc))
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}
		document.cartForm.towar_id.value = id;		
		document.cartForm.quantity.value = ilosc;
		document.cartForm.submit();
	}

</script>
