<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

function win2iso($f_text)
{
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	
	return $f_text;
}


function iso2win($f_text)
{
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');
	$f_text = strtr($f_text, '�', '�');

	return $f_text;
}

?>