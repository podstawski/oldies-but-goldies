<?
$from_name = "SKICUP - Fakro";
$from_mail = "skicup@fakro.com";

/* konta mail`e */
$mail_do_firmy["organizator"] = "skicup@fakro.com";
$mail_do_firmy["webmaster"] = "michal@fakro.com.pl";

if($_POST['poczta'] != '') {
	$form		= $_POST['form'];
	$tematf		= trim($form['temat']) == ''?'ff0000':'4A4A4A';
	$imief		= trim($form['imie']) == ''?'ff0000':'4A4A4A';
	$nazwiskf	= trim($form['nazwisko']) == ''?'ff0000':'4A4A4A';
	
	if((trim($form['email']) == '') || (strpos($form['email'],'@') == false)) {
		$emailf = 'ff0000';
		$errors = 1;
		}
	
	if(trim($form['temat']) == '' || trim($form['imie']) == '' || trim($form['nazwisko']) == '' || trim($form['email']) == '') $errors = 1;
	}

if($_POST['poczta'] == '' || $errors != 0) {
?>

<br>

<? if($errors!='') echo '<div align="center"><strong><font style="color:#ff0000;">Merci pour remplir les cases marqués en rouge!</font></div></strong><br>'; ?>

<br>

<?
$dzial_kontakt = array("organisateur","webmaster");
?>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
<form action="<?=$self;?>" method="post">
<tr>
	<td align="right">Choisir le destinateur du message :</td>
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
	<td align="right"><font style="color: #<?=$tematf;?>">Objet :</font></td>
	<td colspan="2"><input type="text" name="form[temat]" value="<? echo $form['temat'];?>" size="30" maxlength="70" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$imief;?>">Prénom :</font></td>
	<td colspan="2"><input type="Text" name="form[imie]" size="30" maxlength="70" value="<? echo $form['imie'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$nazwiskf;?>">Nom :</font></td>
	<td colspan="2"><input type="Text" name="form[nazwisko]" size="30" maxlength="70" value="<? echo $form['nazwisko'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right">No de téléphone :</td>
	<td colspan="2"><input type="Text" name="form[telefon]" size="30" maxlength="70" value="<? echo $form['telefon'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<?=$emailf;?>">e-mail :</font></td>
	<td colspan="2"><input type="Text" name="form[email]" size="30" maxlength="70" value="<? echo $form['email'];?>" class="input_pole"></td>
</tr>
<tr>
	<td align="right">OBSERVATIONS - COMMENTAIRES - QUESTIONS :</td>
	<td colspan="2"><textarea name="form[uwagi]" cols="58" rows="5" class="input_pole"><? echo $form['uwagi']; ?></textarea></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td colspan="2"><input type="submit" name="poczta" value="Envoyer le message" class="submit"></td>
</tr>
</form>
</table>

<br><br>

<?
	}else{
	$tresc_mail_firma .= "Pan(i)\n";
	$tresc_mail_firma .= "imie: ".$form['imie']."\n";
	$tresc_mail_firma .= "nazwisko: ".$form['nazwisko']."\n";
	$tresc_mail_firma .= "telefon: ".$form['telefon']."\n";
	$tresc_mail_firma .= "e-mail: ".$form['email']."\n\n";
	$tresc_mail_firma .= "wyslal(a) wiadomosc ze strony www.skicup.fakro.com: \n\n\n";
	$tresc_mail_firma .= "temat: ".$form['temat']."\n";
	$tresc_mail_firma .= "uwagi: \n".$form['uwagi'];
	
	$idb->query('INSERT INTO skicup_poczta SET
								data_zgloszenia = "'.date("Y-m-d, H:i:s").'",
								konto = "'.$form['kontakt_konto'].'",
								temat = "'.mb_convert_case($form['temat'], MB_CASE_UPPER, "UTF-8").'",
								imie = "'.mb_convert_case($form['imie'], MB_CASE_UPPER, "UTF-8").'",
								nazwisko = "'.mb_convert_case($form['nazwisko'], MB_CASE_UPPER, "UTF-8").'",
								telefon = "'.mb_convert_case($form['telefon'], MB_CASE_UPPER, "UTF-8").'",
								email = "'.mb_convert_case($form['email'], MB_CASE_UPPER, "UTF-8").'",
								uwagi = "'.mb_convert_case($form['uwagi'], MB_CASE_UPPER, "UTF-8").'"
						');
	
	// wysylka maila
	include("Mail.php");
	// tworzenie obiektu przy uzyciu metody Mail::factory
	$m=&Mail::Factory("smtp",$params);
	#mail do firmy
	// definiowanie naglowka
	$header['From'] = "SKICUP - Fakro <".$from_mail.">";
	$header['To'] = $mail_do_firmy[$form['kontakt_konto']];
	$header['Reply-to'] = $from_mail;
	$header['Subject'] = "wiadomosc ze strony skicup.fakro.com - mail ".date("Y-m-d H:i:s", time());
	$header['Content-Type'] = "text/plain;\n\tcharset=utf-8";
	@$m->send($mail_do_firmy[$form['kontakt_konto']],$header,$tresc_mail_firma);
?>

<table width="100%" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td colspan="2" align="center"><br><br><strong>Votre email a été envoyé.<br>Merci.</strong></td>
</tr>
</table>
<br><br><br>
<?
	}
?>