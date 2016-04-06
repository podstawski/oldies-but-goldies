<script language="javascript">
function showModFun()
{
}
</script>
<?
	if (!strlen($xml)) $xml=$costxt;

	$a=xml2obj($xml);
	$xml=$a->xml;
?>

<table cellpadding=4>

<tr class=k_form>
	<td colspan=4 class=k_formtitle><?echo label("Login / Reggistration / Profile")?>:</td>
</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_name]" value=1 <? if ($xml->field_name) echo "checked"?>>
		<? echo label("Company name") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->name_label)?$xml->name_label:label("Company name")?>" 
		name="AUTH[name_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_name]" value=1 <? if ($xml->prompt_name) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_name]" value=1 <? if ($xml->required_name) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_person]" value=1 <? if ($xml->field_person) echo "checked"?>>
		<? echo label("Person") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->person_label)?$xml->person_label:label("Person")?>" 
		name="AUTH[person_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_person]" value=1 <? if ($xml->prompt_person) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_person]" value=1 <? if ($xml->required_person) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>



<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_name2]" value=1 <? if ($xml->field_name2) echo "checked"?>>
		<? echo label("Name 2") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->name2_label)?$xml->name2_label:label("Name 2")?>" 
		name="AUTH[name2_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_name2]" value=1 <? if ($xml->prompt_name2) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_name2]" value=1 <? if ($xml->required_name2) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>



<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_username]" value=1 <? if ($xml->field_username) echo "checked"?>>
		<? echo label("Username") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->username_label)?$xml->username_label:label("Username")?>" 
		name="AUTH[username_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_username]" value=1 <? if ($xml->prompt_username) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_username]" value=1 <? if ($xml->required_username) echo "checked"?>>
		<? echo label("field required") ?></td>
</tr>


<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_password]" value=1 <? if ($xml->field_password) echo "checked"?>>
		<? echo label("Password") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->password_label)?$xml->password_label:label("Password")?>" 
		name="AUTH[password_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_password]" value=1 <? if ($xml->prompt_password) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_password]" value=1 <? if ($xml->required_password) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>


<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_email]" value=1 <? if ($xml->field_email) echo "checked"?>>
		<? echo label("Email") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->email_label)?$xml->email_label:label("Email")?>" 
		name="AUTH[email_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_email]" value=1 <? if ($xml->prompt_email) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_email]" value=1 <? if ($xml->required_email) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_country]" value=1 <? if ($xml->field_country) echo "checked"?>>
		<? echo label("Country") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->country_label)?$xml->country_label:label("Country")?>" 
		name="AUTH[country_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_country]" value=1 <? if ($xml->prompt_country) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_country]" value=1 <? if ($xml->required_country) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_zip]" value=1 <? if ($xml->field_zip) echo "checked"?>>
		<? echo label("Zip") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->zip_label)?$xml->zip_label:label("Zip")?>" 
		name="AUTH[zip_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_zip]" value=1 <? if ($xml->prompt_zip) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>

	<td><input type="checkbox" name="AUTH[required_zip]" value=1 <? if ($xml->required_zip) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_city]" value=1 <? if ($xml->field_city) echo "checked"?>>
		<? echo label("City") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->city_label)?$xml->city_label:label("City")?>" 
		name="AUTH[city_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_city]" value=1 <? if ($xml->prompt_city) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_city]" value=1 <? if ($xml->required_city) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_address]" value=1 <? if ($xml->field_address) echo "checked"?>>
		<? echo label("Address") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->address_label)?$xml->address_label:label("Address")?>" 
		name="AUTH[address_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_address]" value=1 <? if ($xml->prompt_address) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_address]" value=1 <? if ($xml->required_address) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_tel]" value=1 <? if ($xml->field_tel) echo "checked"?>>
		<? echo label("Phone") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->tel_label)?$xml->tel_label:label("Phone")?>" 
		name="AUTH[tel_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_tel]" value=1 <? if ($xml->prompt_tel) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_tel]" value=1 <? if ($xml->required_tel) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>


