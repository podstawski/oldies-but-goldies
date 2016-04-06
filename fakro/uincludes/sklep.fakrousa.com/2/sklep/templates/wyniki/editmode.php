<?
	global $CYFRY, $CYFRYV, $ALFAN, $ALFANV;

	$cyfry = "";
	$alfan = "";
/*	
	print_r($ALFAN);
	echo "<hr>";
	print_r($CYFRY);
*/
	if (is_array($CYFRY) || is_array($ALFAN))
	{
		for ($i=0; $i < 6; $i++)
		{
			$CYFRY[$i] = $CYFRY[$i]*1;
			$ALFAN[$i] = $ALFAN[$i]*1;

			if ($CYFRY[$i] == 1)
			{
				if (strlen($CYFRYV[$i]))
					$cyfry.=$CYFRYV[$i];
				else
					$cyfry.="0";
			}
			else
				$cyfry.="0";

			if ($ALFAN[$i] == 1)
			{
				if (strlen($ALFANV[$i]))
					$alfan.=$ALFANV[$i];
				else
					$alfan.="0";
			}
			else
				$alfan.="0";
		}

		$pos=strpos($costxt,"&cyfry=");
		if ($pos) $costxt=substr($costxt,0,$pos);
		$costxt.="&cyfry=".$cyfry."&alfan=".$alfan;

		$sql = "UPDATE webtd SET costxt='$costxt' WHERE sid = $WEBTD->sid";
		$kameleon_adodb->execute($sql);
	}

	parse_str($costxt);
	if (!strlen($cyfry))
		$cyfry = "210100";

	if (!strlen($alfan))
		$alfan = "212000";

	for ($i=0; $i < 6; $i++)
	{
		if ($cyfry[$i] != "0")
			$chck[$i] = "checked";
		if ($alfan[$i] != "0")
			$chak[$i] = "checked";

		$indc = $cyfry[$i];
		$chc[$i.$indc] = "checked";
		$inda = $alfan[$i];
		$cha[$i.$inda] = "checked";
	}


	$tabelka = "
	<table bgcolor=\"silver\" valign=top width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">
	<TR>
		<Th>&nbsp;</Th>
		<Th>Cyfry</Th>
		<Th>Alfanumeryczne</Th>
	</TR>
	<TR bgcolor=\"#E7E7E7\">
		<TD>Nazwa</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"CYFRY[0]\" value=1 $chck[0]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[0]\" value=1 $chc[01]> = 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[0]\" value=2 $chc[02]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[0]\" value=3 $chc[03]> LIKE 
		</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"ALFAN[0]\" value=1 $chak[0]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[0]\" value=1 $cha[01]> = 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[0]\" value=2 $cha[02]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[0]\" value=3 $cha[03]> LIKE 
		</TD>
	</TR>
	<TR  bgcolor=\"#E7E7E7\">
		<TD>Indeks</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"CYFRY[1]\" value=1 $chck[1]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[1]\" value=1 $chc[11]> = 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[1]\" value=2 $chc[12]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[1]\" value=3 $chc[13]> LIKE 
		</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"ALFAN[1]\" value=1 $chak[1]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[1]\" value=1 $cha[11]> = 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[1]\" value=2 $cha[12]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[1]\" value=3 $cha[13]> LIKE 
		</TD>
	</TR>
	<TR bgcolor=\"#E7E7E7\">
		<TD>Klucze</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"CYFRY[2]\" value=1 $chck[2]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[2]\" value=1 $chc[21]> = 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[2]\" value=2 $chc[22]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[2]\" value=3 $chc[23]> LIKE 
		</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"ALFAN[2]\" value=1 $chak[2]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[2]\" value=1 $cha[21]> = 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[2]\" value=2 $cha[22]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[2]\" value=3 $cha[23]> LIKE 
		</TD>
	</TR>
	<TR bgcolor=\"#E7E7E7\">
		<TD>Ean</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"CYFRY[3]\" value=1 $chck[3]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[3]\" value=1 $chc[31]> = 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[3]\" value=2 $chc[32]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[3]\" value=3 $chc[33]> LIKE 
		</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"ALFAN[3]\" value=1 $chak[3]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[3]\" value=1 $cha[31]> = 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[3]\" value=2 $cha[32]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[3]\" value=3 $cha[33]> LIKE 
		</TD>
	</TR>
	<TR bgcolor=\"#E7E7E7\">
		<TD>Opis ma³y</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"CYFRY[4]\" value=1 $chck[4]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[4]\" value=1 $chc[41]> = 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[4]\" value=2 $chc[42]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[4]\" value=3 $chc[43]> LIKE 
		</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"ALFAN[4]\" value=1 $chak[4]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[4]\" value=1 $cha[41]> = 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[4]\" value=2 $cha[42]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[4]\" value=3 $cha[43]> LIKE 
		</TD>
	</TR>
	<TR bgcolor=\"#E7E7E7\">
		<TD>Opis du¿y</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"CYFRY[5]\" value=1 $chck[5]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[5]\" value=1 $chc[51]> = 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[5]\" value=2 $chc[52]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"CYFRYV[5]\" value=3 $chc[53]> LIKE 
		</TD>
		<TD>
		<INPUT TYPE=\"checkbox\" NAME=\"ALFAN[5]\" value=1 $chak[5]>
		&nbsp;&nbsp;
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[5]\" value=1 $cha[51]> = 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[5]\" value=2 $cha[52]> ~* 
		<INPUT TYPE=\"radio\" NAME=\"ALFANV[5]\" value=3 $cha[53]> LIKE 		
		</TD>
	</TR>
	<TR bgcolor=\"#E7E7E7\">
	<TD colspan=\"3\">
	<INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"but\">
	</TD>
	</TR>

	</TABLE>
	";
	echo " 
		<FORM METHOD=POST ACTION=\"$self\">		
		$tabelka
		</FORM>";

?>
