<?
	/*
	wersja: 1.1
	autor: DR
	modyfikacja: 13.02.2006

	UWAGI
	w const.h szablonu nale¿y jeszcze w³¹czyæ zmienn¹:
	$C_SHOW_PAGE_KEY=1;

	format pola key [id=gemius_id]
	*/

	if ($KAMELEON_MODE) return;
	global $WEBPAGE;
	$key=$WEBPAGE->pagekey;
	if (!strlen($key)) return;
?>
<!-- (C) 2004 stat.pl - ver 1.0 / Strona xxx -->
<SCRIPT type="text/javascript">
<!--
document.writeln('<'+'scr'+'ipt type="text/javascript" src="http://s2.hit.stat.pl/_'+(new Date()).getTime()+'/script.js?<?echo $key?>"></'+'scr'+'ipt>');
//-->
</SCRIPT>