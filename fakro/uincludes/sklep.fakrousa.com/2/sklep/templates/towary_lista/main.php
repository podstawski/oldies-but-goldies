<?
	if (!strlen($LIST[sort_f]))
	{
		$s="ts_cena";
		if (strlen($def_sort)) $s=$def_sort;
		$LIST[sort_f]=$s;
		$LIST[sort_d]="0";
	}

	$order="ORDER BY $LIST[sort_f]";
	if ($LIST[sort_d]) $order.=" DESC";

	
	if (!$kat_id)
	{
		$query="SELECT ka_id FROM kategorie WHERE ka_kod='$page'";
		parse_str(ado_query2url($query));
	}
	else $ka_id=$kat_id;

	if (!$ka_id) 
	{
		$error = sysmsg("Articles missing","system");
		return;
	}

	$FROMWHERE="FROM towar_kategoria
				LEFT JOIN towar ON tk_to_id=to_id
				LEFT JOIN towar_sklep ON to_id=ts_to_id AND ts_sk_id=$SKLEP_ID
				WHERE tk_ka_id=$ka_id AND ts_aktywny>0
			";


	$producenci=$WM->poptemp("producenci_$ka_id");

	if (!strlen($producenci))
	{
		$query="SELECT to_pr_id $FROMWHERE AND to_pr_id IS NOT NULL
			GROUP BY to_pr_id";
		$result = $adodb->execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_explodeName($result,$i));
			if (strlen($producenci)) $producenci.=",";
			$producenci.=$to_pr_id;
		}
		if (!strlen($producenci)) $producenci="*";
		$WM->pushtemp("producenci_$ka_id",$producenci);
	}


	$producer_comment="<!--";
	$producer_no_comment="-->";


	if ($CIACHO[pr_id])
	{
		$producer_comment="";
		$producer_no_comment="";
		$FROMWHERE.=" AND to_pr_id=".$CIACHO[pr_id];
		$to_pr_id=$CIACHO[pr_id];
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}	

	if (!$LIST[ile]) $LIST[ile]=$WM->poptemp("ile_towarow_${ka_id}".$CIACHO[pr_id]);

	if (!$LIST[ile])
	{
		$query="SELECT COUNT(to_id) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
		$WM->pushtemp("ile_towarow_${ka_id}".$CIACHO[pr_id],$c);
	}


	$co="*";
	include("$SKLEP_INCLUDE_PATH/templates/towar_lista_pola.php");
	$towar_pola=$co;


	
	$navi=$size?navi($self,$LIST,$size):"";

	if (!$size) $size=-1;
	$start=$LIST[start]+0;
	$h="tk_$ka_id";
	if ($CIACHO[pr_id]) $h.="_".$CIACHO[pr_id];
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


	$liczba_rekordow="<br>Liczba rekordów: ".count($to_ids)."<br>";
	echo $liczba_rekordow;
	

	$lp=$LIST[start];

	$sysmsg_th_lp=sysmsg("Lp","system");
	$sysmsg_th_name=sysmsg("Article name","article");
	$sysmsg_th_index=sysmsg("Article index","article");
	$sysmsg_th_photo=sysmsg("Article photo","article");
	$sysmsg_th_desc=sysmsg("Description","article");
	$sysmsg_th_price=sysmsg("Price","article");
	$sysmsg_th_option=sysmsg("Action","article");
	$sysmsg_th_megastor_stack=sysmsg("Stack","article");
	$sysmsg_th_megastor_prom=sysmsg("Promotion","article");

?>
