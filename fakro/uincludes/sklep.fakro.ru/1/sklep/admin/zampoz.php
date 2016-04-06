<?
	$za_id=$_COOKIE[za_id];

	if (!$za_id) return;

	$sql="SELECT * FROM zampoz,towar,towar_sklep WHERE zp_za_id=$za_id AND zp_ts_id=ts_id AND ts_to_id=to_id";
	$res = $projdb->execute($sql);

	echo "<table cellpadding=6>";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		echo "<tr><td title=\"$to_nazwa\">$to_indeks";
		$cena=u_cena($zp_cena);
		echo "<td><a href=\"javascript:zampoz_zmiana($zp_id,'zp_ilosc','Podaj iloЖц w $to_jm',$zp_ilosc)\">| &nbsp; $zp_ilosc $to_jm</a>";
		echo "<td><a href=\"javascript:zampoz_zmiana($zp_id,'zp_cena','Podaj cenъ netto',$zp_cena)\">| &nbsp; za $cena</a>";
	}
	echo "</table>";

?>

<form name="zampoz_zmiana_form" method="post" action="<?echo $self?>">
	<input type="hidden"  name="action" value="AdminZampoz">
	<input type="hidden" id="zp_id" name="form[zp_id]" value="">
	<input type="hidden" id="pole" name="form[zp]" value="">
	<input type="hidden" id="wart" name="form[wart]" value="">

</form>

<script>
	function zampoz_zmiana(zp_id,pole,label,wart)
	{
		wart=prompt(label,wart);
		if (wart==null) return;

		document.zampoz_zmiana_form.zp_id.value=zp_id;
		document.zampoz_zmiana_form.pole.value=pole;
		document.zampoz_zmiana_form.wart.value=wart;
		document.zampoz_zmiana_form.submit();

	}

</script>
