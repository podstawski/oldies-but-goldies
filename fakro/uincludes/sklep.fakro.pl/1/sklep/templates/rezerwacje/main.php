<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}


	$sql = "SELECT ko_rez_data, ko_rez_nr FROM koszyk
		WHERE ko_su_id = ".$AUTH[parent]."
		AND ko_rez_data IS NOT NULL AND (ko_deadline > $NOW OR ko_deadline IS NULL)
		GROUP BY ko_rez_data, ko_rez_nr ORDER BY ko_rez_data DESC, ko_rez_nr";


	$res = $adodb->execute($sql);
		
	if (!$res->RecordCount())
	{
		$error = sysmsg("No reservations in database.","system");
		return;
	}


	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_number=sysmsg("Reservation number","system");
	$sysmsg_reservation=sysmsg("Reservation","system");
	$sysmsg_count=sysmsg("Articles count","system");
	$sysmsg_sure=sysmsg("Are You sure, You want to delete this reservation ?","order");	

	

	$lp=0;
	$i=0;

?>
