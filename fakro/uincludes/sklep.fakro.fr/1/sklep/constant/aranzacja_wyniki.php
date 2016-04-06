<?
	global $kolor, $typ;

	if (!$size) $size=6;


	if (!strlen($kolor)) return;

	if (!function_exists("getAllSubcategories"))
	{
		function getAllSubcategories($id)
		{
			global $adodb;
			
			$sql = "SELECT ka_id FROM kategorie WHERE ka_parent = $id";
			$res = $adodb->execute($sql);
			if ($res->RecordCount())
			{
				for ($i=0; $i < $res->RecordCount(); $i++)
				{
					parse_str(ado_explodename($res,$i));
					$ret.= ",".getAllSubcategories($ka_id);
				}
				return substr($ret,1);
			}
			else
				return $id;
		}	
	}

	if (!function_exists("kameleon_href"))
	{
		function kameleon_href($f="",$f2="",$link)
		{
			return $link.".php";
		}
	}

	$sub_kolory = getAllSubcategories($kolor);
	if (strlen($typ))
		$sub_typy = getAllSubcategories($typ);

	$kolor_val = implode(",",array_unique(explode(",",$sub_kolory)));

	$sql = "SELECT tk_to_id FROM towar_kategoria WHERE tk_ka_id IN ($kolor_val)";
	$adodb->debug=0;
	$res = $adodb->execute($sql);
	$znalezione_kolory = array();

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$znalezione_kolory[] = $tk_to_id;
	}

	if (strlen($typ))
	{
		$typ_val = implode(",",array_unique(explode(",",$sub_typy)));

		$sql = "SELECT tk_to_id FROM towar_kategoria WHERE tk_ka_id IN ($typ_val)";
		$res = $adodb->execute($sql);
		$znalezione_typy = array();
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$znalezione_typy[] = $tk_to_id;
		}
		
		$lista = implode(",",array_intersect($znalezione_kolory,$znalezione_typy));
		$typy_sql = "AND tk_ka_id IN ($typ_val)";
	}
	else
		$lista = implode(",",$znalezione_kolory);

//			WHERE tk_to_id IN ($lista) 
//			AND tk_ka_id IN ($typ_val)

	$sql = "SELECT tk_ka_id, ka_nazwa, ka_foto_m, ka_foto_d, ka_kod FROM towar_kategoria, kategorie 
			WHERE tk_to_id IN ($lista) 
			$typy_sql
			AND ka_id = tk_ka_id
			GROUP BY tk_ka_id, ka_nazwa, ka_foto_m, ka_foto_d, ka_kod
			ORDER BY ka_nazwa";
	
	$res = $adodb->execute($sql);
	echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"2\"><tr>";
	$x = 0;
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

//		if (substr_count($ka_nazwa,"-") > 1) continue;

		if ($LINK_TO_PAGE[$ka_kod]) continue;
		$LINK_TO_PAGE[$ka_kod]=true;

		$link_galery = kameleon_href("","",$ka_kod);
		

		$_sub_nazwa = $ka_nazwa;
		$_sub_nazwa = str_replace("-E-","_",$_sub_nazwa);
		$_sub_nazwa = str_replace("-H-","_",$_sub_nazwa);
		$_sub_nazwa = str_replace("-B-","_",$_sub_nazwa);
		$_sub_nazwa = str_replace("-Solar-","_",$_sub_nazwa);
		$_sub_nazwa = str_replace("AMK","AMZ",$_sub_nazwa);
		$_sub_nazwa = str_replace("AME","AMZ",$_sub_nazwa);
		$_sub_nazwa = str_replace("ARK","ARZ",$_sub_nazwa);
		$_sub_nazwa = str_replace("-","_",$_sub_nazwa);

		#$ka_foto_m = "kat/".strtolower($_sub_nazwa)."_m.jpg";
		#$ka_foto_d = "kat/".strtolower($_sub_nazwa).".jpg";

		$ka_foto_m = "COMMON/product/sample/".strtolower($_sub_nazwa)."_m.jpg";
		$ka_foto_d = "COMMON/product/sample/".strtolower($_sub_nazwa).".jpg";
		
		$_abegin="<a href=\"".$link_galery."\" $link_target>";	
		$_aend = "</a><br>";

		$txt_js = "<a href=\'".$link_galery."\' id=\'inlink\' target=\'_tst\' onclick=window.close(); style=\'color:#666285\;text-decoration:none;font-weight:bold;\'>".sysmsg('Choose size','kategorie')."</a>";
				
		if ($ka_foto_d) 
		{
			$_src = $UIMAGES."/".$ka_foto_d;
			if (file_exists($_src))
				$_size_pp = getimagesize($_src);
			else
				continue;
		}

		$_popup="onClick=\"return galeriaOnclick(this,'$UIMAGES/$ka_foto_d','$txt_js','$link_galery');\"";


		$nazwa_prefix=substr($ka_nazwa,0,3);

		if ($old_nazwa_prefix!=$nazwa_prefix)
		{
			$old_nazwa_prefix=$nazwa_prefix;
			if ($i) echo "</tr><tr>";
		
			$sysmsg_kategoria=sysmsg($nazwa_prefix,'kategorie');
			echo "<td colspan=\"$size\"><h1>$sysmsg_kategoria</h1></td></tr><tr>";
			$x=0;
		}

		if ($x && !($x%$size)) 	
		{
			echo "</tr><tr>";
			$x=0;
		}
		echo "<td align=\"center\" class=\"palety\">";


		echo $_abegin."<img src=\"$UIMAGES/$ka_foto_m\" border=0 hspace=0 vspace=0 
						alt=\"".$ka_nazwa."\" title=\"".$ka_nazwa."\" ".$_size[3].$_popup.">".$_aend;
/*
		$probka_display=sysmsg('display_'.strtolower($nazwa_prefix),'probki');

		echo "<img src=\"$IMAGES/i_specimen2.gif\" alt=\"".sysmsg('order sample','probki')."\" align=\"absmiddle\" class=\"specimen\"
				onclick=\"KOSZJS.putItem('$ka_nazwa $sysmsg_kategoria|$UIMAGES/$ka_foto_m')\" 
				style=\"cursor: pointer;display:$probka_display\">";
*/
		echo "<a href=\"".$link_galery."\" $link_target title=\"".$ka_nazwa."\">".$ka_nazwa."</a></td>";
		$x++;
	}
	echo "</table>";

echo "
<script>
	findAndSelect(document.getElementById('kol'),'$kolor');
	wSetSelect(document.getElementById('kol'),document.getElementById('typ'),'typ')
";	
if (strlen($typ))
echo "
	findAndSelect(document.getElementById('typ'),'$typ');
	wSetSelect(document.getElementById('kol'),document.getElementById('typ'),'kolor')
	";
echo "
</script>
";

?>
