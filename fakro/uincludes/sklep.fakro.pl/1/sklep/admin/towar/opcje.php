<?
	$LIST[id]=$CIACHO[admin_to_id];
	$to_id = $LIST[id];

	$sql = "SELECT * FROM opcje_towaru WHERE ot_to_id = $to_id";
	parse_str(ado_query2url($sql));
	$indx = "indeks produktu";
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"optForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TowarZapiszOpcje\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$to_id\">
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<TR>
		<TH colspan=2 class=\"c2\">
		Kategorie
		</TH>
		<TH class=\"c2\">&nbsp;</TH>
		<TH colspan=2 class=\"c2\">
		Opcje
		</TH>
	</TR>
	<TR>
		<TD colspan=2 class=\"c2\">
		<SELECT NAME=\"kat\" size=5 style=\"width:200px\" onChange=\"wybierzKategorie(this.value)\">
		</SELECT>
		</TD>
		<TD class=\"c2\">&nbsp;</TD>
		<TD colspan=2 class=\"c2\">
		<SELECT NAME=\"opt\" size=5 style=\"width:200px\" onChange=\"wybierzOpcje(this.value)\">
		</SELECT>
		</TD>
	</TR>
	<TR>
		<TD class=\"c2\"><INPUT TYPE=\"button\" value=\" + \" onClick=\"dodajKategorie()\"></TD>
		<TD class=\"c2\"><INPUT TYPE=\"button\" value=\" - \" onClick=\"usunKategorie()\"></TD>
		<TD class=\"c2\">&nbsp;</TD>
		<TD class=\"c2\"><INPUT TYPE=\"button\" value=\" + \" onClick=\"dodajOpcje()\"></TD>
		<TD class=\"c2\"><INPUT TYPE=\"button\" value=\" - \" onClick=\"usunOpcje()\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\" colspan=\"5\" align=\"right\"><INPUT TYPE=\"button\" value=\"Zapisz\" onClick=\"zapiszTablice()\"></TD>
	</TR>
	<INPUT TYPE=\"hidden\" name=\"form[opcje]\" id=\"finalopt\">
	</FORM>
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TowarOpcjeKopiuj\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$to_id\">
	<TR>
		<TD class=\"c2\" colspan=\"5\" align=\"right\">
		<INPUT TYPE=\"text\" value=\"$indx\" name=\"form[copyfrom]\" onClick=\"indxOnClick_$sid(this)\" onBlur=\"indxOnBlur_$sid(this)\">
		<INPUT TYPE=\"submit\" value=\"Kopiuj\"></TD>
	</TR>
	</TABLE>	
	</FORM>
	";

?>
<script>
	
	var opcje = new Array();
	var wybranaKategoria = null;
	var wybranaOpcja = null;
	var select_kat = document.optForm.kat;
	var select_opt = document.optForm.opt;
	var final_tekst = getObject('finalopt');

	function zapiszTablice()
	{
		tekst = "";
		for (key in opcje)
		{
			if (opcje[''+key+''] == null) continue;
			if (tekst != "") tekst+="\n";
			tekst+= key+":";
			subcount=0;
			for (subkey in opcje[''+key+''])
			{
				if (opcje[''+key+''][''+subkey+''] == null) continue;
				if (subcount!=0) tekst+="|";
				tekst+=subkey;
				subcount++;
			}
		}
		final_tekst.value = tekst;
		document.optForm.submit();
	}

	function przeladujOpcje()
	{
		select_opt.length = 0;
		for (key in opcje[''+wybranaKategoria+''])
		{
			if (opcje[''+wybranaKategoria+''][''+key+''] == null) continue;
			last = select_opt.length;
			nowy = new Option(key,key,0,0);
	    	select_opt[last] = nowy;		
		}
	}

	function wybierzKategorie(id)
	{
		if (id != null && id != "")
		{
			wybranaKategoria = id;
			przeladujOpcje();
		}
	}

	function wybierzOpcje(id)
	{
		if (id != null && id != "")
			wybranaOpcja = id;
	}

	function odswiezListy()
	{
		select_kat.length = 0;
		for (key in opcje)
		{
			if (opcje[''+key+''] == null) continue;
			last = select_kat.length;
			nowy = new Option(key,key,0,0);
			if (wybranaKategoria == key) 
				nowy.selected = true;
    		select_kat[last] = nowy;		
		}

		if (wybranaKategoria != null && wybranaKategoria != "")
			przeladujOpcje();
	}

	function dodajKategorie()
	{
		nazwa = prompt('Nazwa',"");
		if (nazwa != "" && nazwa != null)
		{
			opcje[''+nazwa+''] = new Array();
			wybranaKategoria = nazwa;
			odswiezListy();
		}
	}
	
	function dodajOpcje()
	{
		if (wybranaKategoria == null || wybranaKategoria == "")
		{
			alert('Proszę wybrać kategorię.');
			return;
		}

		nazwa = prompt('Nazwa',"");
		if (nazwa != "" && nazwa != null)
		{
			opcje[''+wybranaKategoria+''][''+nazwa+''] = nazwa;
			odswiezListy();
		}

	}
	
	function usunKategorie()
	{
		if (wybranaKategoria == null || wybranaKategoria == "")
		{
			alert('Proszę wybrać kategorię.');
			return;
		}
		opcje[''+wybranaKategoria+''] = null;
		odswiezListy();
	}
	
	function usunOpcje()
	{
		if (wybranaKategoria == null || wybranaKategoria == "")
		{
			alert('Proszę wybrać kategorię.');
			return;
		}

		if (wybranaOpcja == null || wybranaOpcja == "")
		{
			alert('Proszę wybrać opcję.');
			return;
		}

		opcje[''+wybranaKategoria+''][''+wybranaOpcja+''] = null;
		odswiezListy();
	}

	function indxOnClick_<?echo $sid?>(obj)
	{
		if (obj.value=='<?echo $indx?>') obj.value='';
	}

	function indxOnBlur_<?echo $sid?>(obj)
	{
		if (obj.value.length == 0) obj.value='<?echo $indx?>';
	}


<?
	if (strlen($ot_opcje))
	{
		$arr = explode("\n",$ot_opcje);
		for ($i=0; $i < count($arr); $i++)
		{
			$opt = explode(":",$arr[$i]);
			echo "
			opcje['".addslashes(stripslashes(trim($opt[0])))."'] = new Array();";
			$subopt = explode("|",$opt[1]);
			for ($k=0; $k < count($subopt); $k++)
			{
				echo "
				opcje['".addslashes(stripslashes(trim($opt[0])))."']['".addslashes(stripslashes(trim($subopt[$k])))."'] = '1';";
			}

		}
		echo "
		odswiezListy();
		";
	}
?>

</script>
