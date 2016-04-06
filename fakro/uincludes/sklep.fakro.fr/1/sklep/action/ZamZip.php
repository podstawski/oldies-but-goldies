<?
	$status = $LIST[status];
	
	if (!strlen($status)) return;

	$_KATALOG = "/var/tmp/".time()."/";

	$_plik = "";

	include ($SKLEP_INCLUDE_PATH."/raporty/daty.php");
	$status = $status+0;

	$sql = "SELECT * FROM zamowienia WHERE za_data >= $od AND za_data <= $do 
			AND za_status = $status
			ORDER BY za_data";
	
	$res=$adodb->execute($sql);
	
	if (!$res->RecordCount()) return;
	


	mkdir($_KATALOG);

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		parse_str(urldecode($za_parametry));
		
		$dat = date("d/m/Y",$za_data);
		$_plik = "INT".sprintf("%05d",$za_id);
		
		$sql = "SELECT su_login FROM system_user, zamowienia
				WHERE za_su_id = su_id 
				AND za_id = $za_id";
		
		parse_str(ado_query2url($sql));
		$uwagi = addslashes(stripslashes(ereg_replace("\r\n",";",trim($za_uwagi))));
		$linia = "\"$_plik\",\"$za_numer_obcy\",\"$dat\",\"$su_login\",\"$osoba\",\"$uwagi\"";
		$_mainfile = $_plik.".IM1";
		$_subfile = $_plik.".IM2";
		
		$f = fopen($_KATALOG.$_mainfile,"w");
		fwrite($f,$linia);
		fclose($f);

		$sql = "SELECT zampoz.*, towar.*
				FROM zampoz, towar, towar_sklep
				WHERE zp_za_id = $za_id 
				AND ts_to_id = to_id 
				AND zp_ts_id = ts_id";
		$res2=$adodb->execute($sql);
		$linia = "";
		for ($k=0; $k < $res2->RecordCount(); $k++)
		{
			parse_str(ado_explodename($res2,$k));
			$wartosc = ($zp_ilosc*$zp_cena);
			$linia.= "\"$to_indeks\",\"$zp_ilosc\",\"$wartosc\"\r\n";
		}
		$linia = substr($linia,0,-2);
		$f = fopen($_KATALOG.$_subfile,"w");
		fwrite($f,$linia);
		fclose($f);

	}
	$_zipname = $_KATALOG."zam.zip";
	exec("cd $_KATALOG;zip zam.zip *;rm *.IM[12]");
	//Header("Content-Type: application/zip; name=\"zam.zip\"");
	if(isset($HTTP_SERVER_VARS['HTTP_USER_AGENT']) and strpos($HTTP_SERVER_VARS['HTTP_USER_AGENT'],'MSIE'))
		Header('Content-Type: application/force-download');
	else
	Header('Content-Type: application/octet-stream');
	Header("Content-Length: ".filesize($_zipname)."");
	Header("Content-Disposition: attachment; filename=\"zam.zip\"");
	readfile($_zipname);
	unlink($_zipname);
	rmdir($_KATALOG);
	exit();
?>
