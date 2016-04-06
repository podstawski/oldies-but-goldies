<?
	$action="";
	$warunek="WHERE pe_parent IS NULL";
	if (strlen($pe_sess_id)) 
	{
		$warunek = "WHERE pe_sess_id='$pe_sess_id' AND pe_parent IS NULL";
		if  ( !strlen($pe_parent) ) 
		{
			$_REQUEST['pe_sess_id']='';
			$pe_sess_id='';
		}
	}
	if (strlen($pe_parent)) 
	{
		$warunek = "WHERE pe_id=$pe_parent";
		$pe_parent='';
	}

		
	$adodb->freeze_debug=true;
	$query="DELETE FROM kameleon_performance $warunek";
	if ($adodb->Execute($query)) logquery($query) ;
?>