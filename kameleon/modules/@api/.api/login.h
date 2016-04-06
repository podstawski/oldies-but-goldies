<?
	if (!strlen($xml)) $xml=$costxt;

	$a=xml2obj($xml);
	$xml=$a->xml;

	if ($xml->action=="EditCustomer")
	{
		push($xml);
		ob_start();
		include("$INCLUDE_PATH/.api/auth.h");
		$result=ob_get_contents();
		ob_end_clean();	
		$xml=pop();
		$result=explode(":",$result);
		if (strlen($result[1])) $AUTH=NULL;
		parse_str($result[1]);
	
		if (strlen($AUTH[c_xml]))
		{
			$_xml = xml2obj($AUTH[c_xml]);
			$_obj = $_xml->xml;
			while (list($key,$val) = each($_obj))
				$AUTH[$key] = $val;
		}

	}

	// jesli kliknie na zaloguj a jest zalogowany to -> next, czyli after login
	$JScript="";
	if (!strlen(trim($xml->action)) && $AUTH[c_id]>0 && !$xml->show_list)
	{
		$JScript.="
					location.href='$next';
				";
	}


	$COUNTRY_TOKENS["i"]="PL";
	$COUNTRY_TOKENS["p"]="PL";
	$COUNTRY_TOKENS["t"]="CZ";
	$COUNTRY_TOKENS["f"]="FR";
	$COUNTRY_TOKENS["r"]="RU";
	$COUNTRY_TOKENS["s"]="ES";

	$field=label("Field");
	$required=label("is required");

	$JScript.="
		if (apiValidujForm==null)
		{
			function apiValidujForm(obj)
			{
				e=obj.elements;
				for (i=0;i<e.length;i++)
				{
					if (e[i].type=='hidden') continue;
					if (e[i].type=='submit') continue;
					if (e[i].breq==0) continue;
					txt=e[i].value;
					if (txt.length) continue;
					a='$field \'' + e[i].req_txt + '\' $required';
					alert(a);
					e[i].focus();
					return false;
				}
				return true;
			}
		}
";

?>

<form method="POST" action="<?echo $next?>" name="api2_login_form" onSubmit="return apiValidujForm(this)">
<? if (strlen($xml->action)) { ?>
<input type="hidden" name="action" value="<?echo $xml->action?>">
<input type="hidden" name="ACTION_VAR" value="AUTH,CAUTH">
<input type="hidden" name="AUTH[email2]" value="<? echo $xml->email2 ?>">
<?}?>
<table cellspacing=0 cellpadding=0 class="api2_auth_table">

<? if ($xml->field_name) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->name_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_name]" breq="<?echo 0+$xml->required_name?>"
				req_txt="<? echo $xml->name_label ?>"
				value="<? if ($xml->prompt_name) echo stripslashes($AUTH[c_name])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>

<? if ($xml->field_person) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->person_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_person]" breq="<?echo 0+$xml->required_person?>"
				req_txt="<? echo $xml->person_label ?>"
				value="<? if ($xml->prompt_person) echo stripslashes($AUTH[c_person])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>



<? if ($xml->field_name2) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->name2_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_name2]" breq="<?echo 0+$xml->required_name2?>"
				req_txt="<? echo $xml->name2_label ?>"
				value="<? if ($xml->prompt_name2) echo stripslashes($AUTH[c_name2])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>


<? if ($xml->field_country) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->country_label ?>
		</td>
		<td class="api2_auth_td_input">
			<select name="AUTH[c_country]" class="api2_auth_select">
			<?
				$countries=implode("\n",file("$INCLUDE_PATH/countries_opt.inc"));
				$countries=ereg_replace("selected","",$countries);
				if ($xml->prompt_country)
				{
					if (strlen($AUTH[c_country])) $token=$AUTH[c_country];
					else
					{
						$token=strlen($COUNTRY_TOKENS[$lang])?$COUNTRY_TOKENS[$lang]:"US";
					}

					$token="value=\"$token\"";
					$countries=ereg_replace("($token)","\\1 selected",$countries);
				}

				echo $countries;

			?>
			</select>
		</td>
	</tr>
<?}?>


