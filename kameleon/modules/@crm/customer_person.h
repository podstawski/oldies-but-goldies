<?
	global $sid,$MODULES;
	$sid=$WEBTD->sid;


	if (module_select($MODULES->crm->files->customer_slave)) 
	{
		global $CUSTOMER;
		if ($PERSON[c_parent]==$CUSTOMER[c_id])
		{
			reset($CUSTOMER);
			while(list($k,$v)=each($CUSTOMER))
				if (!strlen($PERSON[$k])) $PERSON[$k]=$v;
		}
		_display_view($MODULES->crm->files->customer_slave);
	}

?>