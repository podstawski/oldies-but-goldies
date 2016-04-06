<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

function win2iso($f_text)
{
	$f_text = strtr($f_text, '', '¶');
	$f_text = strtr($f_text, '¹', '±');
	$f_text = strtr($f_text, '', '¼');
	$f_text = strtr($f_text, '', '¦');
	$f_text = strtr($f_text, '¥', '¡');
	$f_text = strtr($f_text, '', '¬');
	
	return $f_text;
}


function iso2win($f_text)
{
	$f_text = strtr($f_text, '¶', '');
	$f_text = strtr($f_text, '±', '¹');
	$f_text = strtr($f_text, '¼', '');
	$f_text = strtr($f_text, '¦', '');
	$f_text = strtr($f_text, '¡', '¥');
	$f_text = strtr($f_text, '¬', '');

	return $f_text;
}


function pl2rtf($str)
{
	
	$str=iso2win($str);

	$str=ereg_replace("[\r]*\n","\\line ",$str);

	
	$str=ereg_replace("¥","\\'a5",$str);
	$str=ereg_replace("Ê","\\'ca",$str);
	$str=ereg_replace("","\\'8c",$str);
	$str=ereg_replace("Æ","\\'c6",$str);
	$str=ereg_replace("Ñ","\\'d1",$str);
	$str=ereg_replace("¯","\\'af",$str);
	$str=ereg_replace("","\\'8f",$str);
	$str=ereg_replace("Ó","\\'d3",$str);
	$str=ereg_replace("£","\\'a3",$str);

	$str=ereg_replace("¹","\\'b9",$str);
	$str=ereg_replace("ê","\\'ea",$str);
	$str=ereg_replace("","\\'9c",$str);
	$str=ereg_replace("æ","\\'e6",$str);
	$str=ereg_replace("ñ","\\'f1",$str);
	$str=ereg_replace("¿","\\'bf",$str);
	$str=ereg_replace("","\\'9f",$str);
	$str=ereg_replace("ó","\\'f3",$str);
	$str=ereg_replace("³","\\'b3",$str);



	return ($str);
}

function b_unpolish($text)
{
 $text=ereg_replace("¹","a",$text);
 $text=ereg_replace("ê","e",$text);
 $text=ereg_replace("ñ","n",$text);
 $text=ereg_replace("³","l",$text);
 $text=ereg_replace("ó","o",$text);
 $text=ereg_replace("¿","z",$text);
 $text=ereg_replace("","z",$text);
 $text=ereg_replace("æ","c",$text);
 $text=ereg_replace("","s",$text);
 $text=ereg_replace("±","a",$text);
 $text=ereg_replace("¶","s",$text);
 $text=ereg_replace("¼","z",$text);

 $text=ereg_replace("¥","A",$text);
 $text=ereg_replace("Ê","E",$text);
 $text=ereg_replace("Ñ","N",$text);
 $text=ereg_replace("£","L",$text);
 $text=ereg_replace("Ó","O",$text);
 $text=ereg_replace("¯","Z",$text);
 $text=ereg_replace("","Z",$text);
 $text=ereg_replace("Æ","C",$text);
 $text=ereg_replace("","S",$text);
 $text=ereg_replace("¡","A",$text);
 $text=ereg_replace("¦","S",$text);
 $text=ereg_replace("¬","Z",$text);

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


?>
