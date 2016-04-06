<?
	global $SERVER_ID;

//	$sql = "DELETE FROM crm_customer WHERE c_server = $SERVER_ID";
	
//	$adodb->debug=1;
//	$adodb->execute($sql);
//	$adodb->debug=0;

	if (strlen($costxt)) 
	{
		$tab = explode(";",$costxt);
		$grupa = $tab[0];
		$wypisz_from = $tab[1];
		$outpage = $tab[2];
		$szablon_page = $tab[3];
		$nl_info = $tab[4];
		$inpage = $tab[5];
		$szablon_in = $tab[6];
		$host_addres = $tab[7];
	} else return;

		$form = "
		<FORM METHOD=POST ACTION=\"$self\" name=\"nlregform\">
		<TABLE width=\"100%\">
		<INPUT TYPE=\"hidden\" id=\"subname\" value=\"Nletter\">
		<INPUT TYPE=\"hidden\" name=\"ACTION_VAR\" value=\"NLFRONT\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[wypisz_from]\" value=\"$wypisz_from\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[outpage]\" value=\"$outpage\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[szablon_page]\" value=\"$szablon_page\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[nl_info]\" value=\"$nl_info\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[inpage]\" value=\"$inpage\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[szablon_in]\" value=\"$szablon_in\">
		<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[host_addres]\" value=\"$host_addres\">
		<TR>
			<TD align=\"center\"><INPUT TYPE=\"text\" id=\"adres_email\" NAME=\"NLFRONT[adres_email]\" class=\"forminput\">
			<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[nl_grupa]\" value=\"$grupa\">
			<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[whattodo]\" id=\"whattodo\" value=\"\">
			</TD>
		</TR>
		<TR>
			<TD style=\"font-size:9px;font-face:verdana\">
			$nl_info
			</TD>
		</TR>
		</TABLE>
		<TABLE width=\"100%\">
		<TR>
			<TD align=\"center\"><INPUT TYPE=\"button\" value=\"".label("sign out")."\" class=\"nlformbutton\" style=\"width:60px\" onclick=\"wypisz()\"></TD>
			<TD align=\"center\"><INPUT TYPE=\"button\" value=\"".label("sign in")."\" class=\"nlformbutton\" style=\"width:60px\" onclick=\"zapisz()\"></TD>
		</TR>
		</TABLE>
		</FORM>		
		";
//	}	
	$c1 = label("Proszê podaæ adres email");
	$c2 = label("Proszê podaæ prawid³owy adres email");

	echo "<A NAME=\"nlForm\"></A>";
	echo $form;

	$JScript.= "
	function isEmailAddr(email) 
	{
		var result = false;
		var theStr = new String(email);
		var index = theStr.indexOf('@');
		if (index > 0)
		{
			var pindex = theStr.indexOf('.',index);
			if ((pindex > index+1) && (theStr.length > pindex+1))
	        result = true;
		}
		return result;
	}

	function wypisz()
	{
		if (document.nlregform.adres_email.value == '')
		{
			alert('$c1');
			document.nlregform.adres_email.focus();
			return;
		}

		if (!isEmailAddr(document.nlregform.adres_email.value))
		{
			alert('$c2');
			document.nlregform.adres_email.focus();
			return;
		}
		
		document.nlregform.action = '$more';
		document.nlregform.subname.name = 'action';
		document.nlregform.whattodo.value = 'out';
		document.nlregform.submit();
	}

	function zapisz()
	{
		if (document.nlregform.adres_email.value == '')
		{
			alert('$c1');
			document.nlregform.adres_email.focus();
			return;
		}
		if (!isEmailAddr(document.nlregform.adres_email.value))
		{
			alert('$c2');
			document.nlregform.adres_email.focus();
			return;
		}
		
		document.nlregform.action = '$next';
		document.nlregform.subname.name = 'action';
		document.nlregform.whattodo.value = 'in';
		document.nlregform.submit();
	}
	";

?>

