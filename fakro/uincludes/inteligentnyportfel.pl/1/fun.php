<?
if (!function_exists(win2iso)) {
	function win2iso($f_text)
	{
		$f_text = strtr($f_text, '', 'ś');
		$f_text = strtr($f_text, 'š', 'ą');
		$f_text = strtr($f_text, '', 'ź');
		$f_text = strtr($f_text, '', 'Ś');
		$f_text = strtr($f_text, 'Ľ', 'Ą');
		$f_text = strtr($f_text, '', 'Ź');
		
		return $f_text;
	}
}

if (!function_exists(iso2win)) {
	function iso2win($f_text)
	{
		$f_text = strtr($f_text, 'ś', '');
		$f_text = strtr($f_text, 'ą', 'š');
		$f_text = strtr($f_text, 'ź', '');
		$f_text = strtr($f_text, 'Ś', '');
		$f_text = strtr($f_text, 'Ą', 'Ľ');
		$f_text = strtr($f_text, 'Ź', '');
	
		return $f_text;
	}
}	

if (!function_exists(b_unpolish)) {	
	function b_unpolish($text)
	{
	 $text=ereg_replace("š","a",$text);
	 $text=ereg_replace("ę","e",$text);
	 $text=ereg_replace("ń","n",$text);
	 $text=ereg_replace("ł","l",$text);
	 $text=ereg_replace("ó","o",$text);
	 $text=ereg_replace("ż","z",$text);
	 $text=ereg_replace("","z",$text);
	 $text=ereg_replace("ć","c",$text);
	 $text=ereg_replace("","s",$text);
	 $text=ereg_replace("ą","a",$text);
	 $text=ereg_replace("ś","s",$text);
	 $text=ereg_replace("ź","z",$text);
	
	 $text=ereg_replace("Ľ","A",$text);
	 $text=ereg_replace("Ę","E",$text);
	 $text=ereg_replace("Ń","N",$text);
	 $text=ereg_replace("Ł","L",$text);
	 $text=ereg_replace("Ó","O",$text);
	 $text=ereg_replace("Ż","Z",$text);
	 $text=ereg_replace("","Z",$text);
	 $text=ereg_replace("Ć","C",$text);
	 $text=ereg_replace("","S",$text);
	 $text=ereg_replace("Ą","A",$text);
	 $text=ereg_replace("Ś","S",$text);
	 $text=ereg_replace("Ź","Z",$text);
	
	 $text=ereg_replace("&#261;","a",$text);
	 $text=ereg_replace("&#281;","e",$text);
	 $text=ereg_replace("&#324;","n",$text);
	 $text=ereg_replace("&#322;","l",$text);
	 $text=ereg_replace("&#380;","z",$text);
	 $text=ereg_replace("&#378;","z",$text);
	 $text=ereg_replace("&#263;","c",$text);
	 $text=ereg_replace("&#347;","s",$text);
	
	 $text=ereg_replace("&#260;","A",$text);
	 $text=ereg_replace("&#280;","E",$text);
	 $text=ereg_replace("&#323;","N",$text);
	 $text=ereg_replace("&#321;","L",$text);
	 $text=ereg_replace("&#379;","Z",$text);
	 $text=ereg_replace("&#377;","Z",$text);
	 $text=ereg_replace("&#262;","C",$text);
	 $text=ereg_replace("&#346;","S",$text);
	
	 return $text;
	}
}	
	
