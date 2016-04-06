<?
	global $_REQUEST;


	if (!$_REQUEST[sklep_id]) 
	{
		$_REQUEST[sklep_id]=$SKLEP_ID;
		echo "<script> document.cookie='sklep_id=$SKLEP_ID';</script>";
	}
	$query="SELECT * FROM sklep";
	$res=$projdb->execute($query);
	for($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));

		
		$sel=($_REQUEST[sklep_id]==$sk_id)?"checked":"";
		echo "<input type='radio' name=\"sklep_id\" $sel value='$sk_id'
					onclick=\"if (this.checked) document.cookie=this.name+'='+this.value\"> $sk_nazwa<br>";
	}





	$query="SELECT su_wyroznik1 FROM system_user  WHERE su_wyroznik1<>'' GROUP BY su_wyroznik1";
	$res=$projdb->execute($query);
	$options="";
	for($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));

		$sel=($_REQUEST[su_wyroznik1]==$su_wyroznik1)?"selected":"";
		$opis=sysmsg("wyroznik1_$su_wyroznik1","crm");
		$options.="\n<option value='$su_wyroznik1' $sel>$opis</option>";
	}

	
	echo "<select name=\"su_wyroznik1\" class=\"form_select\" onchange=\"document.cookie=this.name+'='+this.value\">
			<option value=\"\">Bez ograniczeñ</option>
			$options
		</select>";


	$query="SELECT su_wyroznik2 FROM system_user WHERE su_wyroznik2<>'' GROUP BY su_wyroznik2";
	$res=$projdb->execute($query);
	$options="";
	for($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));

		$sel=($_REQUEST[su_wyroznik2]==$su_wyroznik2)?"selected":"";
		$opis=sysmsg("wyroznik2_$su_wyroznik2","crm");
		$options.="\n<option value='$su_wyroznik2' $sel>$opis</option>";
	}

	
	echo " <select name=\"su_wyroznik2\" class=\"form_select\" onchange=\"document.cookie=this.name+'='+this.value\">
			<option value=\"\">Bez ograniczeñ</option>
			$options
		</select>";

	$query="SELECT su_wyroznik3 FROM system_user WHERE su_wyroznik3<>'' GROUP BY su_wyroznik3";
	$res=$projdb->execute($query);
	$options="";
	for($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));

		$sel=($_REQUEST[su_wyroznik3]==$su_wyroznik3)?"selected":"";
		$opis=sysmsg("wyroznik3_$su_wyroznik3","crm");
		$options.="\n<option value='$su_wyroznik3' $sel>$opis</option>";
	}

	
	echo " <select name=\"su_wyroznik3\" class=\"form_select\" onchange=\"document.cookie=this.name+'='+this.value\">
			<option value=\"\">Bez ograniczeñ</option>
			$options
		</select>";





	$query="SELECT su_opiekun FROM system_user WHERE su_opiekun IS NOT NULL GROUP BY su_opiekun";
	$res=$projdb->execute($query);
	$options="";
	for($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));

		$query="SELECT su_imiona,su_nazwisko FROM system_user WHERE su_id=$su_opiekun";
		parse_str(ado_query2url($query));

		$sel=($_REQUEST[su_opiekun]==$su_opiekun)?"selected":"";
		$options.="\n<option value='$su_opiekun' $sel>$su_imiona $su_nazwisko</option>";
	}

	
	echo " <select name=\"su_opiekun\" class=\"form_select\" onchange=\"document.cookie=this.name+'='+this.value\">
			<option value=\"\">Wszyscy opiekunowie</option>
			$options
		</select>";


	echo "<br> Dodatkowy parametr: ";
	echo "<input type=text value='$_REQUEST[raport_indeks]' name='raport_indeks'
				onchange=\"document.cookie=this.name+'='+this.value\">";

?>
