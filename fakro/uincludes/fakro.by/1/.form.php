<?
	
	global $SHOW, $LABEL, $WEBTD, $REQ, $ssid;

	$xml = array("SHOW"=>$SHOW,"LABEL"=>$LABEL,"REQ"=>$REQ);

	if ($ssid == $WEBTD->sid)
	{
		$sql = "UPDATE webtd SET xml = '".addslashes(serialize($xml))."' WHERE sid = $ssid
				AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
		$adodb->execute($sql);
		$tab = $xml;
	}
	else
	{
		$xml = $WEBTD->xml;
		$tab = unserialize(stripslashes($xml));
	}

	$SHOW = $tab["SHOW"];
	$LABEL = $tab["LABEL"];
	$REQ = $tab["REQ"];

	

	echo "
	<form method=post action=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"ssid\" value=\"".$WEBTD->sid."\">
	<TABLE>
	<TR>
		<TD>Element</TD>
		<TD>Widoczny</TD>
		<TD>Wymagany</TD>
	</TR>

	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[kimjestem]\" value=\"".$LABEL[kimjestem]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[kimjestem]\" value=\"1\" ".($SHOW[kimjestem]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[kimjestem]\" value=\"1\" ".($REQ[kimjestem]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD colspan=3>Opcje dla '".$LABEL[kimjestem]."'</TD>
	</TR>
	
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[check_uzytkownik]\" value=\"".$LABEL[check_uzytkownik]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[check_uzytkownik]\" value=\"1\" ".($SHOW[check_uzytkownik]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[check_zakup]\" value=\"".$LABEL[check_zakup]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[check_zakup]\" value=\"1\" ".($SHOW[check_zakup]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[check_firma_budowlana]\" value=\"".$LABEL[check_firma_budowlana]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[check_firma_budowlana]\" value=\"1\" ".($SHOW[check_firma_budowlana]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[check_architekt]\" value=\"".$LABEL[check_architekt]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[check_architekt]\" value=\"1\" ".($SHOW[check_architekt]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[check_diler]\" value=\"".$LABEL[check_diler]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[check_diler]\" value=\"1\" ".($SHOW[check_diler]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[check_inni]\" value=\"".$LABEL[check_inni]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[check_inni]\" value=\"1\" ".($SHOW[check_inni]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD colspan=3>PozostaГe pola</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[temat]\" value=\"".$LABEL[temat]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[temat]\" value=\"1\" ".($SHOW[temat]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[temat]\" value=\"1\" ".($REQ[temat]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[imie]\" value=\"".$LABEL[imie]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[imie]\" value=\"1\" ".($SHOW[imie]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[imie]\" value=\"1\" ".($REQ[imie]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[nazwisko]\" value=\"".$LABEL[nazwisko]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[nazwisko]\" value=\"1\" ".($SHOW[nazwisko]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[nazwisko]\" value=\"1\" ".($REQ[nazwisko]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[telefon]\" value=\"".$LABEL[telefon]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[telefon]\" value=\"1\" ".($SHOW[telefon]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[telefon]\" value=\"1\" ".($REQ[telefon]?"checked":"")."></TD>
	</TR>

	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[ulica]\" value=\"".$LABEL[ulica]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[ulica]\" value=\"1\" ".($SHOW[ulica]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[ulica]\" value=\"1\" ".($REQ[ulica]?"checked":"")."></TD>
	</TR>

	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[kod]\" value=\"".$LABEL[kod]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[kod]\" value=\"1\" ".($SHOW[kod]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[kod]\" value=\"1\" ".($REQ[kod]?"checked":"")."></TD>
	</TR>

	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[miasto]\" value=\"".$LABEL[miasto]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[miasto]\" value=\"1\" ".($SHOW[miasto]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[miasto]\" value=\"1\" ".($REQ[miasto]?"checked":"")."></TD>
	</TR>


	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[email]\" value=\"".$LABEL[email]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[email]\" value=\"1\" ".($SHOW[email]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[email]\" value=\"1\" ".($REQ[email]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[panstwo]\" value=\"".$LABEL[panstwo]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[panstwo]\" value=\"1\" ".($SHOW[panstwo]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[panstwo]\" value=\"1\" ".($REQ[panstwo]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[uwagi]\" value=\"".$LABEL[uwagi]."\"></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[uwagi]\" value=\"1\" ".($SHOW[uwagi]?"checked":"")."></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"REQ[uwagi]\" value=\"1\" ".($REQ[uwagi]?"checked":"")."></TD>
	</TR>
	<TR>
		<TD><textarea NAME=\"LABEL[regulamin]\" style=\"width:300px;height:120px\">".$LABEL[regulamin]."</textarea></TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"SHOW[regulamin]\" value=\"1\" ".($SHOW[regulamin]?"checked":"")."></TD>
		<TD>&nbsp;</TD>
	</TR>
	<TR>
		<TD colspan=3>TreЖц upomnienia <INPUT TYPE=\"text\" style=\"width:300px\" NAME=\"LABEL[req]\" value=\"".$LABEL[req]."\"></TD>

	</TR>
	<TR>
		<TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
	</TR>
	</TABLE>
	</FORM>
	";

?>