if (!function_exists(utf82iso)) {		
	function utf82iso($f_text)
	{

		$f_text = str_replace("Ĺ","ł",$f_text);
		$f_text = str_replace("ł","ł",$f_text);
		$f_text = str_replace("Ĺ","Ł",$f_text);
		$f_text = str_replace("Ĺ","ń",$f_text);
		$f_text = str_replace("Ĺ","Ń",$f_text);
		$f_text = str_replace("Ä","ę",$f_text);
		$f_text = str_replace("Ä","Ę",$f_text);
		$f_text = str_replace("Ăł","ó",$f_text);
		$f_text = str_replace("ó","ó",$f_text);
		$f_text = str_replace("Ă","Ó",$f_text);
		$f_text = str_replace("Ĺź","ż",$f_text);
		$f_text = str_replace("łť","ż",$f_text);
		$f_text = str_replace("Ĺť","Ż",$f_text);
		$f_text = str_replace("Ĺ","",$f_text);
		$f_text = str_replace("ž","ą",$f_text);//š		
		$f_text = str_replace("Ä","ą",$f_text);//š
		$f_text = str_replace("Ä","Ą",$f_text);//Ľ
		$f_text = str_replace("Ä","ź",$f_text);//Ľ
		$f_text = str_replace("ł","ś",$f_text);//
		$f_text = str_replace("Ĺ","Ś",$f_text);//		
		$f_text = str_replace("ę","ć",$f_text);
		$f_text = str_replace("Ä","Ć",$f_text);

		return $f_text;
	}
}

if (!function_exists("iso2utf")) {
	function iso2utf($f_text)
	{
		$iso88592 = array(
	   'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â',
	   'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â',
	   'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â',
	   'Â', 'Â', 'Â ', 'Ä', 'Ë', 'Ĺ', 'Â¤', 'Ä˝', 'Ĺ', 'Â§',
	   'Â¨', 'Ĺ ', 'Ĺ', 'Ĺ¤', 'Ĺš', 'Â­', 'Ĺ˝', 'Ĺť', 'Â°', 'Ä',
	   'Ë', 'Ĺ', 'Â´', 'Äž', 'Ĺ', 'Ë', 'Â¸', 'ĹĄ', 'Ĺ', 'ĹĽ',
	   'Ĺş', 'Ë', 'Ĺž', 'Ĺź', 'Ĺ', 'Ă', 'Ă', 'Ä', 'Ă', 'Äš',
	   'Ä', 'Ă', 'Ä', 'Ă', 'Ä', 'Ă', 'Ä', 'Ă', 'Ă', 'Ä',
	   'Ä', 'Ĺ', 'Ĺ', 'Ă', 'Ă', 'Ĺ', 'Ă', 'Ă', 'Ĺ', 'ĹŽ',
	   'Ă', 'Ĺ°', 'Ă', 'Ă', 'Ĺ˘', 'Ă', 'Ĺ', 'ĂĄ', 'Ă˘', 'Ä',
	   'Ă¤', 'Äş', 'Ä', 'Ă§', 'Ä', 'ĂŠ', 'Ä', 'ĂŤ', 'Ä', 'Ă­',
	   'ĂŽ', 'Ä', 'Ä', 'Ĺ', 'Ĺ', 'Ăł', 'Ă´', 'Ĺ', 'Ăś', 'Ăˇ',
	   'Ĺ', 'ĹŻ', 'Ăş', 'Ĺą', 'Ăź', 'Ă˝', 'ĹŁ', 'Ë');
		
		return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $f_text);
	}
}

if (!function_exists(sysmsg)) {		
	function sysmsg($msg,$grupa="")
	{
	  global $db;
	  global $lang;
	
		$defaultlang="ms";
	
		$msg=trim($msg);
	
		$m=addslashes($msg);
	
	  	$query="SELECT msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$defaultlang'";
	  	parse_str(query2url($query));
	  	if (!strlen($msg_msg)) 
		{
			$query="INSERT INTO messages (msg_label,msg_lang,msg_msg,msg_group) 
					VALUES ('$m','$defaultlang','$m','$grupa')";
			pg_Exec($db,$query);
		}	
	
	
	  	$query="SELECT msg_msg FROM messages 
				WHERE msg_label='$m' AND msg_lang='$lang'";
	  	parse_str(query2url($query));
		
		if (!strlen($msg_msg)) return $msg;
	  	return stripslashes($msg_msg);
	}
}

if (!function_exists(query2url)) {	
	function query2url($query)
	{
		global $db;
	
		$result=pg_Exec($db,$query);
		if ( pg_numRows($result)!=1 ) return "";
	
		$data=pg_fetch_row($result,0);
		$wynik="";
		for ($i=0;$i<count($data);$i++)
		{	
			if ($i) $wynik.="&";
			$wynik.=pg_fieldname($result,$i)."=".urlencode(trim($data[$i]));
		}
		return $wynik;
	}
}

