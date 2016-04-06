<?

	require_once("$SKLEP_INCLUDE_PATH/nusoap/nusoap.php");
	include_once("$SKLEP_INCLUDE_PATH/admin/ws_fun.php");

	$TemplWebService=1;

	if (substr($filename,0,10)!="WebService")
	{
		$TemplWebService=0;
		$filename="";
		parse_str($costxt);
	}

	global $WS;

	if ("$WS[sid]"=="$WEBTD->sid")
	{
		if (!$TemplWebService)
		{
			$filename=$WS[filename];
			$cache=$WS[cache];
			$kameleon_adodb->Execute("UPDATE webtd SET costxt='filename=$filename' WHERE sid=$WEBTD->sid");
			$WEBTD->costxt="filename=$filename";
		}

		if (strlen($WS[wsdl]))
		{
			$wsdl=stripslashes($WS[wsdl]);
			$operation=stripslashes($WS[operation]);

			$plik=fopen("$SOAP_PATH/$filename","w");
			fwrite($plik,"wsdl=".urlencode($wsdl)."\n");
			fwrite($plik,"operation=".urlencode($operation)."\n");

			while(list($k,$v)=each($WS))
			{
				if ( substr($k,0,6)!="input:" && substr($k,0,7)!="output:" ) continue;
				$pole=ereg_replace(":","][",$k)."]";
				$pole=ereg_replace("(in|out)put\]","\\1put",$pole);

				fwrite($plik,"$pole=".urlencode(stripslashes($v))."\n");
			}

			fclose($plik);
		}


		echo "Zaktualizowano $SOAP_PATH/$filename<br />";
	}

	$input_parameters="";
	$output_parameters="";

	$ws=array();
	$dh = opendir("$SKLEP_INCLUDE_PATH/action"); 
	while (($file = readdir($dh)) !== false) 
	{
		if ($file[0]==".") continue;
		if (substr($file,0,2)!="WS") continue;

		$ws[]=$file;
	}
	sort($ws);

	$options="";
	foreach ($ws AS $file)
	{
		$sel=($file==$filename)?"selected":"";
		$options.="<option $sel value=\"$file\">$file</option>";
	}

	if (!file_exists("$SOAP_PATH")) mkdir ($SOAP_PATH,0700);

	if (strlen($filename) && file_exists("$SOAP_PATH/$filename"))
	{
		$plik=file("$SOAP_PATH/$filename");
		for ($i=0;$i<count($plik);$i++) 
		{
			parse_str(trim(quoteUrlEnc($plik[$i])));
			$var=substr($plik[$i],0,strpos($plik[$i],"="));
			
			eval("\$$var=stripslashes(\$$var);");
		}
	}


	if (strlen($wsdl))
	{
		$wsdl_file=$wsdl;
		$wsdl_url=$wsdl;
		$wft=@filemtime("$SOAP_PATH/$filename.wsdl");
		$ft=@filemtime("$SOAP_PATH/$filename");

		if ($wft+3600*24 < $NOW || $ft>$wft)
		{
			if (strlen($SYSTEM[wsu]))
			{
				$u=urlencode($SYSTEM[wsu]);
				$p=urlencode($SYSTEM[wsp]);
				$wsdl_url=ereg_replace("^(http[s]*://)","\\1$u:$p@",$wsdl);
			}
			$w=fcontent($wsdl_url);
			if (strlen($w))
			{
				$plik=@fopen("$SOAP_PATH/$filename.wsdl","w");
				if ($plik)
				{
					fwrite($plik,$w);
					fclose($plik);
					$wsdl_file="$SOAP_PATH/$filename.wsdl";
					echo "Zapisano WSDL do $SOAP_PATH/$filename.wsdl <br>";
				}
			}
		}
		else $wsdl_file="$SOAP_PATH/$filename.wsdl";


		$client = new soapclient($wsdl_file, true);
		$err = $client->getError();
		if ($err) 
		{
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		}

		
		$operations="";
		$o=$client->operations;
		foreach(array_keys($o) AS $oper) 
		{
			$sel=($oper==$operation)?"selected":"";
			$operations.="<option value=\"$oper\" $sel>$oper</option>";
		}


		$namespace=$client->wsdl->namespaces[tns];
		if (!strlen($namespace)) $namespace=$client->wsdl->namespaces[s0];

		$types=$client->wsdl->schemas[$namespace][0]->complexTypes;
		
		echo "<pre>";
		//print_r($client->operations);
		//print_r($client);
		//print_r($types);

// CAMELA KAWAŁKI KODU

		list($bindname,) = each($client->wsdl->bindings);

		$operacje = $client->wsdl->bindings[$bindname][operations];
		$metody = array();

		foreach($operacje as $operacja)
		{
			$nazwa = $operacja[name];
			$metody[] = $nazwa;
			$parts = $operacja[input][parts];
			foreach($parts as $part => $val)
				$argumenty[$nazwa][] = $part;

			$parts = $operacja[output][parts];
			foreach($parts as $part => $val)
				$wyniki[$nazwa][] = $part;
		}

//		print_r($client->operations[$operation][input][parts]);
// END OF CAMELA KAWAŁKI KODU

		if (strlen($operation))
		{
			if (strlen($client->operations[$operation][input][parts][parameters]))
			{
				$input_name=substr($client->operations[$operation][input][parts][parameters],strlen($namespace)+1);
				$input_parameters=param2form("input",$types["${input_name}_ContainedType"],$types,$input);
			}
			else
			{
				reset($types);
				list($input_name,$i_type) = each($client->operations[$operation][input][parts]);
				$i_type = substr($i_type,strrpos($i_type,":")+1);
				$input_types = $types[$i_type];
				$input_parameters=param2form("input",$input_types,$types,$input);
			}

			if (strlen($client->operations[$operation][output][parts][parameters]))	
			{
				$output_name=substr($client->operations[$operation][output][parts][parameters],strlen($namespace)+1);
				$output_parameters=param2form("output",$types["${output_name}_ContainedType"],$types,$output);
			}
			else
			{
				reset($types);
				list($output_name,$o_type) = each($client->operations[$operation][output][parts]);
				$o_type = substr($o_type,strrpos($o_type,":")+1);
				$output_types = $types[$o_type];
				$output_parameters=param2form("output",$output_types,$types,$output);
			}

			print_r ($output_types);
			echo "\n=================\n";
/*
			print_r($client->operations[$operation][output][parts]);
			echo "\n===============================================\n";
			print_r($types);
			echo "\n=================\n";
			print_r($output_types);
			print_r($client->wsdl->schemas[$namespace][0]);
*/				
		}

		echo "</pre>";
	}


?>


<form method="post" action="<?echo $self ?>">
<input type="hidden" name="WS[sid]" value="<?echo $WEBTD->sid?>">
<table>

<? if (!$TemplWebService) { ?>
<tr>
<td>Akcja lokalna:</td><td>
<select name="WS[filename]">
	<option value="">Wybierz akcję</option>

	<?echo $options?>
</select>
</td></tr>
<? } ?>
<? if (strlen($filename)) { ?>
<tr>
<td>WSDL:</td><td><input type="text" size=80 name="WS[wsdl]" value="<?echo $wsdl?>"></td>
</tr>

<? } ?>

<? if (strlen($wsdl)) { ?>

<tr>
<td>Operacja WS:</td><td>

	<select name="WS[operation]">
	<option value="">Wybierz</option>
	<?echo $operations?>
	</select>
</td>
</tr>


<? 
	echo $input_parameters;
	echo $output_parameters;
} 
?>

</table>

<input type="submit" class="but" value="Zapisz">
</form>

