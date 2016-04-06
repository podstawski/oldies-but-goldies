<?

$from_name = "SKICUP - Fakro";
$from_mail = "skicup@fakro.com";
$data_zawody = $CFG['data_zawody'];

/* konta mail`e */
$mail_do_firmy["organizator"] = "skicup@fakro.com";
?>
<?
$idb->query("SELECT count(*) AS ile FROM skicup_zgloszenie");
$max = $idb->getvalues();

$datastop = date("Ymd");

if($max['ile'] >= $CFG['max_zgloszen'] || $datastop >= $CFG['datastop']) {
# blokada
?>
<br><br>
<table width="550" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td><div align="justify">
	<dd>Przepraszamy, ale lista zgłoszeń IFD FAKRO SKI WORLD CUP została zamknięta.
	Dziękujemy za zainteresowanie.</dd></div>
	<br> <div align="right">Organizatorzy</div></td>
</tr>
</table>
<?
	}else{
# wprowadzenie zgloszenia
function nip($nip) {
	$nip=preg_replace("/[^0-9]/","",$nip);
	
	if(strlen($nip)<>10) return 0;
	$wagi = array(6,5,7,2,3,4,5,6,7);
	for($i=0; $i<9;$i++) $suma += $nip[$i]*$wagi[$i];
	if(($suma%11)==$nip[9]) return 1;
	return 0;
	}

$nip_replace = preg_replace("/[^0-9]/","",$form['nip']);

$_stowarzyszenie = array('Austria','Belgia','Szwajcaria','Czechy','Niemcy','Francja','Wielka Brytania','Wêgry','Chorwacja','Irlandia','Luxemburg','£otwa','Holandia','Polska','Rosja','S³owacja','S³owenia');

$idb->query('SELECT count(*) AS ile FROM skicup_zgloszenie WHERE nip = "'.$nip_replace.'"');
$count = $idb->getvalues();
$count_nip = $count['ile'];

if($count_nip >= 5) {
	$error_nip = 1;
	}

if($_POST['poczta'] != '') {
	$form				= $_POST['form'];
	$imief				= trim($form['imie']) == ''?'ff0000':'4A4A4A';
	$nazwiskf			= trim($form['nazwisko']) == ''?'ff0000':'4A4A4A';
	//$stowarzyszenief	= trim($form['stowarzyszenie']) == ''?'ff0000':'4A4A4A';
	
	//$firmaf				= trim($form['firma']) == ''?'ff0000':'4A4A4A';
	$krajf				= trim($form['kraj']) == ''?'ff0000':'4A4A4A';
	$miejscowoscf		= trim($form['miejscowosc']) == ''?'ff0000':'4A4A4A';
	$kodf				= trim($form['kod']) == ''?'ff0000':'4A4A4A';
	$ulicaf				= trim($form['ulica']) == ''?'ff0000':'4A4A4A';
	
	$telefonf			= trim($form['telefon']) == ''?'ff0000':'4A4A4A';
	//$faxf				= trim($form['fax']) == ''?'ff0000':'4A4A4A';
	
	if((trim($form['email']) == '') || (strpos($form['email'],'@') == false)) {
		$emailf = 'ff0000';
		$errors = 1;
		}
	
	//$nipf						= trim($form['nip']) == ''?'ff0000':'4A4A4A';
	$osoba_towarzyszacaf		= trim($form['osoba_towarzyszaca']) == ''?'ff0000':'4A4A4A';
	$hotelf						= trim($form['hotel']) == ''?'ff0000':'4A4A4A';
	$wypozyczenie_sprzetuf		= trim($form['wypozyczenie_sprzetu']) == ''?'ff0000':'4A4A4A';
	
	$transportf			= trim($form['transport']) == ''?'ff0000':'4A4A4A';
	
	$oswiadczenief				= trim($form['oswiadczenie']) == ''?'ff0000':'4A4A4A';
	
	if(trim($form['imie']) == '' || trim($form['nazwisko']) == '') $errors = 1;
	//if(trim($form['stowarzyszenie']) == '') $errors = 1;
	//if(trim($form['firma']) == '') $errors = 1;
	if(trim($form['kraj']) == '' || trim($form['miejscowosc']) == '' || trim($form['kod']) == '' || trim($form['ulica']) == '') $errors = 1;
	if(trim($form['telefon']) == '') $errors = 1;
	//if(trim($form['fax']) == '') $errors = 1;
	if(trim($form['email']) == '') $errors = 1;
	//if(trim($form['nip']) == '') $errors = 1;
	if(trim($form['osoba_towarzyszaca']) == '' || trim($form['hotel']) == '' || trim($form['wypozyczenie_sprzetu']) == '') $errors = 1;
	if(trim($form['transport']) == '') $errors = 1;
	if(trim($form['oswiadczenie']) == '') $errors = 1;
	}else{
	$d_osobowe = 'tak';
	}

if($_POST['poczta'] == '' || $errors != 0) {
?>

<br>
<? if($error_nip == 1) 	echo '<div align="center"><strong><font style="color:#ff0000;">Przepraszamy, dopuszczalna ilość uczestników z Państwa Firmy została przekroczona. Podstawa: Regulamin I. 4,5</font></div></strong><br>'; ?>
<? if($errors!='' && $error_nip != 1) echo '<div align="center"><strong><font style="color:#ff0000;">Proszę wypełnić pola oznaczone na czerwono!</font></div></strong><br>';?>
<br>

<table width="100%" border="0" cellspacing="3" cellpadding="3">
<form action="<?=$self;?>" method="post">
<tr>
	<td width="200"></td>
	<td width="100"></td>
	<td width="250"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$imief;?>">Imię</font></td>
	<td colspan="2"><input type="Text" name="form[imie]" size="30" maxlength="70" value="<? echo $form['imie'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$nazwiskf;?>">Nazwisko</font></td>
	<td colspan="2"><input type="Text" name="form[nazwisko]" size="30" maxlength="70" value="<? echo $form['nazwisko'];?>" class="input_pole"></td>
</tr>
<?php /*
<tr>
	<td align="right"><font style="color: #<?=$stowarzyszenief;?>">Reprezentuję stowarzyszenie kraju</font></td>
	<td colspan="2">
	<select name="form[stowarzyszenie]" size="1">
		<OPTION VALUE="" <? if($form['stowarzyszenie'] == "") { ?>selected<?}?>>--select------</OPTION>
<?
	foreach($_stowarzyszenie as $k => $v) {
?>
		<OPTION VALUE="<?=$v;?>" <? if($form['stowarzyszenie'] == $v) { ?>selected<?}?>><?=$v;?></OPTION>
<?
		}
?>
	</select>
	</td>
</tr>
*/ ?>
<tr>
	<td colspan="3"><br></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$firmaf;?>">Firma</font></td>
	<td colspan="2"><input type="Text" name="form[firma]" size="50" maxlength="70" value="<? echo $form['firma'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$krajf;?>">Kraj</font></td>
	<td colspan="2"><input type="Text" name="form[kraj]" size="50" maxlength="70" value="<? echo $form['kraj'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$miejscowoscf;?>">Miejscowość</font></td>
	<td colspan="2"><input type="Text" name="form[miejscowosc]" size="28" maxlength="30" value="<? echo $form['miejscowosc'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$kodf;?>">Kod pocztowy</font></td>
	<td colspan="2"><input type="Text" name="form[kod]" size="10" maxlength="10" value="<? echo $form['kod'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$ulicaf;?>">Ulica i nr domu</font></td>
	<td colspan="2"><input type="Text" name="form[ulica]" size="28" maxlength="30" value="<? echo $form['ulica'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$telefonf;?>">Telefon</font></td>
	<td colspan="2"><input type="Text" name="form[telefon]" size="50" maxlength="70" value="<? echo $form['telefon'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$faxf;?>">FAX</font></td>
	<td colspan="2"><input type="Text" name="form[fax]" size="50" maxlength="70" value="<? echo $form['fax'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$emailf;?>">Adres e-mail</font></td>
	<td colspan="2"><input type="Text" name="form[email]" size="50" maxlength="70" value="<? echo $form['email'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$nipf;?>">numer identyfikacji podatkowej (dotyczy tylko członków UE)</font></td>
	<td colspan="2"><input type="Text" name="form[nip]" size="50" maxlength="70" value="<? echo $form['nip'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$osoba_towarzyszacaf;?>">osoba towarzysząca</font></td>
	<td colspan="2">
	<input type="radio" name="form[osoba_towarzyszaca]" value="tak" <? if($form['osoba_towarzyszaca'] == "tak") {?>checked<?}?>> Tak
	<br>
	<input type="radio" name="form[osoba_towarzyszaca]" value="nie" <? if($form['osoba_towarzyszaca'] == "nie") {?>checked<?}?>> Nie
	</td>
</tr>
<tr>
	<td align="right">Imię</td>
	<td colspan="2"><input type="Text" name="form[osoba_towarzyszaca_imie]" size="30" maxlength="70" value="<? echo $form['osoba_towarzyszaca_imie'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right">Nazwisko</td>
	<td colspan="2"><input type="Text" name="form[osoba_towarzyszaca_nazwisko]" size="30" maxlength="70" value="<? echo $form['osoba_towarzyszaca_nazwisko'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$hotelf;?>">rezerwacja Hotelowa (Hotel "Activa"):</font></td>
	<td colspan="2">
	<input type="radio" name="form[hotel]" value="POKOJ 1-OSOBOWY" <? if($form['hotel'] == "POKOJ 1-OSOBOWY") {?>checked<?}?>> POKÓJ 1-OSOBOWY
	<br>
	<input type="radio" name="form[hotel]" value="POKOJ 2-OSOBOWY" <? if($form['hotel'] == "POKOJ 2-OSOBOWY") {?>checked<?}?>> POKÓJ 2-OSOBOWY
	</td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$wypozyczenie_sprzetuf;?>">Jestem zainteresowany wypożyczeniem sprzętu narciarskiego na miejscu</font></td>
	<td colspan="2">
	<input type="radio" name="form[wypozyczenie_sprzetu]" value="tak" <? if($form['wypozyczenie_sprzetu'] == "tak") {?>checked<?}?>> Tak
	<br>
	<input type="radio" name="form[wypozyczenie_sprzetu]" value="nie" <? if($form['wypozyczenie_sprzetu'] == "nie") {?>checked<?}?>> Nie
	</td>
</tr>
<tr>
	<td colspan="3"><br></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$transportf;?>">Określenie sposobu dojazdu:</font></td>
	<td colspan="2">
	<input type="radio" name="form[transport]" value="transport zorganizowany" <? if($form['transport'] == "transport zorganizowany") {?>checked<?}?>> samolot do Krakowa  (port lotniczy "Balice"),
	a potem transport organizowany przez Organizatora do Muszyny
	<br><br>
	<input type="radio" name="form[transport]" value="transport wlasny" <? if($form['transport'] == "transport wlasny") {?>checked<?}?>> transport własny
	</td>
</tr>
</table>

<br><br>

<table width="550" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td valign="top"><input type="checkbox" name="form[dane_osobowe]" value="tak" <? if($form['d_osobowe'] == 'tak' || $form['dane_osobowe'] == 'tak') echo "checked"; ?> ></td>
	<td><div align="justify">Wyrażam zgodę na przetwarzanie przez Głównego Organizatora danych osobowych
	zawartych w formularzu zgłoszeniowym, dla potrzeb związanych z organizacją
	IFD FAKRO SKI WORLD CUP - zgodnie z ustawą z dnia 29 sierpnia 1997 roku o ochronie danych osobowych
	(Dz. U. z 1997 r., Nr 133 poz. 883, z późniejszymi zmianami).</div></td>
</tr>
<tr>
	<td colspan="2"><br></td>
</tr>
<tr>
	<td valign="top"><input type="checkbox" name="form[oswiadczenie]" value="tak" <? if($form['oswiadczenie'] == 'tak') echo "checked"; ?> ></td>
	<td><div align="justify"><font style="color: #<?=$oswiadczenief;?>">Oświadczam, iż zapoznałam(em) się z Regulaminem IFD FAKRO SKI WORLD CUP.</font></div></td>
</tr>
</table>
<table width="550" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td width="200"></td>
	<td width="350"><input type="submit" name="poczta" value="Wyślij wiadomość" class="submit"></td>
</tr>
</form>
</table>

<br><br>

<?
	}else{
	$body = "Pan(i)\n";
	
	foreach($form as $k => $v) {
		$body .= "$k: $v \n";
		}
	$tresc_mail_firma = $body;
	
	$nip_replace = preg_replace("/[^0-9]/","",$form['nip']);
	
	$error = $idb->query('INSERT INTO skicup_zgloszenie SET
								data_zgloszenia = "'.date("Y-m-d, H:i:s").'",
								zawody = "'.$data_zawody.'",
								imie = "'.mb_convert_case($form['imie'], MB_CASE_UPPER, "UTF-8").'",
								nazwisko = "'.mb_convert_case($form['nazwisko'], MB_CASE_UPPER, "UTF-8").'",
								stowarzyszenie = "",
								firma = "'.mb_convert_case($form['firma'], MB_CASE_UPPER, "UTF-8").'",
								kraj = "'.mb_convert_case($form['kraj'], MB_CASE_UPPER, "UTF-8").'",
								miejscowosc = "'.mb_convert_case($form['miejscowosc'], MB_CASE_UPPER, "UTF-8").'",
								kod = "'.mb_convert_case($form['kod'], MB_CASE_UPPER, "UTF-8").'",
								ulica = "'.mb_convert_case($form['ulica'], MB_CASE_UPPER, "UTF-8").'",
								
								telefon = "'.mb_convert_case($form['telefon'], MB_CASE_UPPER, "UTF-8").'",
								fax = "'.mb_convert_case($form['fax'], MB_CASE_UPPER, "UTF-8").'",
								email = "'.mb_convert_case($form['email'], MB_CASE_UPPER, "UTF-8").'",
								nip = "'.mb_convert_case($form['nip'], MB_CASE_UPPER, "UTF-8").'",
								
								osoba_towarzyszaca = "'.mb_convert_case($form['osoba_towarzyszaca'], MB_CASE_UPPER, "UTF-8").'",
								osoba_towarzyszaca_imie = "'.mb_convert_case($form['osoba_towarzyszaca_imie'], MB_CASE_UPPER, "UTF-8").'",
								osoba_towarzyszaca_nazwisko = "'.mb_convert_case($form['osoba_towarzyszaca_nazwisko'], MB_CASE_UPPER, "UTF-8").'",
								hotel = "'.mb_convert_case($form['hotel'], MB_CASE_UPPER, "UTF-8").'",
								wypozyczenie_sprzetu = "'.mb_convert_case($form['wypozyczenie_sprzetu'], MB_CASE_UPPER, "UTF-8").'",
								transport = "'.mb_convert_case($form['transport'], MB_CASE_UPPER, "UTF-8").'",
								dane_osobowe = "'.mb_convert_case($form['dane_osobowe'], MB_CASE_UPPER, "UTF-8").'",
								oswiadczenie = "'.mb_convert_case($form['oswiadczenie'], MB_CASE_UPPER, "UTF-8").'"
						');
	
	if(!$idb->error_number($error)) {
		// wysylka maila
		include("Mail.php");
		// tworzenie obiektu przy uzyciu metody Mail::factory
		$m=&Mail::Factory("smtp",$params);
		// definiowanie naglowka
		$header['From'] = "SKICUP - Fakro <".$from_mail.">";
		$header['To'] = $mail_do_firmy["organizator"];
		$header['Reply-to'] = $from_mail;
		$header['Subject'] = "zgloszenie skicup.fakro.com - mail ".date("Y-m-d H:i:s", time())." - ".$form[firma];
		$header['Content-Type'] = "text/plain;\n\tcharset=utf-8";
		@$m->send($mail_do_firmy["organizator"],$header,$tresc_mail_firma);
?>

<script type="text/javascript">
	<? if(strtoupper($form['osoba_towarzyszaca']) == 'TAK') { ?>window.location = "<?=$osoba_towarzyszaca_tak;?>"<? } ?>
	<? if(strtoupper($form['osoba_towarzyszaca']) == 'NIE') { ?>window.location = "<?=$osoba_towarzyszaca_nie;?>"<? } ?>
</script>

<?
		}else{
?>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td colspan="2" align="center"><br><br><strong><font style="color:#ff0000;">- Błąd serwera -<br>- zgłoszenie nie zostało wysłane !! -</font></strong></td>
</tr>
</table>
<br><br><br>
<?
		}
	}
# koniec wprowadzania zgloszenia
	}
?>