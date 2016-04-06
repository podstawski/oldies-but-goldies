<?
	if (!$prom)
	{
		$error="Promocja ? :-(";
		return;
	}


	$s="ts_cena";
	if (strlen($def_sort)) $s=$def_sort;
	$LIST[sort_f]=$s;
	$LIST[sort_d]="0";


	$order="ORDER BY $LIST[sort_f]";
	if ($LIST[sort_d]) $order.=" DESC";

	$FROMWHERE="";

	//$adodb->debug=1;

	$kat_pages=array();
	$kat_idx=0;
	if ($next_id)
	{
		$kat_pages=array($page);
		$atree=explode(":",$tree);
		for ($i=count($atree)-1; $i;$i--)
		{
			if (!$atree[$i]) continue;
			$kat_pages[]=$atree[$i];
			if ($atree[$i]==$next_id) break;
		}

	}

	
	$ile=0;

	$STD_FROMWHERE="towar,towar_sklep,promocja_towaru,promocja
			WHERE to_id=ts_to_id AND pt_ts_id=ts_id 
			AND pt_pm_id=$prom AND pm_id=pt_pm_id 
			AND (pm_poczatek<$NOW OR pm_poczatek IS NULL) 
			AND (pm_koniec>$NOW OR pm_koniec IS NULL) 
			AND (pt_poczatek<$NOW OR pt_poczatek IS NULL)
			AND (pt_koniec>$NOW OR pt_koniec IS NULL)
			AND ts_sk_id=$SKLEP_ID AND ts_aktywny>0";

	while (!$ile)
	{
		$FROMWHERE="FROM $STD_FROMWHERE";

		$kat_page=$kat_pages[$kat_idx++];

		if ($kat_page)
		{
			$FROMWHERE="FROM kategorie,towar_kategoria,$STD_FROMWHERE
				AND tk_to_id=to_id AND tk_ka_id=ka_id AND ka_kod='$kat_page'
				";
		}

		$h="ile_w_prom_${prom}$kat_page";
		$ile=$WM->poptemp($h);
		if (!$ile)
		{
			$query="SELECT COUNT(*) AS ile $FROMWHERE";
			parse_str(ado_query2url($query));
			if ($ile) $WM->pushtemp($h,$ile);
		}

		if (!$next_id) break;
		if (!$kat_page) break;

	}

	
	//exit();

	if (!$ile)
	{
		$error="&nbsp;";
		return;
	}


	$co="*";
	include("$SKLEP_INCLUDE_PATH/templates/towar_lista_pola.php");
	$towar_pola=$co;

	
	$navi=$size?navi($self,$LIST,$size):"";

	if (!$size) $size=-1;
	$start=$LIST[start]+0;
	$h="tp_${prom}$kat_page";
	$h.="_".$LIST[sort_f].$LIST[sort_d];
	$h.="_".$size;
	$h.="_".$start;

	$to_ids=$WM->to_ids($FROMWHERE,$h,$size,$start,$order);


	$i=0;
	if (!strlen($to_ids)) 
	{
		$to_ids=array();
		return;
	}

	$to_ids=explode(",",$to_ids);



	echo "<br>Liczba rekordów: ".count($to_ids)."<br>";
	

	$lp=$LIST[start];



	$sysmsg_th_lp=sysmsg("Lp","system");
	$sysmsg_th_name=sysmsg("Article name","article");
	$sysmsg_th_index=sysmsg("Article index","article");
	$sysmsg_th_photo=sysmsg("Article photo","article");
	$sysmsg_th_desc=sysmsg("Description","article");
	$sysmsg_th_price=sysmsg("Price","article");
	$sysmsg_th_option=sysmsg("Action","article");
	

	$query="SELECT * FROM promocja WHERE pm_id=$prom";
	parse_str(ado_query2url($query,true));
	
	
?>
