<?
	if (!$KAMELEON_MODE)
	{
		echo "This module can't be used outside WebKameleon !";
		return;
	}

	global $SERVER_ID, $show_edit, $kill_user, $c_id, $save_user, $CRMUSER, $order_by, $restrict;
	global $limit, $offset, $ile, $start, $navi, $ile;

	if ($KAMELEON_MODE) $next_char = '&';
	else $next_char = '?';
	
	include_once "navifun.h";

	$select_group = "";
	$sql = "SELECT DISTINCT(c_email2) FROM crm_customer WHERE
			c_server = $SERVER_ID ORDER BY c_email2";
	$res = $adodb->execute($sql);
	$select_group = "<SELECT id=\"c_email2_select\" onChange=\"rewriteSelect()\">";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$select_group.= "<option value=\"$c_email2\" >$c_email2</option>"; 
	}
	$select_group.= "</SELECT>";
	$lista = "<img src=\"img/i_tree_n.gif\" id=\"select_img\" onclick=\"reloadSelect()\" style=\"cursor:hand\">";
	$da_input = "<img src=\"img/i_new_n.gif\" id=\"input_img\" onclick=\"reloadSelect()\" style=\"cursor:hand\">";

	$c_array = explode(":",$costxt);
	
	if (count($c_array) < 1) return;

	for($i=0; $i < count($c_array) ; $i++)
		if (strlen(trim($c_array[$i]))) parse_str($c_array[$i]);
	
	if (strlen($order_by))
		$sel[$order_by] = "selected";
	else
		$sel[nothing] = "selected";

	$order_select = "<SELECT NAME=\"order_by\" onChange=\"submit()\" class=\"formselect\">";
	$order_select.= "<OPTION value=\"\" $sel[nothing]>- ".label("Select")." -</OPTION>";
	if ($show_name) $order_select.= "<OPTION value=\"c_name\" $sel[c_name]>".label("Company name")."</OPTION>";
	if ($show_person) $order_select.= "<OPTION value=\"c_person\" $sel[c_person]>".label("Name and lastname")."</option>";
	if ($show_username) $order_select.= "<OPTION value=\"c_username\" $sel[c_username]>".label("Username")."</option>";
	if ($show_group) $order_select.= "<OPTION value=\"c_email2\" $sel[c_email2]>".label("Group")."</option>";
	if ($show_addres) $order_select.= "<OPTION value=\"c_address\" $sel[c_address]>".label("Address")."</option>";
	if ($show_zip) $order_select.= "<OPTION value=\"c_zip\" $sel[c_zip]>".label("Zip code")."</option>";
	if ($show_city) $order_select.= "<OPTION value=\"c_city\" $sel[c_city]>".label("City")."</option>";
	if ($show_country) $order_select.= "<OPTION value=\"c_country\" $sel[c_country]>".label("Country")."</option>";
	if ($show_tel) $order_select.= "<OPTION value=\"c_tel\" $sel[c_tel]>".label("Telephone number")."</option>";
	if ($show_email) $order_select.= "<OPTION value=\"c_email\" $sel[c_email]>".label("Email address")."</option>";
	$order_select.= "</select>";
	
	$restrict_select = "<SELECT NAME=\"restrict\" onChange=\"submit()\" class=\"formrestrict\">";
	$restrict_select .= "<OPTION value=\"\" $rest[nothing]>- ".label("Select")." -</option>";

	$sql = "SELECT DISTINCT(c_email2) FROM crm_customer 
			WHERE c_server = $SERVER_ID ORDER BY c_email2";

	$res = $adodb->execute($sql);
	$rest[$restrict] = "selected";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$restrict_select .= "<OPTION value=\"$c_email2\" $rest[$c_email2]>grupa \"$c_email2\"</option>";
	}

	$restrict_select.= "</select>";

	if ($show_edit)
	{
		
		$cancel_button = "<INPUT TYPE=\"Button\" value=\"".label("Cancel")."\" onClick=\"goBack()\" class=\"formbutton\">";
		$mod_button = "<INPUT TYPE=\"Button\" value=\"".label("Save")."\" onClick=\"doRewrite(); validateCrmUser(document.crm_edit_form)\" class=\"formbutton\">";

		if (strlen($c_id))
		{
			$sql = "SELECT * FROM crm_customer
					WHERE c_id = $c_id
					AND c_server = $SERVER_ID";
			
			parse_str(ado_query2url($sql));

			$mod_button = "<INPUT TYPE=\"Button\" value=\"".label("Modifi")."\" class=\"formbutton\" onClick=\"doRewrite(); validateCrmUser(document.crm_edit_form)\">";

		}
		echo "<FORM METHOD=POST ACTION=\"$self\" name=\"crm_edit_form\">
				<INPUT TYPE=\"hidden\" value=\"$c_id\" name=\"c_id\">
				<INPUT TYPE=\"hidden\" value=\"1\" name=\"save_user\">
				<INPUT TYPE=\"hidden\" value=\"\" name=\"CRMUSER[c_email2]\" id=\"hidden_agenda\">
				<TABLE border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"formtable\">	
					<col class=\"formcol1\"><col class=\"formcol2\">
					<tr><td colspan=\"2\" class=\"formtitle\">".label("User profile")."</td></tr>";
					
		$validate_pass = 1;
		if ($edit_name) 	echo "<TR><TD class=\"formlabel\">".label("Company name")."".($validate_name?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_name\" NAME=\"CRMUSER[c_name]\" value=\"$c_name\"></TD></tr>";
		if ($edit_person) 	echo "<TR><TD class=\"formlabel\">".label("Name and lastname")."".($validate_person?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_person\" NAME=\"CRMUSER[c_person]\" value=\"$c_person\"></TD></tr>";
		if ($edit_addres)	echo "<TR><TD class=\"formlabel\">".label("Address")."".($validate_addres?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_address\" NAME=\"CRMUSER[c_address]\" value=\"$c_address\"></TD></tr>";
		if ($edit_zip) 		echo "<TR><TD class=\"formlabel\">".label("Zip code")."".($validate_zip?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_zip\" NAME=\"CRMUSER[c_zip]\" value=\"$c_zip\"></TD></tr>";
		if ($edit_city) 	echo "<TR><TD class=\"formlabel\">".label("City")."".($validate_city?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_city\" NAME=\"CRMUSER[c_city]\" value=\"$c_city\"></TD></tr>";
		if ($edit_country) 	echo "<TR><TD class=\"formlabel\">".label("Country")."".($validate_country?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_country\" NAME=\"CRMUSER[c_country]\" value=\"$c_country\"></TD></tr>";
		if ($edit_tel) 		echo "<TR><TD class=\"formlabel\">".label("Telephone number")."".($validate_tel?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_tel\" NAME=\"CRMUSER[c_tel]\" value=\"$c_tel\"></TD></tr>";
		if ($edit_email) 	echo "<TR><TD class=\"formlabel\">".label("Email address")."".($validate_email?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_email\" NAME=\"CRMUSER[c_email]\" value=\"$c_email\"></TD></tr>";
		if ($edit_username) echo "<TR><TD class=\"formlabel\">".label("Username")."".($validate_username?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_username\" NAME=\"CRMUSER[c_username]\" value=\"$c_username\"></TD></tr>";
		if ($edit_group)	echo "<TR><TD class=\"formlabel\">".label("Group")."".($validate_group?"(*)":"").":$lista $da_input</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"text\" class=\"forminput\" id=\"c_email2_input\" onBlur=\"rewriteInput()\" value=\"$c_email2\">$select_group</TD></tr>";
		if ($edit_pass) 	echo "<TR><TD class=\"formlabel\">".label("Password")."".($validate_pass?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"password\" class=\"forminput\" id=\"c_pass\" NAME=\"CRMUSER[c_pass]\" value=\"\"></TD></tr>
								<tr><TD class=\"formlabel\">".label("Confirm password")."".($validate_pass?"(*)":"").":</TD>
									<TD class=\"forminputcell\"><INPUT TYPE=\"password\" class=\"forminput\" id=\"c_pass2\" NAME=\"CRMUSER[c_pass2]\" value=\"\"></TD></tr>";
	
		echo "<TR><TD class=\"formbottom\" colspan=\"2\">$cancel_button $mod_button</TD></tr>
				</table></form>
				<FORM METHOD=POST ACTION=\"$self\" name=\"gobackform\">
				
				</FORM>
				";

		echo "
			<script>
				function goBack()
				{
					document.gobackform.submit();
				}
				function validateCrmUser(obj)
				{";
		
		if ($validate_name) echo " if (obj.c_name.value == '')
									{
										alert('Proszê podaæ nazwê firmy.');
										obj.c_name.focus();
										return;
									}";

		if ($validate_person) echo " if (obj.c_person.value == '')
									{
										alert('Proszê podaæ imiê i nazwisko.');
										obj.c_person.focus();
										return;
									}";

		if ($validate_addres) echo " if (obj.c_address.value == '')
									{
										alert('Proszê podaæ adres.');
										obj.c_address.focus();
										return;
									}";
		if ($validate_zip) echo " if (obj.c_zip.value == '')
									{
										alert('Proszê podaæ kod pocztowy.');
										obj.c_zip.focus();
										return;
									}";
		if ($validate_city) echo " if (obj.c_city.value == '')
									{
										alert('Proszê podaæ nazwê miejscowoœci.');
										obj.c_city.focus();
										return;
									}";
		if ($validate_country) echo " if (obj.c_country.value == '')
									{
										alert('Proszê podaæ kraj.');
										obj.c_country.focus();
										return;
									}";
		if ($validate_tel) echo " if (obj.c_tel.value == '')
									{
										alert('Proszê podaæ numer telefonu.');
										obj.c_tel.focus();
										return;
									}";
		if ($validate_email) echo " if (obj.c_email.value == '')
									{
										alert('Proszê podaæ email.');
										obj.c_email.focus();
										return;
									}";

		if ($validate_username) echo " if (obj.c_username.value == '')
									{
										alert('Proszê podaæ nazwê u¿ytkownika.');
										obj.c_username.focus();
										return;
									}";

/*
		if ($validate_group) echo " if (obj.c_email2.value == '')
									{
										alert('Proszê podaæ nazwê grupy.');
										obj.c_email2.focus();
										return;
									}";
*/

		if ($edit_pass) echo "\n if (obj.c_id.value == '')
								{
									if (obj.c_pass.value == '')
									{
										alert('Proszê podaæ has³o.');
										obj.c_pass.focus();
										return;
									} 
									else if (obj.c_pass.value != obj.c_pass2.value)
									{
										alert('Has³o niezgodne z potwierdzeniem.');
										obj.c_pass.focus();
										return;
									}
								}
								else if (obj.c_pass.value.length != 0)
								{
									if (obj.c_pass.value != obj.c_pass2.value)
									{
										alert('Has³o niezgodne z potwierdzeniem.');
										obj.c_pass.focus();
										return;
									}
								}
								";


		echo"
				obj.submit();
				}";
		if ($edit_group)
		{
			echo "
			document.crm_edit_form.c_email2_select.style.display = 'inline';
			document.crm_edit_form.c_email2_input.style.display = 'none';
			document.all['select_img'].style.display = 'none';
			document.all['input_img'].style.display = 'inline';

			";
		}

		echo "
			function reloadSelect()
			{
				if(document.crm_edit_form.c_email2_select.style.display == 'none')
				{
					document.crm_edit_form.c_email2_select.style.display = 'inline';
					document.crm_edit_form.c_email2_input.style.display = 'none';
					document.all['select_img'].style.display = 'none';
					document.all['input_img'].style.display = 'inline';
				} else
				{
					document.crm_edit_form.c_email2_select.style.display = 'none';
					document.crm_edit_form.c_email2_input.style.display = 'inline';
					document.all['select_img'].style.display = 'inline';
					document.all['input_img'].style.display = 'none';
				}
			}
			function rewriteSelect()
			{
				document.all['hidden_agenda'].value = document.all['c_email2_select'].value;
			}
			function rewriteInput()
			{
				document.all['hidden_agenda'].value = document.all['c_email2_input'].value;
			}
			
			function doRewrite()
			{
				if (document.crm_edit_form.c_email2_select.style.display == 'none')
				rewriteInput(); else rewriteSelect();

			}
			</script>
		";
		return;
	}

	if ($save_user)
	{
		if (strlen($c_id))
		{
			$sql = "UPDATE crm_customer SET nc_update = ".time().",";
			if ($edit_name) $sql.= " c_name = '$CRMUSER[c_name]',";
			if ($edit_person) $sql.= " c_person = '$CRMUSER[c_person]',";
			if ($edit_addres) $sql.= " c_address = '$CRMUSER[c_address]',";
			if ($edit_zip) $sql.= " c_zip = '$CRMUSER[c_zip]',";
			if ($edit_city) $sql.= " c_city = '$CRMUSER[c_city]',";
			if ($edit_country) $sql.= " c_country = '$CRMUSER[c_country]',";
			if ($edit_tel) $sql.= " c_tel = '$CRMUSER[c_tel]',";
			if ($edit_email) $sql.= " c_email = '$CRMUSER[c_email]',";
			if ($edit_username) $sql.= " c_username = '$CRMUSER[c_username]',";
			if ($edit_group) $sql.= " c_email2 = '$CRMUSER[c_email2]',";
			if ($edit_pass && strlen($CRMUSER[c_pass])) $sql.= "c_password = '$CRMUSER[c_pass]',";
			$sql = substr($sql,0,-1);
			$sql.= " WHERE c_id = $c_id AND c_server = $SERVER_ID";

		}
		else
		{
			if (!strlen($CRMUSER[c_email2])) $CRMUSER[c_email2] = 'dodani';

			$sql = "INSERT INTO crm_customer(nc_create,c_email2,c_server,";
			if ($edit_name) $sql.= "c_name,";
			if ($edit_person) $sql.= "c_person,";
			if ($edit_addres) $sql.= "c_address,";
			if ($edit_zip) $sql.= "c_zip,";
			if ($edit_city) $sql.= "c_city,";
			if ($edit_country) $sql.= "c_country,";
			if ($edit_tel) $sql.= "c_tel,";
			if ($edit_email) $sql.= "c_email,";
			if ($edit_username) $sql.= "c_username,";
//			if ($edit_group) $sql.= "c_email2,";
			if ($edit_pass) $sql.= "c_password,";
			$sql = substr($sql,0,-1);
			$sql.=") VALUES (".time().",'$CRMUSER[c_email2]',$SERVER_ID,";
			if ($edit_name) $sql.= "'$CRMUSER[c_name]',";
			if ($edit_person) $sql.= "'$CRMUSER[c_person]',";
			if ($edit_addres) $sql.= "'$CRMUSER[c_address]',";
			if ($edit_zip) $sql.= "'$CRMUSER[c_zip]',";
			if ($edit_city) $sql.= "'$CRMUSER[c_city]',";
			if ($edit_country) $sql.= "'$CRMUSER[c_country]',";
			if ($edit_tel) $sql.= "'$CRMUSER[c_tel]',";
			if ($edit_email) $sql.= "'$CRMUSER[c_email]',";
			if ($edit_username) $sql.= "'$CRMUSER[c_username]',";
//			if ($edit_group) $sql.= "'$CRMUSER[c_email2]',";
			if ($edit_pass) $sql.= "'$CRMUSER[c_pass]',";
			$sql = substr($sql,0,-1);
			$sql.= ")";

		}
		$adodb->debug=0;
		$adodb->execute($sql);
		$adodb->debug=0;
	}
	if ($kill_user && strlen($c_id))
	{
		$sql = "DELETE FROM crm_customer
				WHERE c_server = $SERVER_ID
				AND c_id = $c_id";

		$adodb->execute($sql);
	}

	echo "<TABLE border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"formtable\">
			<col class=\"formcol1\"><col class=\"formcol2\">";
	if ($sort_ok)
	echo "<FORM METHOD=POST ACTION=\"$self\">
			<INPUT TYPE=\"hidden\" name=\"restrict\" value=\"$restrict\">
			<tr><td class=\"formlabel\">".label("Sort by").":</td>
				<td class=\"forminputcell\">$order_select</td></tr></FORM>";

	if ($restrict_ok)
	echo "<FORM METHOD=POST ACTION=\"$self\">
			<INPUT TYPE=\"hidden\" name=\"sort_by\" value=\"$sort_by\">
			<tr><td class=\"formlabel\">".label("Restrict to").":</td>
				<td class=\"forminputcell\">$restrict_select</td></tr></FORM>";

	if ($add_ok) echo"
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"show_edit\" value=\"1\">
			<tr><td class=\"formlabel\">".label("Add user").":</td>
				<td class=\"forminputcell\">
				<INPUT TYPE=\"submit\" value=\"".label("Add user")."\" class=\"formbutton\"></td></tr>
	</FORM>";

	echo "</table><br>";

	if (!strlen($order_by)) $order_by = "c_person";

	$limit = 20;
	if (strlen($size) && $size<>0) 	$limit = $size;

	if (!$ile)
	{
		$sql = "SELECT COUNT(*) AS ile FROM crm_customer
				WHERE c_server = $SERVER_ID";
		if (strlen($restrict)) $sql.= " AND c_email2 = '$restrict' ";
	
		parse_str(ado_query2url($sql));
		$start=0;
	}
	
	$offset=$start;			


	$sql = "SELECT * FROM crm_customer
			WHERE c_server = $SERVER_ID";
	if (strlen($restrict)) $sql.= " AND c_email2 = '$restrict' ";
	if (strlen($order_by)) $sql.=" ORDER BY $order_by ";
	$sql.=	"LIMIT $limit OFFSET $offset";
	
	$res = $adodb->execute($sql);

	if ($res->RecordCount() > 0)
	{
		if ($ile)
		  echo "
			<table width=\"100%\">
			<tr>
				<td style=\"padding: 2px 0px 7px 0px;\"><b>Znaleziono $ile wpisów.</b></td>
			</tr>
			</table>";

		$href="$self${znak}parametr=$parametr&order_by=$order_by&restrict=$restrict";
		$nawigacja=naviIndex($href,$start,$offset,$ile,$limit);
		echo "<table width=100%><tr><td>$nawigacja</td></tr></table>";

		echo "<TABLE border=\"1\" cellspacing=\"0\" cellpading=\"0\" class=\"tabletable\" width=\"100%\"><TR class=\"tabletitletr\">";
		if ($show_lp) 		echo "<TD class=\"tabletitle\">".label("Lp").".</TD>";
		if ($show_name) 	echo "<TD class=\"tabletitle\">".label("Company name")."</TD>";
		if ($show_person) 	echo "<TD class=\"tabletitle\">".label("Name and lastname")."</TD>";
		if ($show_username)	echo "<TD class=\"tabletitle\">".label("Username")."</TD>";
		if ($show_group)	echo "<TD class=\"tabletitle\">".label("Group")."</TD>";
		if ($show_addres)	echo "<TD class=\"tabletitle\">".label("Address")."</TD>";
		if ($show_zip)		echo "<TD class=\"tabletitle\">".label("Zip code")."</TD>";
		if ($show_city)		echo "<TD class=\"tabletitle\">".label("City")."</TD>";
		if ($show_country)	echo "<TD class=\"tabletitle\">".label("Country")."</TD>";
		if ($show_tel)		echo "<TD class=\"tabletitle\">".label("Telephone number")."</TD>";
		if ($show_email)	echo "<TD class=\"tabletitle\">".label("Email address")."</TD>";
		if ($edit_ok || $delete_ok) echo "<TD class=\"tabletitle\" width=\"1%\" align=\"center\">".label("actions")."</TD>";
		echo "</tr>";
	}


	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		if ($edit_ok) $edit_button = "<A HREF=\"$self${next_char}show_edit=1&c_id=$c_id\"><img src=\"img/i_property_n.gif\" border=\"0\" alt=\"edytuj\"></A>";
		if ($delete_ok) $delete_button = "<A HREF=\"javascript:deleteUser('$c_id')\"><img src=\"img/i_delete_n.gif\" border=\"0\" alt=\"usuñ\"></A>";

		if (!$c_name) 		$c_name		= "&nbsp;";
		if (!$c_person) 	$c_person	= "&nbsp;";
		if (!$c_username)	$c_username	= "&nbsp;";
		if (!$c_email2)		$c_email2	= "&nbsp;";
		if (!$c_address) 	$c_address	= "&nbsp;";
		if (!$c_zip) 		$c_zip		= "&nbsp;";
		if (!$c_city) 		$c_city		= "&nbsp;";
		if (!$c_country) 	$c_country	= "&nbsp;";
		if (!$c_tel) 		$c_tel		= "&nbsp;";
		if (!$c_email) 		$c_email	= "&nbsp;";

		$nr = $i+1;
		echo "<TR class=\"tabletr\">";
			if ($show_lp) 		echo "<TD class=\"tabletd\">$nr</TD>";
			if ($show_name) 	echo "<TD class=\"tabletd\">$c_name</TD>";
			if ($show_person) 	echo "<TD class=\"tabletd\">$c_person</TD>";
			if ($show_username) echo "<TD class=\"tabletd\">$c_username</TD>";
			if ($show_group)	echo "<TD class=\"tabletd\">$c_email2</TD>";
			if ($show_addres) 	echo "<TD class=\"tabletd\">$c_address</TD>";
			if ($show_zip) 		echo "<TD class=\"tabletd\">$c_zip</TD>";
			if ($show_city) 	echo "<TD class=\"tabletd\">$c_city</TD>";
			if ($show_country) 	echo "<TD class=\"tabletd\">$c_country</TD>";
			if ($show_tel) 		echo "<TD class=\"tabletd\">$c_tel</TD>";
			if ($show_email) 	echo "<TD class=\"tabletd\"><A HREF=\"mailto:$c_email\">$c_email</A></TD>";
			if ($edit_ok || $delete_ok) echo "<TD class=\"tabletd\">$edit_button$delete_button</TD>";
		echo "</tr>";

	}
	
	echo "</table>";

	if ($delete_ok)
		echo "
		<FORM METHOD=POST ACTION=\"$self\" name=\"killform\">
		<INPUT TYPE=\"hidden\" NAME=\"kill_user\" value=\"1\">		
		<INPUT TYPE=\"hidden\" NAME=\"c_id\" value=\"\">		
		</FORM>
		";
?>

<SCRIPT>
	function deleteUser(id)
	{
		if (confirm('<? echo "Na pewno chcesz usun¹æ tego u¿ytkownika ?" ?>'))
		{
			document.killform.c_id.value = id;
			document.killform.submit();
		}

	}

</SCRIPT>
