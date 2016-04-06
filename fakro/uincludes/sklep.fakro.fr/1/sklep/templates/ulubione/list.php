<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;
	$lp=$i+$LIST[start];

	$sql = "SELECT * FROM towar WHERE to_id = $ul_to_id";
	parse_str(ado_query2url($sql));

	$cena = $WM->system_cena($to_id,$ul_ilosc,$AUTH[parent]);
	$wart = $cena*$ul_ilosc;
	
	$_tr = " class=t1";
	if (($i)%2) $_tr = " class=t2";

	$wymiary = $WM->towar_wymiary($to_id);

	$total_quant+= $ul_ilosc;
	$total_value+= ($cena*$ul_ilosc);

	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
?>
