<?

	$sql="UPDATE zamowienia SET za_data_realizacji=za_data_przyjecia WHERE za_status=-1 AND za_data_realizacji IS NULL";
	$adodb->execute($sql);


	$sql="UPDATE zamowienia SET za_data_status=za_data_przyjecia WHERE za_status>0 AND za_data_status IS NULL";
	$adodb->execute($sql);
	$sql="UPDATE zamowienia SET za_data_status=za_data WHERE za_status=0 AND za_data_status IS NULL";
	$adodb->execute($sql);

	$sql="UPDATE zamowienia SET za_data_status=za_data WHERE za_status=-5 AND za_data_status IS NULL";
	$adodb->execute($sql);

	$sql="UPDATE zamowienia SET za_data_status=za_data_realizacji WHERE za_status=-1 AND za_data_status IS NULL";
	$adodb->execute($sql);

	if ($KAMELEON_MODE && !strlen($printlist) ) $printlist="index.php?page=197";


	$rodzaj = $cos+0;
	include ($SKLEP_INCLUDE_PATH."/raporty/daty.php");


	$DATA_KONTEKST='za_data';
	if ($rodzaj>=1) $DATA_KONTEKST='za_data_przyjecia';
	if ($rodzaj==-1) $DATA_KONTEKST='za_data_realizacji';

	
	$DATA_KONTEKST='za_data_status';

	if ($CIACHO[kontrahent_id]) $DATA_KONTEKST='za_data';


	$ZASTOSOWANO_FILTR="<B>".sysmsg('Time range','admin').":</B> ".humandate($od)." do ".humandate($do)."<br>";;
	$su_options="";
	$sql = "SELECT su_nazwisko,su_id FROM system_user 
			WHERE su_parent IS NULL ORDER BY su_nazwisko";
	$result = $adodb->execute($sql);
	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));

		$sel=($CIACHO[kontrahent_id]==$su_id) ? "selected":"sel";
		$su_options.="<option value='$su_id' $sel>".stripslashes($su_nazwisko)."</option>\n";

		if ($CIACHO[kontrahent_id]==$su_id) $ZASTOSOWANO_FILTR.="<B>".sysmsg('Limit','admin').":</B> ".sysmsg('customer','admin')." = <B>$su_nazwisko</B><br>"; 
	}
	
	if (strlen($su_options))
	{
		echo "<div align='right'>
				<select name='ciacho[kontrahent_id]' 
					onChange=\"document.cookie=this.name+'='+this.value+';path=/'; document.list_sort_form.s_ile.value=''; document.list_sort_form.s_start.value=0; document.list_sort_form.submit()\">
				<option value=0>".sysmsg('All customers','admin')."</option>
				$su_options
				</select>
			</div>";
	}

	if (!strlen($LIST[sort_f])) $LIST[sort_f]=$DATA_KONTEKST;
	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	
	

	$FROMWHERE = "FROM zamowienia,system_user
					WHERE ($DATA_KONTEKST >= $od OR $DATA_KONTEKST IS NULL) AND ( $DATA_KONTEKST <= $do OR $DATA_KONTEKST IS NULL)
					AND su_id=za_su_id 			
					";

	if ($CIACHO[kontrahent_id]) $FROMWHERE.="AND za_su_id=".$CIACHO[kontrahent_id];

	$statusy_cookie=$_REQUEST[statusy];
	if (is_array($statusy_cookie))
	{
		$statusy="-123";
		while(list($s,$v)=each($statusy_cookie))
		{
			if (!$v) continue;
			$statusy.=",$s";
			$sfiltr.=": ".sysmsg("status_-5","status");

		}
		$FROMWHERE.=" AND za_status IN ($statusy)";
		$ZASTOSOWANO_FILTR.="Statusy$sfiltr";
	}
	elseif  (!$CIACHO[kontrahent_id]) $FROMWHERE.="AND za_status = $rodzaj";

	$sql = "SELECT * $FROMWHERE ORDER BY ".$sort;


	//$projdb->debug=1;

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}
	$navi=$size?navi($self,$LIST,$size):"";
		
	if (strlen($navi))
		$res= $projdb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$res = $projdb->Execute($sql);	


	include ($SKLEP_INCLUDE_PATH."/list.h");

	$pl="";
	if (strlen($printlist)) $pl="<th width=10>&nbsp;</th>"; 
	$table = "$ZASTOSOWANO_FILTR<br>$navi
	<form method=\"POST\" action=\"$printlist\">
	<table id=\"tzam\" class=\"list_table\">
	<TR>
		<th width=10>Lp.</th>
		<th sort='$DATA_KONTEKST' nowrap>".sysmsg('Date / no','admin')."</th>
	";
	if ($CIACHO[kontrahent_id])
		$table.="<th>".sysmsg('Status','admin')."</th>";
	else
		$table.="<th sort='su_nazwisko'>".sysmsg('Customer','admin')."</th>";
	$table.="
		<th sort='za_wart_nt'>".sysmsg('Value','admin')."</th>
		<th width=\"1%\">".sysmsg('Actions','admin')."</th>
	</TR>";

	$qs=sort_navi_qs($LIST);

	$downup=explode(":",$costxt);
	$down=$downup[0];
	$up=$downup[1];
	if (!strlen($down) && count($downup)<2 )
	{
		switch ($rodzaj)
		{
			case 0:
				$down=-5;
				break;
			case 1:
				$down=0;
				break;
			case -1:
				$down=1;
				break;
		}

	}
	if (!strlen($up) && count($downup)<2 )
	{
		switch ($rodzaj)
		{
			case 0:
				$up=1;
				break;
			case 1:
				$up=-1;
				break;
			case -5:
				$up=0;
				break;
		}

	}


	$i_down="i_delete.gif";
	$i_up="tak.gif";

	if (file_exists("$SKLEP_IMAGES/status-$up.gif")) $i_up="status-$up.gif";
	if (file_exists("$SKLEP_IMAGES/status-$down.gif")) $i_down="status-$down.gif";
		


	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$kontrahent = "";
		
		if (!strlen($su_nazwisko))
		{
			$sql = "SELECT su_imiona, su_nazwisko FROM system_user WHERE su_parent = $su_id";
			parse_str(ado_query2url($sql));
			$su_nazwisko = $su_imiona." ".$su_nazwisko;
		}	
		$kontrahent = "$su_nazwisko $su_miasto";

    $ri_procent = 0;
    if($za_voucher_id) {
      $sql1 = "SELECT * FROM towar t
      LEFT JOIN towar_kategoria kt ON (t.to_id = kt.tk_to_id)
      LEFT JOIN kategorie k ON (kt.tk_ka_id = k.ka_id)
      LEFT JOIN rabat_ilosciowy ri ON (k.ka_id = ri.ri_ka_id)
      WHERE t.to_indeks='".$za_voucher_id."'
      AND k.ka_nazwa='Coupons'";
      parse_str(ado_query2url($sql1));
    }
		
		if (!$za_wart_nt) {
			$query="SELECT sum(zp_ilosc*zp_cena) AS za_wart_nt FROM zampoz WHERE zp_za_id=$za_id";
			parse_str(ado_query2url($query));
		}
		
    if($ri_procent) {
      $za_wart_br = ($za_wart_br-(($za_wart_br*$ri_procent)/100));
      if($za_wart_br<0) $za_wart_br = 0;
    }
    
    $wartosc = u_cena($za_wart_br + $za_poczta_br);

		$buttons = "";

		$buttons.= "<a href=\"$next${next_char}list[za_id]=$za_id\"><img src=\"$SKLEP_IMAGES/i_zobacz.gif\" 
						alt=\"".sysmsg('Look','admin')."\" border=\"0\"></a>";


		if (strlen($up))
			$buttons.= "<img src=\"$SKLEP_IMAGES/$i_up\" onClick=\"acceptItem('$za_id')\" 
							style=\"cursor:hand\" hspace=2 alt=\"".sysmsg('Set status','admin').": ".sysmsg("status_$up",'status')."\">";
		if (strlen($down))
			$buttons.= "<img src=\"$SKLEP_IMAGES/$i_down\" onClick=\"rejectItem('$za_id')\" 
							style=\"cursor:hand\" hspace=2 alt=\"".sysmsg('Set status','admin').": ".sysmsg("status_$down",'status')."\">";

		


		$altp=sysmsg("Print PDF","system");
		$printbtn = "<a href=\"$self${next_char}action=PDF&list[pdf]=zamowienie&list[prn]=1$_more&list[id]=$za_id&list[show_indx]=1\" target=\"pdf\"><img src=\"$SKLEP_IMAGES/i_acroread.gif\" alt=\"$altp\" border=0></a>";

		$buttons.=" &nbsp; ".$printbtn;

		if (file_exists("$SOAP_PATH/WS_DodajZamowienie.h") && $rodzaj==0 && !strlen($za_ws) ) 
		{
			
			$buttons.=" <a href='$self${next_char}$qs&list[za_id]=$za_id&action=WS_DodajZamowienie'><img src=\"$SKLEP_IMAGES/i_ws.gif\" border=0 align=\"absMiddle\"></a>";
		}

		if (file_exists("$SOAP_PATH/WS_StatusZamowienia.h") && $rodzaj==1 && strlen($za_ws) ) 
		{
			
			$buttons.=" <a href='$self${next_char}$qs&list[za_id]=$za_id&action=WS_StatusZamowienia'><img src=\"$SKLEP_IMAGES/i_ws.gif\" border=0 align=\"absMiddle\"></a>";
		}

		if ($more!=$self)
			$kontrahent="<a href=\"javascript:\" 
						onClick=\"kartoteka_popup('$more${next_char}sc[admin_su_id]=$su_id','kontrahent');\">$kontrahent</a>";

		$pl="";
		
		if (strlen($printlist)) $pl=" <input title=\"".sysmsg('Check for print list','admin')."\" type=\"checkbox\" name=\"printlist[$za_id]\" value=1>";

		parse_str($za_parametry);
		if (strstr($platnosc,"ele") || strstr($platnosc,"red"))
			$rodzaj_p = "<br><img src=\"".$UIMAGES."/sb/pln.gif\" border=\"0\">";
		else
			$rodzaj_p = "";

		if ($CIACHO[kontrahent_id]) $kontrahent=sysmsg("status_$za_status",'status');

		$table.= "
		<TR dbid=\"$za_id\" >
			<td width=10>".($i+1)."
			<td nowrap><a href=\"$next${next_char}list[za_id]=$za_id\">".date("d-m-Y",$$DATA_KONTEKST)." / $za_numer_obcy $rodzaj_p</a>
			<td>".stripslashes($kontrahent)."
			<td align='right' nowrap>$wartosc
			<td nowrap>$buttons$pl </td>
		</tr>";
	}
	
	$table.="</TABLE>";
	
	global $REQUEST_URI;
	if (strlen($printlist)) $table.="
	
	<p class=\"printlist\" align=\"right\">
	<input type=\"hidden\" value=\"$REQUEST_URI\" name=\"location_back\">
	<input type=\"submit\" value=\"".sysmsg('printlist','order')."\" class=\"button\">
	</p>";
	
	$table.="</form>
	<FORM METHOD=POST ACTION=\"$self\" name=\"acceptForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZamowienieUp\">
	<INPUT TYPE=\"hidden\" id=\"accept_id\" name=\"form[accept_id]\">
	<INPUT TYPE=\"hidden\" id=\"kom\" name=\"form[kom]\">
	<INPUT TYPE=\"hidden\" value=\"$rodzaj\" name=\"form[acc_status]\">
	<INPUT TYPE=\"hidden\" value=\"\" id=\"new_status_a\" name=\"form[new_status]\">
	</FORM>
	<FORM METHOD=POST ACTION=\"$self\" name=\"rejectForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZamowienieDown\">
	<INPUT TYPE=\"hidden\" id=\"reject_id\" name=\"form[reject_id]\">
	<INPUT TYPE=\"hidden\" id=\"kom\" name=\"form[kom]\">
	<INPUT TYPE=\"hidden\" value=\"$rodzaj\" name=\"form[acc_status]\">
	<INPUT TYPE=\"hidden\" value=\"\" id=\"new_status_r\" name=\"form[new_status]\">
	</FORM>
	";
	
	if ($res->RecordCount()) 
		echo $table;
	else
		return;




?>
<script>
list_table_init('tzam','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_selected_item()
	{
		table=getObject('tzam');
		if (!table.selectedId) return;
		location.href='<? echo $next.$next_char;?>list[za_id]='+table.selectedId;
	}

	function list_selected_item()
	{		
	}

	function acceptItem(id)
	{
		kom = prompt('<? echo sysmsg('Accept notice','admin') ?>','<? echo sysmsg('default_accept_notice','admin') ?>');
		if (kom==null) return;
		document.acceptForm.kom.value = kom;
		document.acceptForm.accept_id.value = id;
		document.acceptForm.new_status_a.value = <?echo $up+0?>;
		document.acceptForm.submit();
	}

	function rejectItem(id)
	{
		<?
			if ($rodzaj == 0)
			echo "
			";
		?>

		kom = prompt('<? echo sysmsg('Reject notice','admin') ?>','<? echo sysmsg('default_reject_notice','admin') ?>');
		if (kom==null) return;
		document.rejectForm.kom.value = kom;

		document.rejectForm.reject_id.value = id;
		document.rejectForm.new_status_r.value = <?echo $down+0?>;
		document.rejectForm.submit();
	}

</script>