<? if ($xml->field_zip) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->zip_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_zip]" breq="<?echo 0+$xml->required_zip?>"
				req_txt="<? echo $xml->zip_label ?>"
				value="<? if ($xml->prompt_zip) echo stripslashes($AUTH[c_zip])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>

<? if ($xml->field_city) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->city_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_city]" breq="<?echo 0+$xml->required_city?>"
				req_txt="<? echo $xml->city_label ?>"
				value="<? if ($xml->prompt_city) echo stripslashes($AUTH[c_city])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>

<? if ($xml->field_address) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->address_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_address]" breq="<?echo 0+$xml->required_address?>"
				req_txt="<? echo $xml->address_label ?>"
				value="<? if ($xml->prompt_address) echo stripslashes($AUTH[c_address])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>

<? if ($xml->field_tel) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->tel_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_tel]" breq="<?echo 0+$xml->required_tel?>"
				req_txt="<? echo $xml->tel_label ?>"
				value="<? if ($xml->prompt_tel) echo stripslashes($AUTH[c_tel])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>



<? if ($xml->field_nip) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->nip_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_nip]" breq="<?echo 0+$xml->required_nip?>"
				req_txt="<? echo $xml->nip_label ?>"
				value="<? if ($xml->prompt_nip) echo stripslashes($AUTH[c_nip])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>

<? if ($xml->field_select1) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->label_select1 ?>
		</td>
		<td class="api2_auth_td_input">
			<select name="AUTH[field_select1]" breq="<?echo 0+$xml->required_select1?>"	req_txt="<? echo $xml->label_select1 ?>">
<?			$arr=explode("&",$xml->list_option1);
			for ($i=0; $i<count($arr); $i++)
			{
				$option=explode("=",$arr[$i]);
				$name=$option[0];
				$val=$option[1];
				if ($AUTH[field_select1]==$val)
				{
					$sel=" selected";
				}
				else
					$sel="";
				echo "<option value=\"$val\"$sel>$name</option>";
			}
?>		</select>
		</td>
	</tr>
<?}?>

<? if ($xml->field_select2) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->label_select2 ?>
		</td>
		<td class="api2_auth_td_input">
			<select name="AUTH[field_select2]" breq="<?echo 0+$xml->required_select2?>"	req_txt="<? echo $xml->label_select2 ?>">
<?			$arr=explode("&",$xml->list_option2);
			for ($i=0; $i<count($arr); $i++)
			{
				$option=explode("=",$arr[$i]);
				$name=$option[0];
				$val=$option[1];
				if ($AUTH[field_select2]==$val)
				{
					$sel=" selected";
				}
				else
					$sel="";
				echo "<option value=\"$val\"$sel>$name</option>";
			}
?>		</select>
		</td>
	</tr>
<?}?>




<? if ($xml->field_username) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->username_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[username]" breq="<?echo 0+$xml->required_username?>"
				req_txt="<? echo $xml->username_label ?>"
				value="<? if ($xml->prompt_username) echo stripslashes($AUTH[c_username])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>


<? if ($xml->field_email) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->email_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[c_email]" breq="<?echo 0+$xml->required_email?>"
				req_txt="<? echo $xml->email_label ?>"
				value="<? if ($xml->prompt_email) echo stripslashes($AUTH[c_email])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>



<? if ($xml->field_password) { $cokolwiek=1; ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_label">
			<? echo $xml->password_label ?>
		</td>
		<td class="api2_auth_td_input">
			<input name="AUTH[password]" type="password" breq="<?echo 0+$xml->required_password?>"
				req_txt="<? echo $xml->password_label ?>"
				value="<? if ($xml->prompt_password) echo stripslashes($AUTH[c_password])?>" class="api2_auth_input">
		</td>
	</tr>
<?}?>



<? if ($cokolwiek) { ?>
	<tr class="api2_auth_tr">
		<td class="api2_auth_td_submit" colspan=2>
			<input type="submit" value="<? echo $xml->submit_button?>" class="api2_auth_submit">
		</td>
	</tr>
<?}?>

</table>
</form>

<?
	if ($xml->show_list && $KAMELEON_MODE) include("$INCLUDE_PATH/.api/login_list.h");
?>