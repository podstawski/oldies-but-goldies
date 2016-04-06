<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

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


function pl2rtf($str)
{
	
	$str=iso2win($str);

	$str=ereg_replace("[\r]*\n","\\line ",$str);

	
	$str=ereg_replace("Ѕ","\\'a5",$str);
	$str=ereg_replace("Ъ","\\'ca",$str);
	$str=ereg_replace("","\\'8c",$str);
	$str=ereg_replace("Ц","\\'c6",$str);
	$str=ereg_replace("б","\\'d1",$str);
	$str=ereg_replace("Џ","\\'af",$str);
	$str=ereg_replace("","\\'8f",$str);
	$str=ereg_replace("г","\\'d3",$str);
	$str=ereg_replace("Ѓ","\\'a3",$str);

	$str=ereg_replace("Й","\\'b9",$str);
	$str=ereg_replace("ъ","\\'ea",$str);
	$str=ereg_replace("","\\'9c",$str);
	$str=ereg_replace("ц","\\'e6",$str);
	$str=ereg_replace("ё","\\'f1",$str);
	$str=ereg_replace("П","\\'bf",$str);
	$str=ereg_replace("","\\'9f",$str);
	$str=ereg_replace("ѓ","\\'f3",$str);
	$str=ereg_replace("Г","\\'b3",$str);



	return ($str);
}

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


?>
