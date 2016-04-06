<?
	$w_cosiu = explode(";",$costxt);
	$maile = explode(",",$w_cosiu[0]);

	$regiony.="<option value=\"\">Wybierz z listy</option>";
	for ($i = 1; $i < count($w_cosiu); $i++)
		$regiony.="<option value=\"".$w_cosiu[$i]."\">".$w_cosiu[$i]."</option>";

	$dot = "*";
	$table = "
	<FORM METHOD=POST ACTION=\"$next\" onSubmit=\"return validateForm(this)\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"OfertaWyslij\">
	<INPUT TYPE=\"hidden\" name=\"form[mailto]\" value=\"".$w_cosiu[0]."\">
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>
		<Th colspan=2>".sysmsg("Prepare offer to send","cart")."</Th>
	</TR>
	</thead>
	<tbody>
	<TR>
		<Td class=\"c2\">".sysmsg("email","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[email]\" id=\"email\" style=\"width:200px\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("firm name","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[firma]\" id=\"firma\" style=\"width:200px\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("address","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[adres]\" id=\"adres\" style=\"width:250px\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("zip code","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[kod]\" id=\"kod\" size=8 style=\"width:50px\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("city","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[miasto]\" id=\"miasto\" style=\"width:200px\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("phone","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tel]\" id=\"tel\"></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("person","system").":$dot</Td>
		<Td class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[osoba]\" id=\"osoba\" style=\"width:200px\"></Td>
	</TR>
	<TR>
		<Td valign=\"top\" class=\"c2\">".sysmsg("notice","system").":</Td>
		<Td class=\"c4\"><TEXTAREA NAME=\"form[uwagi]\" style=\"width:300px;height:100px\"></TEXTAREA></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("area","system").":</Td>
		<Td class=\"c4\"><SELECT NAME=\"form[region]\" style=\"width:200px\">$regiony</SELECT></Td>
	</TR>
	</tbody>
	<tfoot>
	<TR>
		<Td class=\"c2\"><INPUT TYPE=\"Button\" value=\"".sysmsg("Cancel","system")."\"></Td>
		<Td class=\"c4\"><INPUT TYPE=\"submit\" value=\"".sysmsg("Submit","system")."\"></Td>
	</tr>
	</tfoot>
	</TABLE>
	</FORM>";

	echo $table;
?>
<script>
	
	function checkEmail(obj)
	{
		re = new RegExp("[a-z|A-Z|0-9|\.|\-|\_]+@[a-z|A-Z|0-9|\.|\-|\_]+");

		if (!re.test(obj.value))
		{
			obj.focus();
			return false;
		}
		return true;
	}

	function validateForm(obj)
	{
		if (obj.email.value == '' || !checkEmail(obj.email))
		{
			alert('<? echo sysmsg("email is not valid","system")?>');
			obj.email.focus();
			return false;
		}
		if (obj.firma.value == '')
		{
			alert('<? echo sysmsg("firm name is not valid","system")?>');
			obj.firma.focus();
			return false;
		}
		if (obj.adres.value == '')
		{
			alert('<? echo sysmsg("address is not valid","system")?>');
			obj.adres.focus();
			return false;
		}
		if (obj.kod.value == '')
		{
			alert('<? echo sysmsg("zip code is not valid","system")?>');
			obj.kod.focus();
			return false;
		}
		if (obj.miasto.value == '')
		{
			alert('<? echo sysmsg("city is not valid","system")?>');
			obj.miasto.focus();
			return false;
		}
		if (obj.tel.value == '')
		{
			alert('<? echo sysmsg("phone is not valid","system")?>');
			obj.tel.focus();
			return false;
		}
		if (obj.osoba.value == '')
		{
			alert('<? echo sysmsg("person is not valid","system")?>');
			obj.osoba.focus();
			return false;
		}

		return true;
	}
</script>
