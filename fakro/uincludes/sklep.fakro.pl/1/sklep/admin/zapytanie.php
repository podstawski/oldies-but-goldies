<?
	if (!$LIST[id]) return;
	$query="SELECT * FROM zapytania WHERE za_id=".$LIST[id];
	parse_str(ado_query2url($query));
	if (!$za_id) return;

	$query="SELECT * FROM towar,towar_sklep WHERE ts_to_id=to_id AND ts_id=$za_ts_id";
	parse_str(ado_query2url($query));
/*
	echo "<B>OD: $za_email, tel. $za_telefon</B><br>";
	echo "<i>".date("d-m-Y H:i",$za_pyt_data)."</i>";
	echo "<hr>";
	echo "<B>$to_nazwa [$to_indeks]</B><hr>";
	echo nl2br($za_pyt);
*/	
	echo "
	<TABLE>
	<TR>
		<TD><B>Od:</b></TD>
		<TD>$za_email (tel. $za_telefon)</TD>
	</TR>
	<TR>
		<TD><B>Data:</B></TD>
		<TD>".date("d-m-Y H:i",$za_pyt_data)."</TD>
	</TR>
	<TR>
		<TD><b>Temat:</b></TD>
		<TD>$to_nazwa [$to_indeks]</TD>
	</TR>
	<TR>
		<TD><b>Treść:</b></TD>
		<TD>".nl2br($za_pyt)."</TD>
	</TR>
	</TABLE>
	";
	
	echo "<hr><B>Odpowiedź:</B><br>";

	if ($za_odp_data)
	{
		$query="SELECT * FROM system_user WHERE su_id=$za_odp_su_id";
		parse_str(ado_query2url($query));

/*
		echo "<i>$su_imiona $su_nazwisko, ".date("d-m-Y H:i",$za_odp_data)."</i>";
		echo "<hr>";
		echo nl2br($za_odp);
		echo "<hr>";
*/
		echo "
		<TABLE>
		<TR>
			<TD><B>Osoba:</b></TD>
			<TD>$su_imiona $su_nazwisko</TD>
		</TR>
		<TR>
			<TD><B>Data:</B></TD>
			<TD>".date("d-m-Y H:i",$za_odp_data)."</TD>
		</TR>
		<TR>
			<TD><b>Treść:</b></TD>
			<TD>".nl2br($za_odp)."</TD>
		</TR>
		</TABLE>
		";

		$qs=sort_navi_qs($LIST);
		echo "<input type=\"button\" class=\"button\" value=\"Powrót\" 
				onClick=\"location.href='$next${next_char}$qs'\">";
		return;
	}
//	echo "<hr>";

?>

<script language="JScript">
    function eweLoad()
	{
		var ewe = new EWE('<?echo $SKLEP_INCLUDE_PATH; ?>/ewe/source/ewe_langPL.xml');
		ewe.load(document.all.eweContainer,'');
	}
    var editorPath = '<?echo $SKLEP_INCLUDE_PATH; ?>/ewe/';
</script>
<script language="JScript" src="<?echo $SKLEP_INCLUDE_PATH; ?>/ewe/source/ewe.js"></script>

<form method="post" id="opisSave" action="<?echo $next?>">
<input type="checkbox" name="form[za_email]" value="1" checked> E-mail
<input type="hidden" name="action" value="ZapytanieOdpowiedz">
<input type="hidden" name="form[za_id]" value="<?echo $LIST[id]?>">
<textarea id="HTMLContent" name="form[za_odp]" style="visibility:hidden"></textarea>

</form>

<div id="eweContainer" unselectable="on"></div>

<script language="JScript" src="<?echo $SKLEP_INCLUDE_PATH?>/js/eweload.js" defer="defer"></script>

<script language="JScript">

	function sampleSave()
	{
		if (ViewCurrent == 2) toggleView();
		document.forms['opisSave'].HTMLContent.value = cleanup(document.all.ewe.innerHTML);
		document.forms['opisSave'].submit();
  }
</script>