if (!function_exists(pg_ExplodeName)) {	
	function pg_ExplodeName ($result,$row)
	{
		$text="";
		$cols=pg_NumFields($result);
		$data=pg_fetch_row($result,$row);
		for ($i=0;$i<$cols;$i++)
		{
			$name=pg_FieldName($result,$i);
			$value=urlencode(trim($data[$i]));
			$text.="$name=$value";
			if ($i!=$cols-1)
			$text.="&";
		}
		return $text;
	}
}

if (!function_exists(pg_ObjectArray)) {	
	function pg_ObjectArray($db,$query)
	{
		$wynik="";
		$result=pg_Exec($db,$query);
		
		$cols=pg_NumFields($result);
		for ($j=0;$j<$cols;$j++) $pola[]=pg_FieldName($result,$j);
		for ($i=0;$i<pg_NumRows($result);$i++)
		{
			$obj=pg_Fetch_Object($result,$i);
			for ($j=0;$j<$cols;$j++) $obj->$pola[$j]=trim($obj->$pola[$j]);
			$wynik[]=$obj;
		}
		return($wynik);
	}
}	

if (!function_exists(flash_xmlfile)) {	
	function flash_xmlfile($p,$kammode=1) 
	{
		if (!$p) return "";
		global $KAMELEON_MODE,$KAMELEON_UIMAGES,$UIMAGES,$lang;

		if ($KAMELEON_MODE)	$ret = $UIMAGES;
		else $ret = $KAMELEON_UIMAGES;
		
		if (!$kammode) $ret = $UIMAGES;
		
		$ret.= "/sb/xml";
		if (!$kammode) $ret.= "/ftp";
		$ret.= "/".$p."_".$lang.".xml";
		return $ret;
	}
}

if (!function_exists(flash_xmlfile_export)) {	
	function flash_xmlfile_export($p) 
	{
		if (!$p) return "";
		global $KAMELEON_UIMAGES,$lang;
		$ret = $KAMELEON_UIMAGES;
		
		$ret.= "/sb/xml";
		$ret.= "/ftp";
		$ret.= "/".$p."_".$lang.".xml";
		return $ret;
	}
}
if (!function_exists("navi")) {	
 function navi($href,$list,$size)
 {
	$C_NAVI_PAGES = 15;
	$ile=$list[ile]+0;

	if ($ile<=$size || !$size) return "";
	$next=$list[start]+$size;
	if ($next>=$ile) $next="";


	if ($list[start])
	{
		$prev=$list[start]-$size;


		if ($prev<0) $prev=0;

	}	

	$href.=strstr($href,"?")?"&":"?";
	$href.="list[ile]=".$list[ile];
	$href.="&list[sort_f]=".$list[sort_f];

	$href.="&list[sort_d]=".($list[sort_d]+0);

	$current=round($list[start]/$size);
	$first=$current-floor(5/2)+1;

	$last=$first+5;

	while ($first<0)
	{
		$first++;
		$last++;
	}

	while ($last*$size>=$ile)
	{
		if ($first>0) $first--;
		$last--;
	}

	for ($i=$first;$i<=$last;$i++)
	{
		$page=$i+1;
		$start=$i*$size;
		if ($i!=$current) $wynik.=" <a href=\"$href&list[start]=$start\">[";
		else $wynik.="<span class=\"current\">";


		$wynik.=$page;

		if ($i!=$current) $wynik.="]</a> ";
		else $wynik.="</span>";
	}


	if (strlen($prev)) $prev="<a href=\"$href&list[start]=$prev\">&laquo;&laquo;</a> ";
	if (strlen($next)) $next=" <a href=\"$href&list[start]=$next\">&raquo;&raquo;</a>";

	$razem= "Znaleziono";
	$strony= "stron";
	$wynik="<span class=\"list_navi\">$razem: $ile, $strony: $prev$wynik$next</span>";


	return $wynik;
 }
 }
?>