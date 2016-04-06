<SCRIPT language=JavaScript>
<!--
function showForm(sf) {
	if(sf == 'df_lista_usa') {
		show('df_lista_show1');
		hide('df_lista_show2');
		}
	if(sf == 'df_lista_canada') {
		hide('df_lista_show1');
		show('df_lista_show2');
		}
	}
function hide(id) {
	element = document.getElementById(id);
	if (element)
		element.style.display = "none";
	}
function show(id) {
	element = document.getElementById(id);
	if (element)element.selectedIndex
	element.style.display = "";
	}
-->
</SCRIPT>

<?
$type['country'] = array(1=> "USA", 2=> "CANADA");
$state['USA'] = array(
						1=>'AL',2=>'AK',3=>'AZ',4=>'AR',5=>'CA',6=>'CO',7=>'CT',8=>'DC',9=>'DE',10=>'FL',11=>'GA',12=>'HI',
						13=>'ID',14=>'IL',15=>'IN',16=>'IA',17=>'KS',18=>'KY',19=>'LA',20=>'ME',21=>'MD',22=>'MA',23=>'MI',
						24=>'MN',25=>'MS',26=>'MO',27=>'MT',28=>'NE',29=>'NV',30=>'NH',31=>'NJ',32=>'NM',33=>'NY',34=>'NC',
						35=>'ND',36=>'OH',37=>'OK',38=>'OR',39=>'PA',40=>'RI',41=>'SC',42=>'SD',43=>'TN',44=>'TX',45=>'UT',
						46=>'VT',47=>'VA',48=>'WA',49=>'WV',50=>'WI',51=>'WY'
						);
$state['CANADA'] = array(
						1=>'AB',2=>'BC',3=>'MB',4=>'NB',5=>'NF',6=>'NS',7=>'NT',8=>'ON',9=>'PE',10=>'PQ',11=>'SK',12=>'YK'
						);

$type['product'] = array(1=> "attic ladders", 2=> "accessories");

$type['attic_ladders'] = array(
								1=> "OLN 22/47,7'2''- 8'11'' ceiling height",
								2=> "OLN 25/47,7'2''- 8'11'' ceiling height",
								3=> "OLN 22/54,7'11'' - 10'1'' ceiling height",
								4=> "OLN 25/54,7'11'' - 10'1'' ceiling height",
								5=> "OLN 30/54,7'11'' - 10'1'' ceiling height",
								6=> "OLN-P 22/47,7'2''- 8'11'' ceiling height",
								7=> "OLN-P 25/47,7'2''- 8'11'' ceiling height",
								8=> "OLN-P 22/54,7'11'' - 10'1'' ceiling height",
								9=> "OLN-P 25/54,7'11'' - 10'1'' ceiling height",
								10=> "OLN-P 30/54,7'11'' - 10'1'' ceiling height",
								11=> "LWS-P 22/47,7'2''- 8'11'' ceiling height",
								12=> "LWS-P 25/47,7'2''- 8'11'' ceiling height",
								13=> "LWS-P 22/54,7'11'' - 10'1'' ceiling height",
								14=> "LWS-P 25/54,7'11'' - 10'1'' ceiling height",
								15=> "LWS-P 30/54,7'11'' - 10'1'' ceiling height",
								16=> "LWS-P 22/54,7'11'' - 10'9'' ceiling height",
								17=> "LWS-P 25/54,7'11'' - 10'9'' ceiling height",
								18=> "LWS-P 30/54,7'11'' - 10'9'' ceiling height",
								19=> "LWF 22/47,7'2''- 8'11'' ceiling height",
								20=> "LWF 25/47,7'2''- 8'11'' ceiling height",
								21=> "LWF 22/54,7'11'' - 10'1'' ceiling height",
								22=> "LWF 25/54,7'11'' - 10'1'' ceiling height",
								23=> "LWF 30/54,7'11'' - 10'1'' ceiling height",
								24=> "LWS-M 27/47,7'2''- 9'2'' ceiling height",
								25=> "OWM 22/47,7'2''- 8'11'' ceiling height",
								26=> "OWM 25/47,7'2''- 8'11'' ceiling height",
								27=> "OWM 22/54,7'11'' - 10'1'' ceiling height",
								28=> "OWM 25/54,7'11'' - 10'1'' ceiling height",
								29=> "OWM 30/54,7'11'' - 10'1'' ceiling height",
								30=> "LMS 22/47,7'2''- 8'11'' ceiling height",
								31=> "LMS 25/47,7'2''- 8'11'' ceiling height",
								32=> "LMS 22/54,7'11'' - 10'1'' ceiling height",
								33=> "LMS 25/54,7'11'' - 10'1'' ceiling height",
								34=> "LMS 30/54,7'11'' - 10'1'' ceiling height",
								35=> "LST 22/31,7'' 6 1/2'' - 10'10'' ceiling height",
								36=> "LST 27/31,7'' 6 1/2'' - 10'10'' ceiling height",
								37=> "LST 22/47,7'' 6 1/2'' - 10'10'' ceiling height",
								38=> "LST 25/47,7'' 6 1/2'' - 10'10'' ceiling height",
								39=> "LST 22/54,7'' 6 1/2'' - 10'10'' ceiling height",
								40=> "LST 25/54,7'' 6 1/2'' - 10'10'' ceiling height",
								41=> "LSF 22/47,7' 10 1/2'' - 10' 6'' ceiling height",
								42=> "LSF 25/47,7' 10 1/2'' - 10' 6'' ceiling height"
								);
								//4=> "LWS-P 22 1/2x54, 10' ceiling height",
								//3=> "LWS-P 22 1/2x47, 8'10'' ceiling height",
								//8=> "LWS-M Do It Yourself ladder"
