<?
	global $FILTR;

	parse_str($costxt);

	if (!$KAMELEON_MODE)
	{
		$sesfiltr = $SKLEP_SESSION["sesfiltr_$sid"];
		$cfiltr = $SKLEP_SESSION["CFILTR[$sid]"];
		$cfiltr_s = $SKLEP_SESSION["CFILTR_S[$sid]"];
	}
	
	if (is_array($cfiltr) && !is_array($FILTR))
	{
		$FILTR = $cfiltr;
		$FILTR_S = $cfiltr_s;
	}

	if (!is_array($filtr)) return;

	$query="SELECT ka_id FROM kategorie WHERE ka_kod='$page'";
	parse_str(ado_query2url($query));
	if (!$ka_id) return;

	$sel = "";
	for ($i=0; $i < count($filtr); $i++)
	{
		$pola = explode(",",$filtr[$i]);
		$sep = $separator[$i];
		if (!strlen($sep)) $sep = " ";
		$ind = $filtr[$i];
		$header = "";
		for ($k=0; $k < count($pola); $k++)
			$header.= $sep.sysmsg("th_".$pola[$k],"system");
		$header = substr($header,1);

		$sql = "SELECT ".$filtr[$i]." FROM towar
				LEFT JOIN towar_parametry ON tp_to_id = to_id
				LEFT JOIN towar_sklep ON ts_to_id = to_id AND ts_sk_id=$SKLEP_ID
				,towar_kategoria
				WHERE tk_ka_id=$ka_id  
				AND tk_to_id=to_id AND ts_sk_id=$SKLEP_ID 
				GROUP BY ".$filtr[$i]." ORDER BY ".$filtr[$i];
		if (!is_array($sesfiltr[$ind])|| $KAMELEON_MODE)
			$res = $adodb->execute($sql);
		$cont = "<option value=\"\">$header: ".sysmsg("Choose","system")."</option>\n";
		if (!is_array($sesfiltr[$ind]) || $KAMELEON_MODE)
			for ($k=0; $k < $res->RecordCount(); $k++)
			{
				parse_str(ado_explodename($res,$k));
				$to_parse= "";
				for ($x=0; $x < count($pola); $x++)
					$to_parse.= "$sep".$$pola[$x];
				$to_parse = substr($to_parse,1);
				if ($FILTR[$ind] == $to_parse)
					$chck = "selected";
				else
					$chck = "";
				if (!strlen($to_parse)) continue;
				$cont.= "<option value=\"$to_parse\" $chck>$to_parse</option>\n";
				$sesfiltr[$ind][$k] = $to_parse;	
			}
		else
			for ($k=0; $k < count($sesfiltr[$ind]); $k++)
			{
				$to_parse = $sesfiltr[$ind][$k];
				if ($FILTR[$ind] == $to_parse)
					$chck = "selected";
				else
					$chck = "";
				$cont.= "<option value=\"$to_parse\" $chck>$to_parse</option>\n";					
			}


		$sel.= "<td><SELECT NAME=\"FILTR[".$filtr[$i]."]\">$cont</SELECT>
		<INPUT TYPE=\"hidden\" name=\"FILTR_S[".$filtr[$i]."]\" value=\"$sep\">
		</td>";
	}

	echo "
	<FORM METHOD=POST ACTION=\"$self\">
	<table>
	<tr>
	$sel
	<td><INPUT TYPE=\"submit\" class=\"but\" value=\"".sysmsg("Set filter","system")."\"></td>
	</tr>
	</table>
	</FORM>
	";
	if (!$KAMELEON_MODE)
	{
		$SKLEP_SESSION["sesfiltr_$sid"] = $sesfiltr;
		$SKLEP_SESSION["CFILTR[$sid]"] = $FILTR;
		$SKLEP_SESSION["CFILTR_S[$sid]"] = $FILTR_S;
		session_register("sesfiltr_$sid");
		session_register("CFILTR[$sid]");
		session_register("CFILTR_S[$sid]");
	}
?>
