<?
	global $WEBPAGE,$WEBTD,$SKLEP_SESSION;
	if (!is_object($WEBPAGE)) return;

	//if (is_array($SKLEP_SESSION[kategorie])) return;

	function wszystkie_kategorie($adodb,$page,$path="")
	{
		global $ver,$lang,$SERVER_ID;
		global $KAMELEON_MODE;


		$query="SELECT id,title
			FROM webpage WHERE prev=$page 
			AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
			AND (hidden IS NULL OR hidden=0)
			AND (nositemap IS NULL OR nositemap=0)
			ORDER BY title_short,title";
		$result=$adodb->Execute($query);
		if (!$result) return "";
		if (!$result->RecordCount()) return;


		if (strlen($path)) $path.= " &raquo; ";
		
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_explodeName($result,$i));
			$href=kameleon_href("","",$id);

			push($KAMELEON_MODE);
			$KAMELEON_MODE=0;
			$href_nkm=kameleon_href("","",$id);
			$KAMELEON_MODE=pop();

			$p=$path.$title;
			$t=strtolower($title);

			$ka_id=0;
			$sql="SELECT ka_id FROM kategorie WHERE ka_kod='$id'";
			parse_str(ado_query2url($query));
			$wynik["${id}_$t"]=array($href,$p,$href_nkm,$ka_id);

			$sub=wszystkie_kategorie($adodb,$id,$p);
			if (is_array($sub)) $wynik=array_merge($wynik,$sub);
				
		}
		
		return $wynik;
	}

	
	


	if (!$KAMELEON_MODE)
	{
		echo "<?\n";
		echo "	if (!is_array(\$SKLEP_SESSION[kategorie]))\n	{\n";
		echo "		\$kategorie=array();\n";
		echo "		include(\"\$UFILES/kategorie.php\");\n";
		echo "		session_register(\"kategorie\");\n";
		echo "		\$SKLEP_SESSION[kategorie]=\$kategorie;\n";
		echo "	}\n?>\n";
	}
	else
	{
		$k=wszystkie_kategorie($kameleon_adodb,$WEBTD->next);

		$SKLEP_SESSION[kategorie]=$k;
		$plik=fopen("$UFILES/kategorie.php","w");
		fwrite($plik,"<?\n");

		while(list($kat,$a)=each($k))
		{
			fwrite($plik,"\$kategorie[\"$kat\"]=array(\"$a[2]\",\"$a[1]\",\"$a[3]\");\n");
		}
		fwrite($plik,"?>");

		fclose($plik);
		
	}
?>
