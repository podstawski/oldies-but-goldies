<?
$from_name = "ZAWODY - Fakro";
$from_mail = "zawody@fakro.com.pl";

/* konta mail`e */
$mail_do_firmy["organizator"] = "zawody@fakro.com.pl";
$mail_do_firmy["webmaster"] = "michal@fakro.com.pl";

if($_POST[poczta] != '') {
	$form = $_POST['form'];
	$tematf = trim($form[temat]) == ''?'ff0000':'4A4A4A';
	$imief = trim($form[imie]) == ''?'ff0000':'4A4A4A';
	$nazwiskf = trim($form[nazwisko]) == ''?'ff0000':'4A4A4A';
	
	if((trim($form[email]) == '') || (strpos($form[email],'@') == false)) {
		$emailf = 'ff0000';
		$errors = 1;
		}
	
	if(trim($form[temat]) == '' || trim($form[imie]) == '' || trim($form[nazwisko]) == '' || trim($form[email]) == '') $errors = 1;
	}

if($_POST[poczta] == '' || $errors != 0) {
?>

<br>

<? if($errors!='') echo '<div align="center"><strong><font style="color:#ff0000;">Proszê wype³niæ pola oznaczone na czerwono!</font></div></strong><br>'; ?>

<br>

<?
$dzial_kontakt = array("organizator","webmaster");
?>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
<form action="<?=$self;?>" method="post">
<tr>
	<td align="right">wybierz odbiorcê wiadomo¶ci :</td>
	<td colspan="2"><select name="form[kontakt_konto]">
<?
	for($i = 0; $i < count($dzial_kontakt); $i++) {
		echo "<option ";
		if($kontakt_konto == $dzial_kontakt[$i]) echo " selected ";
		echo " value=\"" .$dzial_kontakt[$i]. "\">" .$dzial_kontakt[$i]. "</option>";
		}
?>
	</select></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$tematf;?>">Temat wiadomo¶ci :</font></td>
	<td colspan="2"><input type="text" name="form[temat]" value="<? echo $form[temat];?>" size="30" maxlength="70" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$imief;?>">Imiê :</font></td>
	<td colspan="2"><input type="Text" name="form[imie]" size="30" maxlength="70" value="<? echo $form[imie];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$nazwiskf;?>">Nazwisko :</font></td>
	<td colspan="2"><input type="Text" name="form[nazwisko]" size="30" maxlength="70" value="<? echo $form[nazwisko];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right">Telefon kontaktowy :</td>
	<td colspan="2"><input type="Text" name="form[telefon]" size="30" maxlength="70" value="<? echo $form[telefon];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$emailf;?>">Adres e-mail :</font></td>
	<td colspan="2"><input type="Text" name="form[email]" size="30" maxlength="70" value="<? echo $form[email];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right">UWAGI - KOMENTARZE - ZAPYTANIA :</td>
	<td colspan="2"><textarea name="form[uwagi]" cols="58" rows="5" class="input_pole"><? echo $form[uwagi]; ?></textarea></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><input type="submit" name="poczta" value="Wy¶lij wiadomo¶æ" class="submit"></td>
</tr>
</form>
</table>

<br><br>

<?
	}else{
	$tresc_mail_firma .= "Pan(i)\n";
	$tresc_mail_firma .= "imie: ".$form[imie]."\n";
	$tresc_mail_firma .= "nazwisko: ".$form[nazwisko]."\n";
	$tresc_mail_firma .= "telefon: ".$form[telefon]."\n";
	$tresc_mail_firma .= "e-mail: ".$form[email]."\n\n";
	$tresc_mail_firma .= "wysla³(a) wiadomo¶æ ze strony www.zawody.fakro.pl: \n\n\n";
	$tresc_mail_firma .= "temat: ".$form[temat]."\n";
	$tresc_mail_firma .= "uwagi: \n".$form[uwagi];
	
	$idb->sqlaction('insert','zawody_poczta','',
		array('data_zgloszenia',	'konto',		'temat',	'imie',	'nazwisko',	'telefon',	'email',	'uwagi'),
		array(date("Y-m-d, H:i:s"),	$form[kontakt_konto],	$form[temat],	$form[imie],	$form[nazwisko],	$form[telefon],	$form[email],	$form[uwagi]));
// wysylka maila
include("Mail.php");
// tworzenie obiektu przy uzyciu metody Mail::factory
$m=&Mail::Factory("smtp",$params);
#mail do firmy
// definiowanie naglowka
$header['From'] = "ZAWODY - Fakro <".$from_mail.">";
$header['To'] = $mail_do_firmy[$form[kontakt_konto]];
$header['Reply-to'] = $from_mail;
$header['Subject'] = "wiadomo¶æ ze strony zawody.fakro.pl - mail ".date("Y-m-d H:i:s", time());
$header['Content-Type'] = "text/plain;\n\tcharset: ISO-8859-2";
@$m->send($mail_do_firmy[$form[kontakt_konto]],$header,$tresc_mail_firma);
?>

<table width="100%" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td colspan="2" align="center"><br><br><strong>POCZTA ZOSTA£A WYS£ANA<br>Dziêkujemy</strong></td>
</tr>
</table>
<br><br><br>
<?
	}
?>