<?
$from_name = "ZAWODY - Fakro";
$from_mail = "zawody@fakro.com.pl";
$data_zawody = '2012';

/* konta mail`e */
$mail_do_firmy["organizator"] = "zawody@fakro.com.pl";
?>
<?
$idb->query("SELECT count(*) AS ile FROM zawody_zgloszenie");
$max = $idb->getvalues();

$datastop = date("Ymd");

if($max['ile'] >= 120 || $datastop >= "20120224") {
# blokada
?>
<br><br>
<table width="550" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td><div align="justify">
	<dd>Przepraszamy, ale lista zgłoszeń X Narciarskich Mistrzostwach Polski Branży Budowlanej została zamknięta. 
	Dziękujemy za zainteresowanie. Wszystkich Państwa gorąco zapraszamy do uczestnictwa w zawodach już za rok.</dd></div>
	<br> <div align="right">Organizatorzy</div></td>
</tr>
</table>
<?
	}else{
# wprowadzenie zgloszenia
$form = $_POST['form'];

function nip($nip) {
	$nip=preg_replace("/[^0-9]/","",$nip);
	
	if(strlen($nip)<>10) return 0;
	$wagi = array(6,5,7,2,3,4,5,6,7);
	for($i=0; $i<9;$i++) $suma += $nip[$i]*$wagi[$i];
	if(($suma%11)==$nip[9]) return 1;
	return 0;
	}

$nip_replace = preg_replace("/[^0-9]/","",$form[nip]);
$idb->query('SELECT count(*) AS ile FROM zawody_zgloszenie WHERE nip = "'.$nip_replace.'"');
$count = $idb->getvalues();
$count_nip = $count['ile'];

if($count_nip >= 5) {
	$error_nip = 1;
	}

if($_POST[poczta] != '') {
	$form = $_POST['form'];
	$imief = trim($form[imie]) == ''?'ff0000':'4A4A4A';
	$nazwiskf = trim($form[nazwisko]) == ''?'ff0000':'4A4A4A';
	$plecf = trim($form[plec]) == ''?'ff0000':'4A4A4A';
	
	if(!ereg('^[0-9]{4}$',$form[rok_urodzenia]) || ($form[rok_urodzenia] == '') || ($form[rok_urodzenia] < '1921') || ($form[rok_urodzenia] > '2007')) {
		$rok_urodzeniaf = 'ff0000';
		$errors = 1;
		}
	
	$firmaf = trim($form[firma]) == ''?'ff0000':'4A4A4A';
	$nipf = trim($form[nip]) == ''?'ff0000':'4A4A4A';
	$nipf = nip(trim($form[nip])) == ''?'ff0000':'4A4A4A';
	$ulicaf = trim($form[ulica]) == ''?'ff0000':'4A4A4A';
	$miejscowoscf = trim($form[miejscowosc]) == ''?'ff0000':'4A4A4A';
	
	if(!ereg('^[0-9]{2}$',$form[kod_1]) || ($form[kod_1] == '')) {
		$kodf = 'ff0000';
		$errors = 1;
		}
	if(!ereg('^[0-9]{3}$',$form[kod_2]) || ($form[kod_2] == '')) {
		$kodf = 'ff0000';
		$errors = 1;
		}
	
	$stanowiskof = trim($form[stanowisko]) == ''?'ff0000':'4A4A4A';
	$telefonf = trim($form[telefon]) == ''?'ff0000':'4A4A4A';
	
	if((trim($form[email]) == '') || (strpos($form[email],'@') == false)) {
		$emailf = 'color="ff0000"';
		$errors = 1;
		}
	
	$oplataf = trim($form[oplata]) == ''?'ff0000':'4A4A4A';
	$oswiadczenief = trim($form[oswiadczenie]) == ''?'ff0000':'4A4A4A';
	
	if(trim($form[imie]) == '' || trim($form[nazwisko]) == '' || trim($form[plec]) == '' || trim($form[rok_urodzenia]) == '' || trim($form[firma]) == '') $errors = 1;
	if(nip(trim($form[nip])) == '' || trim($form[ulica]) == '' || trim($form[miejscowosc]) == '' || trim($form[kod_1]) == '' || trim($form[kod_2]) == '') $errors = 1;
	if(trim($form[stanowisko]) == '' || trim($form[telefon]) == '' || trim($form[email]) == '') $errors = 1;
	if(trim($form[oplata]) == '' || trim($form[oswiadczenie]) == '') $errors = 1;
	if($error_nip == 1) $errors = 1;
	}else{
	$d_osobowe = 'tak';
	}

if($_POST[poczta] == '' || $errors != 0) {
?>

<br>
<? if($error_nip == 1) 	echo '<div align="center"><strong><font style="color:#ff0000;">Przepraszamy, dopuszczalna ilość uczestników z Państwa Firmy została przekroczona.<br>Podstawa: Regulamin IX NMPB, I Postanowienia Ogólne, pkt. 4c</font></div></strong><br>'; ?>
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
	<td colspan="2"><input type="Text" name="form[imie]" size="30" maxlength="70" value="<? echo $form[imie];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$nazwiskf;?>">Nazwisko</font></td>
	<td colspan="2"><input type="Text" name="form[nazwisko]" size="30" maxlength="70" value="<? echo $form[nazwisko];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$plecf;?>">Płeć</font></td>
	<td colspan="2">
	<select name="form[plec]" size="1">
		<OPTION VALUE="" <? if($form[plec] == "") { ?>selected<?}?>>--select------</OPTION>
		<OPTION VALUE="kobieta" <? if($form[plec] == "kobieta") { ?>selected<?}?>>Kobieta</OPTION>
		<option value="mezczyzna" <? if($form[plec] == "mezczyzna") { ?>selected<?}?>>Mężczyzna</option>
	</select>
	</td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$rok_urodzeniaf;?>">Rok urodzenia</font></td>
	<td colspan="2"><input type="Text" name="form[rok_urodzenia]" size="4" maxlength="4" value="<? echo $form[rok_urodzenia];?>" class="input_pole"></td>
</tr>
<tr>
	<td colspan="3"><br></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$firmaf;?>">Nazwa firmy</font></td>
	<td colspan="2"><input type="Text" name="form[firma]" size="50" maxlength="70" value="<? echo $form[firma];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$nipf;?>">NIP firmy</font></td>
	<td colspan="2"><input type="Text" name="form[nip]" size="50" maxlength="70" value="<? echo $form[nip];?>" class="input_pole"></td>
</tr>
<tr>
	<td rowspan="3" align="right">Dokładny adres</td>
	<td align="right"><font style="color: #<?=$ulicaf;?>">Ulica</font></td>
	<td><input type="Text" name="form[ulica]" size="28" maxlength="30" value="<? echo $form[ulica];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$miejscowoscf;?>">Miejscowość</font></td>
	<td><input type="Text" name="form[miejscowosc]" size="28" maxlength="30" value="<? echo $form[miejscowosc];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$kodf;?>">Kod pocztowy</font></td>
	<td>
	<input type="Text" name="form[kod_1]" size="2" maxlength="2" value="<? echo $form[kod_1];?>" class="input_pole"> -
	<input type="Text" name="form[kod_2]" size="3" maxlength="3" value="<? echo $form[kod_2];?>" class="input_pole">
	</td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$stanowiskof;?>">Stanowisko</font></td>
	<td colspan="2"><input type="Text" name="form[stanowisko]" size="50" maxlength="70" value="<? echo $form[stanowisko];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$telefonf;?>">Telefon kontaktowy</font></td>
	<td colspan="2"><input type="Text" name="form[telefon]" size="50" maxlength="70" value="<? echo $form[telefon];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$emailf;?>">Adres e-mail</font></td>
	<td colspan="2"><input type="Text" name="form[email]" size="50" maxlength="70" value="<? echo $form[email];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$oplataf;?>">Opłata startowa *</font></td>
	<td colspan="2">
	Tak <input type="radio" name="form[oplata]" value="tak" <? if($form[oplata] == "tak") {?>checked<?}?>>&nbsp;&nbsp;&nbsp;
	Nie <input type="radio" name="form[oplata]" value="nie" <? if($form[oplata] == "nie") {?>checked<?}?>></td>
</tr>
<tr>
	<td align="right"></td>
	<td colspan="2">* proszę zakreślić czy w chwili dokonywania zgłoszenia przesłana została opłata startowa w wysokości 70 zł (zgodnie z regulaminem zawodów).</td>
</tr>
</table>

<br><br>

<table width="550" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td valign="top"><input type="checkbox" name="form[dane_osobowe]" value="tak" <? if($form[d_osobowe] == 'tak' || $form[dane_osobowe] == 'tak') echo "checked"; ?> ></td>
	<td><div align="justify">Wyrażam zgodę na przetwarzanie przez Głównego Organizatora danych osobowych
	zawartych w formularzu zgłoszeniowym, dla potrzeb związanych z organizacją
	IX Narciarskich Mistrzostwach Polski Branży Budowlanej - zgodnie z ustawą z dnia 29 sierpnia 1997 roku o ochronie danych osobowych
	(Dz. U. z 1997 r., Nr 133 poz. 883, z późniejszymi zmianami).</div></td>
</tr>
<tr>
	<td colspan="2"><br></td>
</tr>
<tr>
	<td valign="top"><input type="checkbox" name="form[oswiadczenie]" value="tak" <? if($form[oswiadczenie] == 'tak') echo "checked"; ?> ></td>
	<td><div align="justify"><font style="color: #<?=$oswiadczenief;?>">Oświadczam, iż zapoznałam(em) się z  Regulaminem X Narciarskich Mistrzostw Polski Branży Budowlanej.</font></div></td>
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
	$kod_pocztowy = $form[kod_1]."-".$form[kod_2];
	
	$tresc_mail_firma = "Pan(i)\n";
	$tresc_mail_firma .= "imie: ".$form[imie]."\n";
	$tresc_mail_firma .= "nazwisko: ".$form[nazwisko]."\n\n";
	$tresc_mail_firma .= "firma: ".$form[firma]."\n";
	$tresc_mail_firma .= "stanowisko: ".$form[stanowisko]."\n";
	$tresc_mail_firma .= "telefon: ".$form[telefon]."\n";
	$tresc_mail_firma .= "email: ".$form[email]."\n";
	$tresc_mail_firma .= "oplata: ".$form[oplata]."\n\n";
	
/*
	$temat_mail_zwrotny = "Potwierdzenie wysłania zgłoszenia";
	$tresc_mail_zwrotny = "Dziękujemy za zgłoszenie udziału w IX Narciarskich Mistrzostwach Polski Branży\n";
	$tresc_mail_zwrotny .= "Budowlanej, które odbędą się 25 lutego 2007 r. na trasie nr 2a  Jaworzyny Krynickiej.\n";
	$tresc_mail_zwrotny .= "Start pierwszego zawodnika planowany jest na godzinę 10.30.\n";
	$tresc_mail_zwrotny .= "Numery startowe będzie można odebrać w godzinach od 9.00-10.00 w biurze zawodów, znajdującym się przy mecie slalomu, w dolnej części trasy 2a.\n\n";
	$tresc_mail_zwrotny .= "Równocześnie przypominamy, iż warunkiem uczestnictwa jest dokonanie wpłaty.\n";
	$tresc_mail_zwrotny .= "Opłata startowa wynosi 65 zł od uczestnika, płatna na konto:\n";
	$tresc_mail_zwrotny .= "Kredyt Bank S.A. O/Nowy Sącz, ul. Jagiellońska 56, 33-300 Nowy Sącz,\n";
	$tresc_mail_zwrotny .= "FAKRO Sp. z o.o., ul. Węgierska 144a, 33-300 Nowy Sącz , 91150015591215500098290000, z dopiskiem \"zawody\".\n";
	$tresc_mail_zwrotny .= "W przypadku rezygnacji z uczestnictwa w zawodach uiszczona opłata nie podlega zwrotowi.\n";
	$tresc_mail_zwrotny .= "Lista  startowa, aktualizowana codziennie, dostępna będzie na stronach internetowych.\n\n";
	$tresc_mail_zwrotny .= "Wszelkie informacje na temat zawodów uzyskać można pod numerami telefonu 018 414 0 151.\n\n";
	$tresc_mail_zwrotny .= "Życzymy udanej zabawy i do zobaczenia na stoku.\n";
	$tresc_mail_zwrotny .= "Organizatorzy\n\n";
*/	
	$nip_replace = preg_replace("/[^0-9]/","",$form[nip]);
	
	$error = $idb->query('INSERT INTO zawody_zgloszenie SET
							data_zgloszenia = "'.date("Y-m-d, H:i:s").'",
							zawody = "'.$data_zawody.'",
							imie = "'.mb_convert_case($form['imie'], MB_CASE_UPPER, "UTF-8").'",
							nazwisko = "'.mb_convert_case($form['nazwisko'], MB_CASE_UPPER, "UTF-8").'",
							plec = "'.mb_convert_case($form['plec'], MB_CASE_UPPER, "UTF-8").'",
							rok_urodzenia = "'.mb_convert_case($form['rok_urodzenia'], MB_CASE_UPPER, "UTF-8").'",
							firma = "'.mb_convert_case($form['firma'], MB_CASE_UPPER, "UTF-8").'",
							nip = "'.$nip_replace.'",
							ulica = "'.mb_convert_case($form['ulica'], MB_CASE_UPPER, "UTF-8").'",
							miejscowosc = "'.mb_convert_case($form['miejscowosc'], MB_CASE_UPPER, "UTF-8").'",
							kod = "'.$kod_pocztowy.'",
							stanowisko = "'.mb_convert_case($form['stanowisko'], MB_CASE_UPPER, "UTF-8").'",
							telefon = "'.mb_convert_case($form['telefon'], MB_CASE_UPPER, "UTF-8").'",
							email = "'.mb_convert_case($form['email'], MB_CASE_UPPER, "UTF-8").'",
							oplata = "'.mb_convert_case($form['oplata'], MB_CASE_UPPER, "UTF-8").'",
							dane_osobowe = "'.mb_convert_case($form['dane_osobowe'], MB_CASE_UPPER, "UTF-8").'",
							oswiadczenie = "'.mb_convert_case($form['oswiadczenie'], MB_CASE_UPPER, "UTF-8").'"
							');
	
	if(!$idb->error_number($error)) {
		// wysylka maila
		include("Mail.php");
		// tworzenie obiektu przy uzyciu metody Mail::factory
		$m=&Mail::Factory("smtp",$params);
		#mail do firmy
		// definiowanie naglowka
		$header['From'] = "ZAWODY - Fakro <".$from_mail.">";
		$header['To'] = $mail_do_firmy["organizator"];
		$header['Reply-to'] = $from_mail;
		$header['Subject'] = "zgloszenie zawody.fakro.pl - mail ".date("Y-m-d H:i:s", time())." - ".$form[firma];
		$header['Content-Type'] = "text/plain;\n\tcharset: UTF-8";
		@$m->send($mail_do_firmy["organizator"],$header,$tresc_mail_firma);
?>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td colspan="2" align="center"><br><br><strong>ZGŁOSZENIE ZOSTAŁO WYSŁANE<br>Dziękujemy za zainteresowanie naszymi zawodami</strong><br><br>
	Jeśli szukasz zakwaterowania serdecznie zapraszamy do Ośrodka Wypoczynkowego "Activa" <a href="http://www.hotel-activa.pl" target="_blank">www.hotel-activa.pl</a>
	</td>
</tr>
</table>
<br><br><br>
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