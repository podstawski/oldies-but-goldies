<?
	global $_REQUEST;

	$TOWAR_SKLEP_WHERE="";
	$ZAMOWIENIE_WHERE="";
	$KONTRAHENT_WHERE="";

	echo "<B>Zakres czasowy:</B> ".humandate($od)." - ".humandate($do)."<br>";

	if ($_REQUEST[sklep_id])
	{
		$query="SELECT * FROM sklep WHERE sk_id=$_REQUEST[sklep_id]";
		parse_str(ado_query2url($query));

		echo "<B>Sklep:</B> $sk_nazwa<br>";

		$TOWAR_SKLEP_WHERE.=" AND ts_sk_id=$sk_id";
		$ZAMOWIENIE_WHERE.=" AND za_sk_id=$sk_id";
	}

	if (strlen($_REQUEST[su_wyroznik1]))
	{
		echo "<B>".sysmsg('crm_title_1','crm').":</B> ".sysmsg('wyroznik1_'.$_REQUEST[su_wyroznik1],'crm')."<br>";
		$KONTRAHENT_WHERE.=" AND su_wyroznik1='$_REQUEST[su_wyroznik1]'";
	}
	
	if (strlen($_REQUEST[su_wyroznik2]))
	{
		echo "<B>".sysmsg('crm_title_2','crm').":</B> ".sysmsg('wyroznik2_'.$_REQUEST[su_wyroznik2],'crm')."<br>";

		$KONTRAHENT_WHERE.=" AND su_wyroznik2='$_REQUEST[su_wyroznik2]'";
	}
	
	if (strlen($_REQUEST[su_wyroznik3]))
	{
		echo "<B>".sysmsg('crm_title_3','crm').":</B> ".sysmsg('wyroznik3_'.$_REQUEST[su_wyroznik3],'crm')."<br>";
		$KONTRAHENT_WHERE.=" AND su_wyroznik3='$_REQUEST[su_wyroznik3]'";
	}

	if ($_REQUEST[su_opiekun])
	{
		$query="SELECT su_nazwisko,su_imiona FROM system_user WHERE su_id=$_REQUEST[su_opiekun]";
		parse_str(ado_query2url($query));
		echo "<B>Opiekun kontrahenta:</B> $su_imiona $su_nazwisko<br>";
		
		$KONTRAHENT_WHERE.=" AND su_opiekun='$_REQUEST[su_opiekun]'";
	}
	

	$statusy_cookie=$_REQUEST[statusy];
	if (is_array($statusy_cookie))
	{
		$statusy="-123";
		echo "<B>Statusy zamówienia:</B> ";
		while(list($s,$v)=each($statusy_cookie))
		{
			if (!$v) continue;
			$statusy.=",$s";
			echo "/ ".sysmsg("status_$s","status");

		}
		$ZAMOWIENIE_WHERE.=" AND za_status IN ($statusy)";
		echo "<br>";
	}
	
?>
