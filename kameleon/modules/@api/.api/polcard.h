<?
	global $REMOTE_ADDR;

	$a=xml2obj($costxt);
	$xml=$a->xml;

	if (strlen($POLCARD[resp]))
	{
		include("$INCLUDE_PATH/.api/polcard_resp.h");
		return;
	}

	$required_fields=array( "amount"		=>	10,
							"order_id"		=>	20,
							"email"			=>	30,
							"street"		=>	30,
							"phone"			=>	15,
							"postcode"		=>	7,
							"city"		=>	15,
							"country"		=>	2
							);
	$may_forward=1;


	$next_form="<form action=\"$next\" method=\"POST\">
				<table class=\"api2_polcard_table\" cellspacing=0 cellpadding=0>";
	$polcard_form="<form action=\"https://post.polcard.com.pl/cgi-bin/autoryzacja.cgi\" method=\"post\">";

	while ( list($k,$v) = each($required_fields) )
	{
		if ($k=="amount") $POLCARD[$k]=ereg_replace("[^0-9\.]","",ereg_replace(",",".",$POLCARD[$k]));

		if (strlen($POLCARD[$k]))
		{
			$input="$POLCARD[$k]<input type=\"hidden\" name=\"POLCARD[$k]\" value=\"$POLCARD[$k]\">";
			if ($k=="amount") $input.=" PLZ";
			

			$next_form.="<tr class=\"api2_polcard_tr\">
						<td class=\"api2_polcard_td_left\">".$xml->$k."</td>
						<td class=\"api2_polcard_td_right\">$input</td>
						</tr>";

			$value = $POLCARD[$k];
			if ($k=="amount") $value=round($POLCARD[$k]*100);
			if ($k=="order_id") $value=base64_encode("$value");
			$polcard_form.="<input type=\"hidden\" name=\"$k\" value=\"$value\">";
			$cookie_script.="	document.cookie='POLCARD[$k]=$value';\n";


		}
		else
		{
			$may_forward=0;
			$input="<input type=\"text\" size=\"$v\" name=\"POLCARD[$k]\" 
						class=\"api2_polcard_input\">";

			if ($k=="amount") $input.=" PLZ";


			if ($k=="country") 
			{
				$input="<select name=\"POLCARD[$k]\" class=\"api2_polcard_select\">";
				$input.=implode("\n",file("$INCLUDE_PATH/countries_opt.inc"));
				$input.="</select>";
			}


			$next_form.="<tr class=\"api2_polcard_tr\">
						<td class=\"api2_polcard_td_left\">".$xml->$k."</td>
						<td class=\"api2_polcard_td_right\">$input</td>
						</tr>";
		}
		
		
	}

	if ($may_forward)
	{
		$polcard_form.="<input type=\"hidden\" name=\"client_ip\" value=\"$REMOTE_ADDR\">";
		$polcard_form.="<input type=\"hidden\" name=\"pos_id\" value=\"$xml->pos_id\">";

		$test=($xml->test)?"Y":"N";
		$polcard_form.="<input type=\"hidden\" name=\"test\" value=\"$test\">";

		switch ($lang)
		{
			case "i": 
			case "p":
					$l="PL";
					break;
			case "d":
					$l="DE";
					break;
			default:
					$l="EN";
		}
		$polcard_form.="<input type=\"hidden\" name=\"language\" value=\"$l\">";


		$polcard_form.="<div class=\"api2_polcard_td_submit\"><input type=\"submit\" value=\"$xml->submit_payment\" class=\"api2_polcard_submit\"></div>";

		$polcard_form.="</form>";

		$next_form.="</table></form>\n";

		echo $next_form;
		echo $polcard_form;

		if ( strlen($POLCARD["return"]) )
		{
			$ret_code=base64_encode($POLCARD["return"]);
			$JScript.="	document.cookie='ret_code=$ret_code';\n";
		}

	}
	else
	{
		$next_form.="<tr class=\"api2_polcard_tr\">
						<td class=\"api2_polcard_td_submit\" colspan=2>
						<input type=\"submit\" value=\"$xml->submit_data\"
							class=\"api2_polcard_submit\">
						</td>
					</tr>";
		
		$next_form.="<input type=\"hidden\" name=\"POLCARD[return]\" value=\"".$POLCARD["return"]."\">";
		$next_form.="</table></form>\n";

		echo $next_form;

	}



?>
