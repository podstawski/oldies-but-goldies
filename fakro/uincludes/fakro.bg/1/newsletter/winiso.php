<?
if ($_WINISO_INCLUDED==1) return;
else $_WINISO_INCLUDED=1;

if (!function_exists("win2iso"))
{
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
}

if (!function_exists("iso2win"))
{
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
}

?>