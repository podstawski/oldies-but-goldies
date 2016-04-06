<?
if ($_winiso_==1 ) return;
$_winiso_=1;

function win2iso($str)
{
		//return $str;

	$str=ereg_replace("¥","¡",$str);
	$str=ereg_replace("Œ","¦",$str);
	$str=ereg_replace("","¬",$str);
	$str=ereg_replace("¹","±",$str);
	$str=ereg_replace("œ","¶",$str);
	$str=ereg_replace("Ÿ","¼",$str);

	return ($str);
}

function iso2win($f_text)
{
	$f_text = strtr($f_text, '¶', 'œ');
	$f_text = strtr($f_text, '±', '¹');
	$f_text = strtr($f_text, '¼', 'Ÿ');
	$f_text = strtr($f_text, '¦', 'Œ');
	$f_text = strtr($f_text, '¡', '¥');
	$f_text = strtr($f_text, '¬', '');

	return $f_text;
}
?>