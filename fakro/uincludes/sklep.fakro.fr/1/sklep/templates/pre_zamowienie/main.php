<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}
	$adodb->debug=0;
	/*********************************************************************************************/
	$sql = "SELECT * FROM poczta ORDER BY po_nazwa";
	$res = $adodb->execute($sql);
	$jscript = "poczta = new Array()\n";
	$poczta = "";
	$deliv_display = 'none';
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		$deliv_display = 'inline';
		parse_str(ado_explodename($res,$i));
		$jscript.= "poczta[$po_id]= new Array()\n";
		$jscript.= "poczta[$po_id]['n']= $po_cena_nt\n";
		$jscript.= "poczta[$po_id]['b']= $po_cena_br\n";
		$jscript.= "poczta[$po_id]['m']= $po_darmo_powyzej\n";

		$darmocha=$po_darmo_powyzej?', bezp³atnie powy¿ej '.u_cena($po_darmo_powyzej):'';
		$poczta.= "<option value=\"$po_id\">$po_nazwa, cena: ".u_cena($po_cena_br)."$darmocha</option>\n";
	}
	/*********************************************************************************************/

	$sql = "SELECT MAX(za_numer) AS order_number FROM zamowienia WHERE za_su_id = ".$AUTH[parent];
	parse_str(ado_query2url($sql));
	$order_number+=1;

	$sql = "SELECT count(*) AS order_number_all FROM zamowienia";
	parse_str(ado_query2url($sql));
	$order_number_all+=1;


	$sql = "SELECT su_adres1, su_adres2, su_adres3
		FROM system_user WHERE su_id = ".$AUTH[parent];
	parse_str(ado_query2url($sql));

	$adres_options="";
	$adres_display="";

	for ($i=1;$i<=3;$i++)
	{
		eval("\$adr=\$su_adres$i ;");
		$adr=stripslashes($adr);
		$a=addslashes($adr);
		if (strlen($adr)) $adres_options.="<option value=\"$a\">$adr</option>\n";
	}
	
	$sql = "SELECT * FROM adresy WHERE ad_su_id = ".$AUTH[parent]." ORDER BY ad_adres";
	$res = $projdb->execute($sql);
	for ($i=0; $i < $res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));
		$adr=stripslashes($ad_adres);
		if (strlen($ad_ws)) $ad_adres = $ad_ws;
		$a=addslashes($ad_adres);
		$adres_options.="<option value=\"$a\">$adr</option>\n";		
	}

	if (!strlen($adres_options)) $adres_display="none";


	$sql = "SELECT * FROM koszyk WHERE
		ko_su_id = ".$AUTH[id]." 
		AND ko_rez_data IS NULL 
		AND (ko_deadline > $NOW OR ko_deadline IS NULL)
		ORDER BY ko_id";

	$res = $adodb->execute($sql);
		
	if (!$res->RecordCount())
	{
		$error = sysmsg("no_article_in_cart","cart");
		return;
	}

	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_article_id=sysmsg("Article Id","cart");
	$sysmsg_article_name=sysmsg("Article name","cart");
	$sysmsg_article_sizes=sysmsg("Article sizes","cart");
	$sysmsg_quantity=sysmsg("Quantity","cart");
	$sysmsg_price=sysmsg("Price","cart");
	$sysmsg_value=sysmsg("Value","cart");
	$sysmsg_total=sysmsg("Total","cart");
	$sysmsg_notice=sysmsg("Notice","system");
	$sysmsg_adres=sysmsg("Delivery addres","system");
	$sysmsg_submit=sysmsg("Submit order","system");
	$sysmsg_please=sysmsg("Please, fill the order number field ","cart");
	$sysmsg_order_number=sysmsg("Order number","system");
	$sysmsg_delivery = sysmsg("Delivery","cart");
	$sysmsg_choose_delivery = sysmsg("Choose delivery type","cart");
	$sysmsg_delivery_free = sysmsg("Delivery free","cart");
	$sysmsg_delivery_costs = sysmsg("Delivery costs","cart");
	$sysmsg_netto = sysmsg("netto","cart");
	$sysmsg_brutto = sysmsg("gross","cart");
	
	if (!strlen($adres_options)) $sysmsg_adres="&nbsp;";
	
	/*****************************************************************************/
	$sql = "SELECT * FROM tr_waga ORDER BY tr_waga_do DESC LIMIT 1;";
	parse_str(ado_query2url($sql));
  
  include_once("$SKLEP_INCLUDE_PATH/templates/towar_doplata.php");
	/*****************************************************************************/
	
	$lp=0;
	$i=0;
	$total_quant = 0;
	$total_value = 0;
	$total_value_br = 0;

	$display_noprice = ($AUTH[p_price]) ? "" : "none";

?>