//---------------------------------------------------

if($_POST['buy'] != '') {
	$form = $_POST['form'];
	
	$first_namef = trim($form['first_name']) == ''?"ff0000":"000000";
	$last_namef = trim($form['last_name']) == ''?"ff0000":"000000";
	$addressf = trim($form['address']) == ''?"ff0000":"000000";
	$cityf = trim($form['city']) == ''?"ff0000":"000000";
	$zip_codef = trim($form['zip_code']) == ''?"ff0000":"000000";
	
	$country_type1 = trim($form['country_type']) == ''?1:0;
	
	$country_type2 = (trim($form['country_type']) == '1' && trim($form['state_usa']) == '')?1:0;
	$country_type3 = (trim($form['country_type']) == '2' && trim($form['state_canada']) == '')?1:0;
	
	$country_typef = ($country_type1 == '1' || $country_type2 == 1 || $country_type3 == 1)?"ff0000":"000000";
	
	$phonef = trim($form['phone']) == ''?"ff0000":"000000";
	$emailf = (trim($form['email']) == '' || !strpos($form['email'],'@'))?"ff0000":"000000";
	
	$locationf = trim($form['location']) == ''?"ff0000":"000000";
	$type_1f = trim($form['type_1']) == ''?"ff0000":"000000";
	$quantityf = trim($form['quantity']) == ''?"ff0000":"000000";
	$best_timef = trim($form['best_time']) == ''?"ff0000":"000000";
	
	if(trim($form['first_name']) == '' || trim($form['last_name']) == '' || trim($form['address']) == '' || trim($form['city']) == '' || trim($form['zip_code']) == '') $errors = 1;
	if(trim($form['country_type']) == '' || (trim($form['country_type']) == '1' && trim($form['state_usa']) == '') || (trim($form['country_type']) == '2' && trim($form['state_canada']) == '')) $errors = 1;
	if(trim($form['phone']) == '' || (trim($form['email']) == '' || !strpos($form['email'],'@')) || trim($form['location']) == '' || trim($form['type_1']) == '' || trim($form['quantity']) == '' || trim($form['best_time']) == '') $errors = 1;
	
	if($form['country_type'] == 1) unset($form['state_canada']);
	if($form['country_type'] == 2) unset($form['state_usa']);
	}
