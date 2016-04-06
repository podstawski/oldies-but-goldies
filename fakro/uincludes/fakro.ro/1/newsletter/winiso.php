<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

if (!function_exists("win2iso"))
{
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
}

if (!function_exists("iso2win"))
{
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
}

?>