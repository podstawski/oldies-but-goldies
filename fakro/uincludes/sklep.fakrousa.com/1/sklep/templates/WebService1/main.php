<?
	eval("\$${filename}_list=array();");
	$ws=$WM->ws_action_pre("$SOAP_PATH/$filename",$param_str,&$operation,$output);
	if ($ws)
	{
		eval($param_str);
		$towar="";
		$error=$WM->ws_action($ws,$operation,$params,$output,$res_str);
		if (!strlen($error)) eval($res_str);
	}
	else $error="Brak definicji";

	if (strlen($error)) return;

	eval("\$WebService_list = \$${filename}_list;");
	$i=0;
?>