?>


<div align="center">
<?
if($_POST['buy'] == '' || $errors != 0) {
?>	
	<? if($errors!='') echo "<br><br><font color=\"red\"><strong>You have to fill out the field marked in red!</strong></font>"; ?>
<TABLE width="100%" cellspacing="3" cellpadding="3" border="0">
<tr>
	<td width="150"></td>
	<td></td>
</tr>
<form action="<?=$self;?>" method="post">
<tr>
	<td align="right"><font style="color: #<? echo $first_namef;?>;">First name:</font></td>
	<td><input type="text" name="form[first_name]" value="<? echo $form['first_name'];?>" size="30" maxlength="150"><td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $last_namef;?>;">Last name:</font></td>
	<td><input type="text" name="form[last_name]" value="<? echo $form['last_name'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $addressf;?>;">Address:</font></td>
	<td><input type="text" name="form[address]" value="<? echo $form['address'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $cityf;?>;">City:</font></td>
	<td><input type="text" name="form[city]" value="<? echo $form['city'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $zip_codef;?>;">Zip code:</font></td>
	<td><input type="text" name="form[zip_code]" value="<? echo $form['zip_code'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $country_typef;?>;">Country:</font></td>
	<td>
	<input type="radio" name="form[country_type]" value="1" <? if($form['country_type'] == "1") echo "checked"; ?> onClick="showForm('df_lista_usa');"> <? echo $type['country'][1]; ?>
	<br>
	<input type="radio" name="form[country_type]" value="2" <? if($form['country_type'] == "2") echo "checked"; ?> onclick="showForm('df_lista_canada');"> <? echo $type['country'][2]; ?>
	</td>
</tr>
<tr id="df_lista_show1" <? if($form['country_type'] != "1") echo 'style="DISPLAY: none"'; ?>>
	<td align="right"><font style="color: #<? echo $country_typef;?>;">State/Province :</font></td>
	<td>
	<select name="form[state_usa]">
		<option value="">---select---------</option>
<?
	for($i = 1; $i <= count($state['USA']); $i++) {
		echo '<option value="'.$i.'" ';
		if($form['state_usa'] == $i) echo "selected";
		echo '>'.$state['USA'][$i].'</option>';
		}
?>
	</select>
	</td>
</tr>
<tr id="df_lista_show2" <? if($country_type != "2") echo 'style="DISPLAY: none"'; ?>>
	<td align="right"><font style="color: #<? echo $country_typef;?>;">State/Province :</font></td>
	<td>
	<select name="form[state_canada]">
		<option value="">---select---------</option>
<?
	for($i = 1; $i <= count($state['CANADA']); $i++) {
		echo '<option value="'.$i.'" ';
		if($form['state_canada'] == $i) echo "selected";
		echo '>'.$state['CANADA'][$i].'</option>';
		}
?>
	</select>
	</td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $phonef;?>;">Phone no.:</font></td>
	<td><input type="text" name="form[phone]" value="<? echo $form['phone'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $emailf;?>;">E-mail address:</font></td>
	<td><input type="text" name="form[email]" value="<? echo $form['email'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td colspan="2"><br><br></td>
</tr>

<tr>
	<td align="right"><font style="color: #<? echo $locationf;?>;">Location:</font></td>
	<td>
	<select name="form[location]">
		<option value="" <? if($form['location'] == "0") echo "selected"; ?>>---select---------</option>
		<option value="commerical" <? if($form['location'] == "commerical") echo "selected"; ?>>commerical</option>
		<option value="residential" <? if($form['location'] == "residential") echo "selected"; ?>>residential</option>
	</select>
	</td>
</tr>
<tr id="df_lista_show1">
	<td align="right"><font style="color: #<? echo $type_1f;?>;">attic ladders</font></td>
	<td>
	<select name="form[type_1]">
		<option value="">---select---------</option>
<?
	for($i = 1; $i <= count($type['attic_ladders']); $i++) {
		echo '<option value="'.$i.'" ';
		if($form['type_1'] == $i) echo "selected";
		echo '>'.$type['attic_ladders'][$i].'</option>';

		}
?>
	</select>
	</td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $quantityf;?>;">Quantity:</font></td>
	<td><input type="text" name="form[quantity]" value="<? echo $form['quantity'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right"><font style="color: #<? echo $best_timef;?>;">Best time to contact:</font></td>
	<td><input type="text" name="form[best_time]" value="<? echo $form['best_time'];?>" size="30" maxlength="150"></td>
</tr>
<tr>
	<td align="right">Where did You Hear About Us:<br>Comments:</td>
	<td><textarea cols="42" rows="3" name="form[about_us]"><? echo $form['about_us'];?></textarea></td>
</tr>
<tr>
	<td colspan="2"><strong>By pressing "SUBMIT" you are entering a legal purchase agreement and are obligated to complete the transaction with the Skywin Fakro Company.</strong></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="buy" value="submit" class="submit"></td>
</tr>
</form>
</table>
</div>
<?
	}else{
	#ustawienia do poczty 
	$params["host"] = "mail.fakro.com.pl";		// adres serwera SMTP
	$params["port"] = "25";						// port serwera SMTP (zazwyczaj: 25)
	$params["auth"] = true;						// czy serwer wymaga autoryzacji (zazwyczaj: true)
	$params["username"] = "robotfakro";			// login konta (ewentualnie adres e-mail konta)
	$params["password"] = "2wsxcde3";			// hasło konta
	
	$from_name = "FAKRO";
	$from_mail = "fakro@fakro.ca";
	
	/* konta mail`e */
	$mail_do_firmy = "mark@fakro.ca";
	#$mail_do_firmy = "michal@fakro.com.pl";
	
	/* e-mail do firmy */
	$message .= "First name : ".$form['first_name'];
	$message .= "\nLast name : ".$form['last_name'];
	$message .= "\nAddress : ".$form['address'];
	$message .= "\nCity : ".$form['city'];
	$message .= "\nZip code : ".$form['zip_code'];
	#----
	$message .= "\nCountry : ".$type['country'][$form['country_type']];
	
	if($form['country_type'] == '1' && $form['state_usa'] != '') $message .= "\nState/Province : ".$state['USA'][$form['state_usa']];
	if($form['country_type'] == '2' && $form['state_canada'] != '') $message .= "\nState/Province : ".$state['CANADA'][$form['state_canada']];
	
	
	$message .= "\nPhone no. : ".$form['phone'];
	$message .= "\nE-mail address : ".$form['email'];
	
	$message .= "\nLocation : ".$form['location'];
	$message .= "\nProduct type : ".$type['attic_ladders'][$form['type_1']];
	$message .= "\nQuantity : ".$form['quantity'];
	
	$message .= "\n\nBest time to contact : ".$form['best_time'];
	$message .= "\nWhere did You Hear About Us / Comments : ".$form['about_us'];
	
	$tresc_listu_firma = $message."\r\n";
	
	// wysylka maila
	include("Mail.php");
	// tworzenie obiektu przy uzyciu metody Mail::factory
	$m=&Mail::Factory("smtp",$params);
	# do firmy
	// definiowanie naglowka
	$header['From'] = "FAKRO <".$from_mail.">";
	$header['To'] = $mail_do_firmy;
	$header['Reply-to'] = $from_mail;
	$header['Subject'] = "Buy now - ".date("Y-m-d H:i:s", time());
	$header['Content-Type'] = "text/plain;\n\tcharset: ISO-8859-2";
	$error =  @$m->send($mail_do_firmy,$header,$tresc_listu_firma);
	
	
	if(PEAR::isError($error)) {
		echo "<br><br><strong>E-mail server error</strong> -<br>- message sending error !! -<br><br><br><br>";
		}else{
		echo "<br><br><strong>Your e-mail has been sent</strong><br><br><br><br><br><br><br>";
		}
	}
?>