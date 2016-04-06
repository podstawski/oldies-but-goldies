<?
	include_once('include/class/ConvertCharset.class.php');



	function utf82lang($text,$l='')
	{
		if (!strlen($text)) return;

		global $lang,$CHARSET_TAB;
		static $cc;

		if (!strlen($l)) $l=$lang;
		
		if (strlen($l)==2) return $text;

		if (!is_object($cc)) $cc=new ConvertCharset();
		return $cc->Convert($text, 'utf-8', strtolower($CHARSET_TAB[$l]));
	}


	function lang2utf8($text,$l='')
	{
		if (!strlen($text)) return;

		global $lang,$CHARSET_TAB;
		static $cc;

		if (!strlen($l)) $l=$lang;
		if (strlen($l)==2) return $text;

		if (!is_object($cc)) $cc=new ConvertCharset();
		return $cc->Convert($text, strtolower($CHARSET_TAB[$l]), 'utf-8');
	}

?>