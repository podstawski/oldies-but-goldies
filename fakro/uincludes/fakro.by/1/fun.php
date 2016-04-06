<?
if (!function_exists('win2iso')) {
	function win2iso($f_text)
	{
		$f_text = strtr($f_text, '', 'Ж');
		$f_text = strtr($f_text, 'Й', 'Б');
		$f_text = strtr($f_text, '', 'М');
		$f_text = strtr($f_text, '', 'І');
		$f_text = strtr($f_text, 'Ѕ', 'Ё');
		$f_text = strtr($f_text, '', 'Ќ');
		
		return $f_text;
	}
}

if (!function_exists('iso2win')) {
	function iso2win($f_text)
	{
		$f_text = strtr($f_text, 'Ж', '');
		$f_text = strtr($f_text, 'Б', 'Й');
		$f_text = strtr($f_text, 'М', '');
		$f_text = strtr($f_text, 'І', '');
		$f_text = strtr($f_text, 'Ё', 'Ѕ');
		$f_text = strtr($f_text, 'Ќ', '');
	
		return $f_text;
	}
}	

if (!function_exists('b_unpolish')) {	
	function b_unpolish($text)
	{
	 $text=ereg_replace("Й","a",$text);
	 $text=ereg_replace("ъ","e",$text);
	 $text=ereg_replace("ё","n",$text);
	 $text=ereg_replace("Г","l",$text);
	 $text=ereg_replace("ѓ","o",$text);
	 $text=ereg_replace("П","z",$text);
	 $text=ereg_replace("","z",$text);
	 $text=ereg_replace("ц","c",$text);
	 $text=ereg_replace("","s",$text);
	 $text=ereg_replace("Б","a",$text);
	 $text=ereg_replace("Ж","s",$text);
	 $text=ereg_replace("М","z",$text);
	
	 $text=ereg_replace("Ѕ","A",$text);
	 $text=ereg_replace("Ъ","E",$text);
	 $text=ereg_replace("б","N",$text);
	 $text=ereg_replace("Ѓ","L",$text);
	 $text=ereg_replace("г","O",$text);
	 $text=ereg_replace("Џ","Z",$text);
	 $text=ereg_replace("","Z",$text);
	 $text=ereg_replace("Ц","C",$text);
	 $text=ereg_replace("","S",$text);
	 $text=ereg_replace("Ё","A",$text);
	 $text=ereg_replace("І","S",$text);
	 $text=ereg_replace("Ќ","Z",$text);
	
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
	
if (!function_exists('utf82iso')) {		
	function utf82iso($f_text)
	{

		$f_text = str_replace("Х","Г",$f_text);
		$f_text = str_replace("Г","Г",$f_text);
		$f_text = str_replace("Х","Ѓ",$f_text);
		$f_text = str_replace("Х","ё",$f_text);
		$f_text = str_replace("Х","б",$f_text);
		$f_text = str_replace("Ф","ъ",$f_text);
		$f_text = str_replace("Ф","Ъ",$f_text);
		$f_text = str_replace("УГ","ѓ",$f_text);
		$f_text = str_replace("ѓ","ѓ",$f_text);
		$f_text = str_replace("У","г",$f_text);
		$f_text = str_replace("ХМ","П",$f_text);
		$f_text = str_replace("ГЛ","П",$f_text);
		$f_text = str_replace("ХЛ","Џ",$f_text);
		$f_text = str_replace("Х","",$f_text);
		$f_text = str_replace("О","Б",$f_text);//Й		
		$f_text = str_replace("Ф","Б",$f_text);//Й
		$f_text = str_replace("Ф","Ё",$f_text);//Ѕ
		$f_text = str_replace("Ф","М",$f_text);//Ѕ
		$f_text = str_replace("Г","Ж",$f_text);//
		$f_text = str_replace("Х","І",$f_text);//		
		$f_text = str_replace("ъ","ц",$f_text);
		$f_text = str_replace("Ф","Ц",$f_text);

		return $f_text;
	}
}

if (!function_exists("iso2utf")) {
	function iso2utf($f_text)
	{
		$iso88592 = array(
	   'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т',
	   'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т',
	   'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т',
	   'Т', 'Т', 'Т ', 'Ф', 'Ы', 'Х', 'ТЄ', 'ФН', 'Х', 'ТЇ',
	   'ТЈ', 'Х ', 'Х', 'ХЄ', 'ХЙ', 'Т­', 'ХН', 'ХЛ', 'ТА', 'Ф',
	   'Ы', 'Х', 'ТД', 'ФО', 'Х', 'Ы', 'ТИ', 'ХЁ', 'Х', 'ХЅ',
	   'ХК', 'Ы', 'ХО', 'ХМ', 'Х', 'У', 'У', 'Ф', 'У', 'ФЙ',
	   'Ф', 'У', 'Ф', 'У', 'Ф', 'У', 'Ф', 'У', 'У', 'Ф',
	   'Ф', 'Х', 'Х', 'У', 'У', 'Х', 'У', 'У', 'Х', 'ХЎ',
	   'У', 'ХА', 'У', 'У', 'ХЂ', 'У', 'Х', 'УЁ', 'УЂ', 'Ф',
	   'УЄ', 'ФК', 'Ф', 'УЇ', 'Ф', 'УЉ', 'Ф', 'УЋ', 'Ф', 'У­',
	   'УЎ', 'Ф', 'Ф', 'Х', 'Х', 'УГ', 'УД', 'Х', 'УЖ', 'УЗ',
	   'Х', 'ХЏ', 'УК', 'ХБ', 'УМ', 'УН', 'ХЃ', 'Ы');
		
		return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $f_text);
	}
}

if (!function_exists('sysmsg')) {		
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

if (!function_exists('query2url')) {	
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

if (!function_exists('pg_ExplodeName')) {	
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

if (!function_exists('pg_ObjectArray')) {	
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

if (!function_exists('flash_xmlfile')) {	
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

if (!function_exists('flash_xmlfile_export')) {	
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