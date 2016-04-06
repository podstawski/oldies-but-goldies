<?
	$date_up='';

	if ($FORM[acc_status]==0 && $new_stat==1) 
	{				
		$date_up = ", za_data_przyjecia = $NOW, za_osoba_przyjecia=$AUTH[id]";
		$kom = ",za_uwagi_przyjecia = '".$FORM[kom]."'";
	}
	if ($FORM[acc_status]>=1 && $new_stat==-1) 
	{				
		$date_up = ", za_data_realizacji = $NOW, za_osoba_realizacji=$AUTH[id]";
		$kom = ",za_uwagi_realizacji = '".$FORM[kom]."'";				
	}
	if ($FORM[acc_status]==-5 && $new_stat==0) 
	{				
		$date_up = ", za_data_przyjecia = NULL, za_data_realizacji = NULL";
		$kom = ",za_uwagi_przyjecia = '".$FORM[kom]."'";		
	}


	if ($FORM[acc_status]==0 && $new_stat==-5) 
	{
		$date_up = ", za_data_przyjecia = $NOW, za_data_realizacji = NULL, za_osoba_przyjecia=$AUTH[id]";
		$kom = ",za_uwagi_przyjecia = '".$FORM[kom]."'";
	}
	if ($FORM[acc_status]==1 && $new_stat==0) 
	{
		$date_up = ", za_data_przyjecia = NULL, za_data_realizacji = NULL";
	}
	if ($FORM[acc_status]==-1 && $new_stat==1) 
	{
		$date_up = ", za_data_przyjecia = $NOW";
	}

	$date_up.= ", za_data_status = $NOW";

?>