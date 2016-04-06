<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}

	

	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="za_data";
		$LIST[sort_d]=1;
	}
	$LIST[sort_d]+=0;

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");

	$FROMWHERE="FROM zamowienia WHERE za_su_id = ".$AUTH[parent]." 
				AND za_data >= $od AND za_data <= $do";


	$statusy_cookie=$_REQUEST[statusy];
	if (is_array($statusy_cookie))
	{
		$statusy="-123";
		while(list($s,$v)=each($statusy_cookie))
		{
			if (!$v) continue;
			$statusy.=",$s";
		}
		$FROMWHERE.=" AND za_status IN ($statusy)";
	}

	$sql = "SELECT * $FROMWHERE ORDER BY $sort";
	
	$res = $adodb->execute($sql);

	


	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_number=sysmsg("Order number.","system");
	$sysmsg_order=sysmsg("Order","system");
	$sysmsg_count=sysmsg("Articles count","system");
	$sysmsg_status=sysmsg("Status","system");


	$lp=0;
	$i=0;

	$altp=sysmsg("Print PDF","system");
?>
