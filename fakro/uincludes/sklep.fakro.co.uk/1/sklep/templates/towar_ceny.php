<?
	$to_cena_zl=u_Cena($to_cena);
	$to_cena_br=round($to_cena*(100+$to_vat))/100;
	$to_cena_br_zl=u_Cena($to_cena_br);

	$cena_zl=u_Cena($cena);
	$cena_br=round($cena*(100+$to_vat))/100;
	$cena_br_zl=u_Cena($cena_br);

	$wart_zl=u_Cena($wart);
	$wart_br=round($wart*(100+$to_vat))/100;
	$wart_br_zl=u_Cena($wart_br);

	$wart_ws_zl=u_Cena($wart_ws);
	$wart_ws_br=round($wart_ws*(100+$to_vat))/100;
	$wart_ws_br_zl=u_Cena($wart_ws_br);

	if ($cena==0)
	{
		$cena_zl=sysmsg("price_zero_nt","cart");
		$cena_br_zl=sysmsg("price_zero_br","cart");
	}
	if ($wart==0)
	{
		$wart_br=sysmsg("value_zero_nt","cart");
		$wart_br_zl=sysmsg("value_zero_br","cart");
	}
  
//if($_SERVER['REMOTE_ADDR'] == '37.128.111.165') {

  $total_value_zl=u_Cena($total_value);
//  $total_value_br+=$wart_br;
  $total_value_br_tmp+=$wart_br;
  
  $total_value_br = ($total_value_br_tmp-(($total_value_br_tmp*$voucher_wartosc)/100));
  $total_value_br_zl = u_Cena($total_value_br_tmp-(($total_value_br_tmp*$voucher_wartosc)/100));
  
  // tylko przy kwotach
  //if($total_value_br<0) $total_value_br=0;
  //if($total_value_br_zl<0) $total_value_br_zl=u_Cena(0);
  
//}else{
//	$total_value_zl=u_Cena($total_value);
//	$total_value_br+=$wart_br;
//	$total_value_br_zl=u_Cena($total_value_br);
//}

	$total_value_ws_zl=u_Cena($total_value_ws);
	$total_value_ws_br+=$wart_ws_br;
	$total_value_ws_br_zl=u_Cena($total_value_ws_br);
	

	$cena_o_zl=u_cena($cena_o);
	$cena_o_br=round($cena_o*(100+$to_vat))/100;
	$cena_o_br_zl=u_Cena($cena_o_br);

	$total_value_o_zl=u_Cena($total_value_o);
	$total_value_o_br=round($total_value_o*(100+$to_vat))/100;
	$total_value_o_br_zl=u_Cena($total_value_o_br);
?>
