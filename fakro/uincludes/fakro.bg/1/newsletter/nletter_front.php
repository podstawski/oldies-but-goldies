<?

		$ret = "<img src=\"$IMAGES/font/font.php?";
		$ret.= "gtitle=".urlencode($WEBTD->title)."&"; 
		$ret.= "fontsize=12&";
		$ret.= "fontcolor=".$col;
		$ret.= "\">";

		if ($lang=='r') 
			$ret = "<h1 style=\"display:inline !important\">".$WEBTD->title."</h1>";

		$zap = array("i"=>"zapisz","f"=>"noter","d"=>"einschreiben","r"=>"зaп'иcывaть","e"=>"sign up");
		$wyp = array("i"=>"wypisz","f"=>"щcrire","d"=>"ausschreiben","r"=>"исключ'ить","e"=>"sign out");
		$zapisz = $zap[$lang];
		$wypisz = $wyp[$lang];



		parse_str($costxt);
		$form = "
		<FORM METHOD=POST ACTION=\"$self\" name=\"nlregform\" onSubmit=\"return wyslij()\">
		<TABLE  border=\"0\" class=\"nls\" cellpadding=\"0\" cellspacing=\"0\">
		<INPUT TYPE=\"hidden\" id=\"subname\" value=\"Nletter\">
		<INPUT TYPE=\"hidden\" name=\"ACTION_VAR\" value=\"NLFRONT\">
		<thead>
		<TR>
			<td >$ret</td>
			<td class=\"but\"><INPUT TYPE=\"image\" onClick=\"wyslij()\" src=\"".$IMAGES."/news.gif\" ></td>
		<tr>
		</thead>
			
			<td></td>
			<td align=\"right\"><INPUT TYPE=\"hidden\" NAME=\"NLFRONT[whattodo]\" id=\"whattodo\" value=\"\">
				<table  cellspacing=\"0\" cellpadding=\"0\" class=\"rad\" border=\"0\">
					<tr>
						<td><INPUT TYPE=\"text\" id=\"adres_email\" NAME=\"NLFRONT[adres_email]\" class=\"forminput\" value=\"e-mail\" onFocus=\"if (this.value=='e-mail') this.value = ''\" onBlur=\"if (this.value=='') this.value='e-mail'\">
						
						<INPUT TYPE=\"hidden\" NAME=\"NLFRONT[nl_grupa]\" value=\"$grupa\"></td>
						<td onclick=\"zapisz()\" style=\"cursor:pointer\"><INPUT TYPE=\"radio\" name=\"where\" id=\"zap\" checked value=\"\" onclick=\"zapisz()\"> $zapisz</td>
						<td onclick=\"wypisz()\" style=\"cursor:pointer\"><INPUT TYPE=\"radio\" name=\"where\" id=\"wyp\" onclick=\"wypisz()\" value=\"\"> $wypisz</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
		</table>
		
		</FORM>		
		";
//	}	
	$c1 = "Proszъ podaц adres email";
	$c2 = "Proszъ podaц prawidГowy adres email";

	echo "<A NAME=\"nlForm\"></A>";
	echo $form;

	echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
	<!--
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
	
	function wyslij()
	{
		if (document.nlregform.zap.checked)
			return zapisz();
		else
			return wypisz();
	}

	function wypisz()
	{
		if (document.nlregform.adres_email.value == '')
		{
			alert('$c1');
			document.nlregform.adres_email.focus();
			return false;
		}

		if (!isEmailAddr(document.nlregform.adres_email.value))
		{
			alert('$c2');
			document.nlregform.adres_email.focus();
			return false;
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
			return false;
		}
		if (!isEmailAddr(document.nlregform.adres_email.value))
		{
			alert('$c2');
			document.nlregform.adres_email.focus();
			return false;
		}
		
		document.nlregform.action = '$next';
		document.nlregform.subname.name = 'action';
		document.nlregform.whattodo.value = 'in';
		document.nlregform.submit();
	}
	//-->
	</SCRIPT>
	";

?>

