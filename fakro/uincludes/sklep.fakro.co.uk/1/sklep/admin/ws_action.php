<?
	include_once("$SKLEP_INCLUDE_PATH/admin/ws_fun.php");
	require_once("$SKLEP_INCLUDE_PATH/nusoap/nusoap.php");

	if (!file_exists($ws_action)) return;

	if (!strlen($ws_debug)) $ws_debug=0;

	$ws_debug=$WM->debug();
	if ($ws_debug) ob_start();

	$plik=file($ws_action);
	for ($i=0;$i<count($plik);$i++) 
	{
			parse_str(trim($plik[$i]));
			$var=substr($plik[$i],0,strpos($plik[$i],"="));	
			eval("\$$var=stripslashes(\$$var);");
	}
	
	if (file_exists("$ws_action.wsdl")) $wsdl="$ws_action.wsdl";

	$client = $WM->ws_soapclient($wsdl);
	if (strlen($SYSTEM[wsu])) $client->setCredentials($SYSTEM[wsu], $SYSTEM[wsp]);
	$client->decode_utf8=false;
	$client->soap_defencoding = 'UTF-8';

	$error = $client->getError();
	if ($error) return;

	$params=array();

	if ($ws_debug) { echo "<h2>Input def:</h2><pre>";print_r($input);echo "</pre>";}
		

	while (is_array($input) && list($k,$v)=each($input))
	{
		if (!is_array($v)) eval(" \$params[\$k]=$v ;");
		else
		{
			$str2eval=createInputSubstr("\$params[$k]",$v);
			//echo nl2br($str2eval);
			eval($str2eval);
			
		}
	}
	if ($ws_debug) {echo "<h2>Parameters:</h2><pre>";print_r($params);echo "</pre>";}

	arr2utf8($params);

	$WM->pre_exec_query_dump();
	$result=$client->call($operation, array('parameters'=>$params));
	


	if (is_array ($ws_action_clear)) foreach($ws_action_clear AS $var) eval ("\$$var=\"\";");
	
	if ($client->fault)
	{
		$error=addslashes(utf82iso88592($result[faultstring]));
		$error=ereg_replace("\n","; ",$error);
	}
	if ($err=$client->getError())
	{
		$error=addslashes(utf82iso88592($err));
		$error=ereg_replace("\n","; ",$error);
	}

	$WM->post_exec_query_dump("SOAP:$operation.$error",strlen($error)?0:1,-1,-1);

	if (strlen($error) && $ws_debug)
	{
		echo '<h2>Request:</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response:</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		$error="";
	}

	if (strlen($error)) return;


	if ($ws_debug) {echo "<h2>Result:</h2><pre>";print_r($result); echo"</pre>";}


	if (count($output)==1)
	{
		foreach($output AS $o) if (is_array($o)) $output = $o;
	}
		

	$wynik=ws_result_string($result,$output);
	

	if ($ws_debug) {echo "<h2>Output:</h2><pre>";print_r($output); echo"</pre>";}
	if ($ws_debug) {echo "<h2>Wynik:</h2><pre>$wynik</pre>";}

	if (strlen($wynik)>1000)
	{
		$tmpfname = tempnam("/tmp", "ws");
		$handle = fopen($tmpfname, "w");
		fwrite($handle, "<? $wynik ?>");
		fclose($handle);
		include ($tmpfname);
		unlink($tmpfname);
	}
	else eval($wynik);
	$wynik="";


	if ($ws_debug) $WM->ws_debug.=ob_get_contents();
	ob_end_clean();
	$ws_debug="";
?>
