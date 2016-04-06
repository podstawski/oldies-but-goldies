<?
function b_convert_charset($lang,$charset,$orygin)
{
	//if (strtolower($charset)=="utf-8" && $orygin=="label" ) return true;
	return false;
}

function s_convert_charset($text,$lang,$charset,$orygin)
{

 if (strtolower($charset)=="utf-8" && $orygin=="label" && ($lang=="i" || $lang=="p") )
	return kameleon_iso88592_2utf8($text);

 if (strtolower($charset)=="utf-8" && $orygin=="label" )
 {
	$text=ereg_replace("�","a",$text);
	$text=ereg_replace("�","e",$text);
	$text=ereg_replace("�","n",$text);
	$text=ereg_replace("�","l",$text);
	$text=ereg_replace("�","o",$text);
	$text=ereg_replace("�","z",$text);
	$text=ereg_replace("�","z",$text);
	$text=ereg_replace("�","c",$text);
	$text=ereg_replace("�","s",$text);
	$text=ereg_replace("�","a",$text);
	$text=ereg_replace("�","s",$text);
	$text=ereg_replace("�","z",$text);

	$text=ereg_replace("�","a",$text);
	$text=ereg_replace("�","e",$text);
	$text=ereg_replace("�","n",$text);
	$text=ereg_replace("�","l",$text);
	$text=ereg_replace("�","o",$text);
	$text=ereg_replace("�","z",$text);
	$text=ereg_replace("�","z",$text);
	$text=ereg_replace("�","c",$text);
	$text=ereg_replace("�","s",$text);

	$text=ereg_replace("�","a",$text);
	$text=ereg_replace("�","s",$text);
	$text=ereg_replace("�","z",$text);

	$text=ereg_replace("�","au",$text);
	$text=ereg_replace("�","eu",$text);
	$text=ereg_replace("�","ou",$text);
	
	$text=ereg_replace("�","AU",$text);
	$text=ereg_replace("�","EU",$text);
	$text=ereg_replace("�","OU",$text);

	$text=ereg_replace("�","ss",$text);




	return utf8_encode($text);
 }
}

function kameleon_win2iso($f_text)
{
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	
	return $f_text;
}

function kameleon_iso88592_2utf8($input) 
{
	$input=kameleon_win2iso($input);

	  $iso88592 = array(
	   '', '', '', '', '', '', '', '', '', '',
	   '', '', '', '', '', '', '', '', '', '',
	   '', '', '', '', '', '', '', '', '', '',
	   '', '', '� ', 'Ą', '˘', 'Ł', '¤', 'Ľ', 'Ś', '§',
	   '¨', '� ', 'Ş', 'Ť', 'Ź', '­', 'Ž', 'Ż', '°', 'ą',
	   '˛', 'ł', '´', 'ľ', 'ś', 'ˇ', '¸', 'š', 'ş', 'ť',
	   'ź', '˝', 'ž', 'ż', 'Ŕ', 'Á', 'Â', 'Ă', 'Ä', 'Ĺ',
	   'Ć', 'Ç', 'Č', 'É', 'Ę', 'Ë', 'Ě', 'Í', 'Î', 'Ď',
	   'Đ', 'Ń', 'Ň', 'Ó', 'Ô', 'Ő', 'Ö', '×', 'Ř', 'Ů',
	   'Ú', 'Ű', 'Ü', 'Ý', 'Ţ', 'ß', 'ŕ', 'á', 'â', 'ă',
	   'ä', 'ĺ', 'ć', 'ç', 'č', 'é', 'ę', 'ë', 'ě', 'í',
	   'î', 'ď', 'đ', 'ń', 'ň', 'ó', 'ô', 'ő', 'ö', '÷',
	   'ř', 'ů', 'ú', 'ű', 'ü', 'ý', 'ţ', '˙');

	  return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $input);
}


?>