<?
if ($_winiso_==1 ) return;
$_winiso_=1;

function win2iso($str)
{
		//return $str;

	$str=ereg_replace("�","�",$str);
	$str=ereg_replace("�","�",$str);
	$str=ereg_replace("�","�",$str);
	$str=ereg_replace("�","�",$str);
	$str=ereg_replace("�","�",$str);
	$str=ereg_replace("�","�",$str);

	return ($str);
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