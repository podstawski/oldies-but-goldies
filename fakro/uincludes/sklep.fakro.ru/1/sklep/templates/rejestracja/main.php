<?
	parse_str($costxt);
	
	$SKLEP_SESSION["sg"]=$sg;
	session_register("sg");

	$goto=$_REQUEST[goto];
	
	if ($AUTH[id] > 0)
		$akcja = "OsobaZmienDane";
	else
		$akcja = "OsobaRejestruj";

	$checkpass = 1;
	if ($akcja == "OsobaZmienDane")
	{
		$sql = "SELECT 
				su_parent,
				su_login,
				su_imiona,
				su_nazwisko,
				su_email,
				su_gsm
				FROM system_user WHERE su_id = ".$AUTH[id];
		parse_str(ado_query2url($sql));

		$sql = "SELECT 
				su_pesel,
				su_ulica,
				su_kod_pocztowy,
				su_miasto,
				su_telefon,
				su_nip,
				su_adres1,
				su_adres2,
				su_adres3,
				su_wyroznik1,
				su_wyroznik2,
				su_wyroznik3,
				su_nazwisko AS nazwa 
				FROM system_user WHERE su_id = ".$su_parent;
		parse_str(ado_query2url($sql));		
		$checkpass = 0;
	}

	$osoby_fields_firma = sysmsg("Company","user");
	$osoby_fields_imiona = sysmsg("Forename","user");
	$osoby_fields_nazwisko = sysmsg("Surname","user");
	$osoby_fields_plec = sysmsg("Sex","user");

	$osoby_fields_login = sysmsg("Login","user");
	$osoby_fields_pass = sysmsg("Password","user");
	$osoby_fields_pass_confirm = sysmsg("Password confirm","user");
	$osoby_fields_pesel = sysmsg("Identyfication number","user");

	$osoby_fields_ulica = sysmsg("Street","user");
	$osoby_fields_kod_pocztowy = sysmsg("Zip code","user");
	$osoby_fields_miasto = sysmsg("City","user");
	
	$osoby_fields_dostawa = sysmsg("Delivery","user");

	$osoby_fields_telefon = sysmsg("Telephone","user");
	$osoby_fields_gsm = sysmsg("Fax","user");
	$osoby_fields_email = sysmsg("Email","user");

	$osoby_fields_nip = sysmsg("Tax Id","user");

	$sysmsg_no_su_imiona = sysmsg("Please insert Your name","user");
	$sysmsg_no_su_nazwisko = sysmsg("Please insert Your surname","user");
	$sysmsg_no_su_pass = sysmsg("Please insert password","user");	
	$sysmsg_no_su_pass_confirm = sysmsg("Password confirm not match","user");	
	$sysmsg_no_su_nip = sysmsg("Please insert Tax Id","user");	
	$sysmsg_no_su_ulica = sysmsg("Please insert street name","user");	
	$sysmsg_no_su_kod_pocztowy = sysmsg("Please insert zip code","user");	
	$sysmsg_no_su_miasto = sysmsg("Please insert city name","user");	
	$sysmsg_no_su_adres1 = sysmsg("Please insert delivery address","user");	
	$sysmsg_no_su_telefon = sysmsg("Please insert telephone number","user");	
	$sysmsg_no_su_gsm = sysmsg("Please insert cellphone number","user");	
	$sysmsg_no_su_email = sysmsg("Please insert email address","user");	

	$commit = $cos;

	if ($SYSTEM[auth] == 'login')
	{
		$checklogin = 1;
		$checkemail = 0;
		$pass_display = "none";
		$login_display = "inline";
		if ($akcja == "OsobaZmienDane")
		{
			$login_type = "hidden";
			$_su_login = $su_login;
		}
	}
	else
	{
		$checklogin = 0;
		$checkemail = 1;
		$pass_display = "inline";
		$login_display = "none";
		if ($akcja == "OsobaZmienDane")
		{
			$email_type = "hidden";
			$_su_email = $su_email;
		}
	}

	$su_firma = $nazwa;
	if (($commit && !$FORM[do_commit]) || (!$commit))
	{
		$input_type = "text";
		$input_type_pass = "password";
		$aenable = "";
		$henable = "disabled";
		$areastyle = "display:inline";

		$where = $next;
		if ($commit)
			$akcja = "";
	}
	else if ($commit && $FORM[do_commit])
	{

		$aenable = "disabled";
		$henable = "";
		$areastyle = "display:none";
		$input_type = "hidden";
		$login_type = "hidden";
		$email_type = "hidden";
		$pass_display = "none";
		$input_type_pass = "hidden";
		
		$where = $next;

		if (strlen($goto))
		{
			$err_log_req = "inline";
			$where = $goto;
		}

		$no_check = 1;

		$_su_firma = $FORM[su_firma];
		$_su_login = $FORM[su_login];
		$_su_pass = "******";
		$_su_pesel = $FORM[su_pesel];
		$_su_imiona = $FORM[su_imiona];
		$_su_nazwisko = $FORM[su_nazwisko];
		$_su_ulica = $FORM[su_ulica];
		$_su_kod_pocztowy = $FORM[su_kod_pocztowy];
		$_su_miasto = $FORM[su_miasto];
		$_su_telefon = $FORM[su_telefon];
		$_su_gsm = $FORM[su_gsm];
		$_su_email = $FORM[su_email];
		$_su_nip = $FORM[su_nip];
		$_su_adres1 = $FORM[su_adres1];

		$su_firma = $FORM[su_firma];
		$su_login = $FORM[su_login];
		$su_pass = $FORM[su_pass];
		$su_pesel = $FORM[su_pesel];
		$su_imiona = $FORM[su_imiona];
		$su_nazwisko = $FORM[su_nazwisko];
		$su_ulica = $FORM[su_ulica];
		$su_kod_pocztowy = $FORM[su_kod_pocztowy];
		$su_miasto = $FORM[su_miasto];
		$su_telefon = $FORM[su_telefon];
		$su_gsm = $FORM[su_gsm];
		$su_email = $FORM[su_email];
		$su_nip = $FORM[su_nip];
		$su_adres1 = $FORM[su_adres1];
	}

?>
