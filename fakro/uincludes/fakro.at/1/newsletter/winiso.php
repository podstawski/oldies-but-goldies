<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

if (!function_exists("win2iso"))
{
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
}

if (!function_exists("iso2win"))
{
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
}

?>