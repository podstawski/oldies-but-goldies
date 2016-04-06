<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

function win2iso($f_text)
{
	$f_text = strtr($f_text, 'œ', '¶');
	$f_text = strtr($f_text, '¹', '±');
	$f_text = strtr($f_text, 'Ÿ', '¼');
	$f_text = strtr($f_text, 'Œ', '¦');
	$f_text = strtr($f_text, '¥', '¡');
	$f_text = strtr($f_text, '', '¬');
	
	return $f_text;
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