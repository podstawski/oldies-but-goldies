<?
	if (!is_array($WebService_list))
	{
		$template_loop=0;
		return;
	}

	if ($i>=count($WebService_list))
	{
		$template_loop=0;
		return;
	}
	

	while (list($key,$val) = each ($WebService_list[$i]) ) eval ("\$$key = \$val;");
	$i++;
?>