<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_nip]" value=1 <? if ($xml->field_nip) echo "checked"?>>
		<? echo label("NIP") ?></td>
	<td><input type="text" size=50 value="<? echo strlen($xml->nip_label)?$xml->nip_label:label("NIP")?>" 
		name="AUTH[nip_label]" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[prompt_nip]" value=1 <? if ($xml->prompt_nip) echo "checked"?>>
		<? echo label("prompt allowed") ?></td>
	<td><input type="checkbox" name="AUTH[required_nip]" value=1 <? if ($xml->required_nip) echo "checked"?>>
		<? echo label("field required") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_select1]" value=1 <? if ($xml->field_select1) echo "checked"?>>
		<? echo label("label") ?></td>
	<td>
		<input type="text" size=50 value="<? echo strlen($xml->label_select1)?$xml->label_select1:label("label")?>" name="AUTH[label_select1]" class="k_input"><br>
		<textarea cols=50 rows=5 name="AUTH[list_option1]" class="k_input"><? echo strlen($xml->list_option1)?$xml->list_option1:label("option1=value1&option2=value2...")?></textarea>
		</td>
	<td></td>
	<td></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="AUTH[field_select2]" value=1 <? if ($xml->field_select2) echo "checked"?>>
		<? echo label("label") ?></td>
	<td>
		<input type="text" size=25 value="<? echo strlen($xml->label_select2)?$xml->label_select2:label("label")?>" name="AUTH[label_select2]" class="k_input"><br>
		<textarea cols=50 rows=5 name="AUTH[list_option2]" class="k_input"><? echo strlen($xml->list_option2)?$xml->list_option2:label("option1=value1&option2=value2...")?></textarea>
	</td>
	<td></td>
	<td></td>

</tr>



<?
	$sql = "SELECT DISTINCT(c_email2) FROM crm_customer WHERE
			c_server = $SERVER_ID ORDER BY c_email2";

	$res = $adodb->execute($sql);
	$select_group = "<SELECT name=\"AUTH[email2]\" id=\"c_email2_select\" class=\"k_input\">";
	$gc = $res->RecordCount();
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		if ($xml->email2 == $c_email2) $sel = "selected";
		else $sel = "";
		$select_group.= "<option value=\"$c_email2\" $sel>$c_email2</option>"; 
	}
	$select_group.= "</SELECT>";

$lista = "<img align=\"absMiddle\" src=\"img/i_tree_n.gif\" id=\"select_img\" onclick=\"reloadSelect()\" style=\"cursor:hand\">";
$da_input = "<img  align=\"absMiddle\" src=\"img/i_new_n.gif\" id=\"input_img\" onclick=\"reloadSelect()\" style=\"cursor:hand\">";
?>

<tr class=k_form>
	<td><? echo label("Group");?> </td>
	<td><? echo $lista." ".$da_input; echo $select_group?><input type="text" size=25 value="<? echo $xml->email2?>" 
		name="AUTH[email2]" id="c_email2_input" class="k_input"></td>
	<td><input type="checkbox" name="AUTH[show_list]" value=1 <? if ($xml->show_list) echo "checked"?>>
		<? echo label("show user list") ?></td>

		<td>&nbsp;</td>
</tr>

<tr class=k_form>
	<td><? echo label("Button") ?></td>
	<td><input type="text" size=50 
		value="<? echo strlen($xml->submit_button)?$xml->submit_button:label("Button")?>" 
		name="AUTH[submit_button]" class="k_input"></td>
	<td>
		<select name="AUTH[action]" class="k_select">
			<option value=""><?echo label("Login")?></option>
			<option value="NewCustomer" <?if ($xml->action=="NewCustomer") echo "selected"?>><?echo label("Reggister")?></option>
			<option value="EditCustomer" <?if ($xml->action=="EditCustomer") echo "selected"?>><?echo label("Update data")?></option>
			<option value="RemindPass" <?if ($xml->action=="RemindPass") echo "selected"?>><?echo label("Remind password")?></option>
		</select>
	</td>
	<td>&nbsp;</td>
</tr>


</table>

<script>
			function reloadSelect()
			{
				if(document.all['c_email2_select'].style.display == 'none')
				{
					document.all['c_email2_select'].disabled = false;
					for (i=0; i < document.all['c_email2_select'].options.length;i++)
						if (document.all['c_email2_select'].options[i].value == document.all['c_email2_input'].value)
							document.all['c_email2_select'].options[i].selected = true;

					document.all['c_email2_select'].style.display = 'inline';
					document.all['c_email2_input'].style.display = 'none';
					document.all['c_email2_input'].disabled = true;
					document.all['c_email2_select'].disabled = false;
					document.all['select_img'].style.display = 'none';
					document.all['input_img'].style.display = 'inline';
				} else
				{
					document.all['c_email2_input'].value = document.all['c_email2_select'].value;
					document.all['c_email2_select'].style.display = 'none';
					document.all['c_email2_select'].disabled = true;
					document.all['c_email2_input'].disabled = false;
					document.all['c_email2_input'].style.display = 'inline';
					document.all['select_img'].style.display = 'inline';
					document.all['input_img'].style.display = 'none';
				}
			}
			<?
				if ($gc)
				{
					echo "
					document.all['c_email2_select'].style.display = 'inline';\n
					document.all['c_email2_input'].style.display = 'none';\n
					document.all['c_email2_input'].disabled = true;\n
					document.all['c_email2_select'].disabled = false;\n
					document.all['select_img'].style.display = 'none';\n
					document.all['input_img'].style.display = 'inline';\n
					";
				} 
				else
				{
					echo "reloadSelect();\n";
				}

			?>

</script>
