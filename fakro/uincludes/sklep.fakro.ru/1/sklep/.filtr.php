<?
	global $WYMIARY, $opt;
	if (!is_array($WYMIARY)) return;
	parse_str($costxt);

	if (strlen($FORM[kill_filtr]))
	{
		$tmp = "";
		$k=0;
		for ($i=0; $i < count($filtr); $i++)
			if ($i != $FORM[kill_filtr])
			{	
				$tmp.= "&filtr[$k]=".$filtr[$i];
				$k++;
			}
		$costxt = substr($tmp,1);
		$sql = "UPDATE webtd SET costxt = '$costxt' WHERE sid = $sid";
		$kameleon_adodb->execute($sql);
	}

	if (strlen($FORM[filtr_id]))
	{
		if (is_array($opt))
		{
			$lista = implode(",",$opt);
			$str = "filtr[".$FORM[filtr_id]."]=$lista&separator[".$FORM[filtr_id]."]=".$FORM[filtr_sep];
		}

		$tmp = "";
		$k=0;
		reset($filtr);
		for ($i=0; $i < count($filtr); $i++)
			if ($i != $FORM[filtr_id])
			{	
				if (is_array($opt)) $k = $i;
				$tmp.= "&filtr[$k]=".$filtr[$i]."&separator[$k]=".$separator[$i];
				$k++;
			}
		$costxt = substr($tmp,1)."&".$str;
		$sql = "UPDATE webtd SET costxt = '$costxt' WHERE sid = $sid";
		$kameleon_adodb->execute($sql);
	}
	$filtr = array();
	$separatror = array();
	parse_str($costxt);
	$table = "
	<TABLE width=\"100%\">
	<TR>
		<Th valign=\"top\">Numer filtra</Th>
		<Th valign=\"top\">Pola w filtrze</Th>
		<Th valign=\"top\"></Th>
	</TR>
	";
	
	$i=0;
	for($i=0;$i < count($filtr); $i++)
	{
		$pola = explode(",",$filtr[$i]);

		$cont = "";
		for ($k=0; $k < count($WYMIARY); $k++)
		{
			$chck = "";
				if (in_array($WYMIARY[$k],$pola))
					$chck = "checked";
			$cont.=" <INPUT TYPE=\"checkbox\" NAME=\"opt[".$WYMIARY[$k]."]\" value=\"".$WYMIARY[$k]."\" $chck> ".$WYMIARY[$k]."<br>";
		}
		$table.= "
		<FORM METHOD=POST ACTION=\"$self\">				
		<INPUT TYPE=\"hidden\" name=\"form[filtr_id]\" value=\"$i\">
		<TR>
			<TD valign=\"top\">Filtr ".($i+1)."</TD>
			<TD valign=\"top\">$cont</TD>
			<TD valign=\"top\">
			<img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" style=\"cursor:hand\" onClick=\"submit()\">
			<img src=\"$UIMAGES/autoryzacja/i_delete_n.gif\" style=\"cursor:hand\" onClick=\"usunFiltr('$i')\">
			</TD>
		</TR>
		<TR>
			<TD valign=\"top\">Separator</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" NAME=\"form[filtr_sep]\" value=\"".$separator[$i]."\" style=\"width:20px\"></TD>
			<TD valign=\"top\"></TD>
		</TR>
		<tr>
			<td colspan=3><hr></td>
		</tr>
		</FORM>";

	}
	$cont = "";
	for ($k=0; $k < count($WYMIARY); $k++)
		$cont.=" <INPUT TYPE=\"checkbox\" NAME=\"opt[".$WYMIARY[$k]."]\" value=\"".$WYMIARY[$k]."\"> ".$WYMIARY[$k]."<br>";
	$table.= "
	<FORM METHOD=POST ACTION=\"$self\">				
	<INPUT TYPE=\"hidden\" name=\"form[filtr_id]\" value=\"$i\">
	<TR>
		<TD valign=\"top\">Nowy filtr</TD>
		<TD valign=\"top\">$cont</TD>
		<TD valign=\"top\">
		<img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" style=\"cursor:hand\" onClick=\"submit()\">					
		</TD>
	</TR>
	<TR>
		<TD valign=\"top\">Separator</TD>
		<TD valign=\"top\"><INPUT TYPE=\"text\" NAME=\"form[filtr_sep]\" value=\"".$separator[$i]."\" style=\"width:20px\"></TD>
		<TD valign=\"top\"></TD>
	</TR>
	</FORM>";

	$table.= "</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killFiltr\">
	<INPUT TYPE=\"hidden\" name=\"form[kill_filtr]\" id=\"killid\">
	</FORM>
	";

	echo $table;
?>
<script>
	function usunFiltr(id)
	{
		if (confirm('Na pewno usunЙц ten filtr ?'))
		{
			document.killFiltr.killid.value = id;
			document.killFiltr.submit();
		}
	}

</script>
