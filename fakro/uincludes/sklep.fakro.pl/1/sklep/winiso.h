<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

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


function pl2rtf($str)
{
	
	$str=iso2win($str);

	$str=ereg_replace("[\r]*\n","\\line ",$str);

	
	$str=ereg_replace("Ľ","\\'a5",$str);
	$str=ereg_replace("Ę","\\'ca",$str);
	$str=ereg_replace("","\\'8c",$str);
	$str=ereg_replace("Ć","\\'c6",$str);
	$str=ereg_replace("Ń","\\'d1",$str);
	$str=ereg_replace("Ż","\\'af",$str);
	$str=ereg_replace("","\\'8f",$str);
	$str=ereg_replace("Ó","\\'d3",$str);
	$str=ereg_replace("Ł","\\'a3",$str);

	$str=ereg_replace("š","\\'b9",$str);
	$str=ereg_replace("ę","\\'ea",$str);
	$str=ereg_replace("","\\'9c",$str);
	$str=ereg_replace("ć","\\'e6",$str);
	$str=ereg_replace("ń","\\'f1",$str);
	$str=ereg_replace("ż","\\'bf",$str);
	$str=ereg_replace("","\\'9f",$str);
	$str=ereg_replace("ó","\\'f3",$str);
	$str=ereg_replace("ł","\\'b3",$str);



	return ($str);
}

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


?>
