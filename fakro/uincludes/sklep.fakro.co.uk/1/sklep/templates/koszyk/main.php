<?
	if ($AUTH[id]>0)
	{
		$sql = "DELETE FROM koszyk WHERE ko_deadline < $NOW OR ko_ilosc=0;
				SELECT * FROM koszyk WHERE
				ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
				ORDER BY ko_id;
				
				";

		$res = $adodb->execute($sql);
		
		if (!$res->RecordCount())
		{
			$error = sysmsg("no_article_in_cart","cart");
			return;
		}

		$display_noprice = ($AUTH[p_price]) ? "" : "none";
		$favmore = $more;
	}
	else
	{
		$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
		if (!is_array($KOSZYK_OFERT) || !count($KOSZYK_OFERT))
		{
			$error = sysmsg("no_article_in_cart","cart");
			return;
		}
	
		$tcc=0;
		foreach(array_keys($KOSZYK_OFERT) AS $tc) 
		{
			$tcc+=$KOSZYK_OFERT[$tc];
		}
		if ($tcc==0)
		{
			$error = sysmsg("no_article_in_cart","cart");
			return;
		}	
		
		$i=0;
		reset($KOSZYK_OFERT);
	}

	$total_quant = 0;
	$total_value_br = 0;
	$total_value = 0;
	$total_oryg = 0;

	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_article_id=sysmsg("Article Id","cart");
	$sysmsg_article_sizes=sysmsg("Article sizes","cart");
	$sysmsg_quantity=sysmsg("Quantity","cart");
	$sysmsg_price=sysmsg("Price","cart");
	$sysmsg_value=sysmsg("Value","cart");
	$sysmsg_article_name = sysmsg("Article name","cart");
	$sysmsg_total_value = sysmsg("Total value","cart");
	$sysmsg_total_value_br = sysmsg("Total value gross","cart");

	$sysmsg_clear = sysmsg("Clear cart","system");
	$lp=0;
	$i=0;
  
  // sprawdzenie czy uzytkownik jest zalogowany
  //if($_SERVER['REMOTE_ADDR'] != '37.128.111.165') return;
  if($AUTH['id']>0) {
    unset($voucher_id);
    
    $sql1 = "SELECT * FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_rez_data IS NULL ORDER BY ko_id;";
    $res1 = $adodb->execute($sql1);

    $ko_ts_id_voucher = array();
    for($i1=0; $i1 < $res1->RecordCount(); $i1++) {
      parse_str(ado_explodename($res1,$i1));
      if(strlen($ko_rez_uwagi) > 0) $ko_ts_id_voucher[] = "'".$ko_rez_uwagi."'";
    }
    
    if(is_array($ko_ts_id_voucher) && !empty($ko_ts_id_voucher)) {
      if(count($ko_ts_id_voucher) > 1) return;
      
      $sql2 = "SELECT * FROM towar t
      LEFT JOIN towar_kategoria kt ON (t.to_id = kt.tk_to_id)
      LEFT JOIN kategorie k ON (kt.tk_ka_id = k.ka_id)
      LEFT JOIN rabat_ilosciowy ri ON (k.ka_id = ri.ri_ka_id)
      WHERE t.to_indeks IN (".implode(',', $ko_ts_id_voucher).")
      AND k.ka_nazwa='Coupons'";
      parse_str(ado_query2url($sql2));
      
      if($to_id) {
        $voucher_table = '';
        $promocjaArr = 'Discount NEWHOME20';

        //usuĹ
        $ico_delete_org     = $SKLEP_IMAGES."/i_delete.gif";
        $ico_delete_src     = $UIMAGES."/system/i_delete.gif";
        if (file_exists($ico_delete_src)) {
          $ico_delete_size = getimagesize($ico_delete_src);
        }else{
          $ico_delete_src = $ico_delete_org;
          $ico_delete_size = getimagesize($ico_delete_org);
        }
        $ico_delete_src = $ico_delete_src;
        $ico_delete_size= $ico_delete_size[3];
        $ico_delete_alt  = sysmsg("Delete this","buttons");  
        
        // wylaczone sprawdzic gdzie to leci
        //$voucher_wartosc_table = number_format(($voucher_wartosc*-1),2,',','.');
        $voucher_wartosc = $ri_procent;

        $voucher_table = '<tr><td colspan="3">'.$promocjaArr.'</td><td>-' . $ri_procent . '%</td><td><img class="del" src="'.$ico_delete_src.'" alt="'.$ico_delete_alt.'" '.$ico_delete_size.' onClick="deleteBon();"></td></tr>';
      }
    }
  }else{
    $voucher_wartosc = 0;
  }
	
  $voucher_add_koszyk = true;
  
  
?>
