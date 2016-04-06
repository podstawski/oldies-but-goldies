<?
global $pogoda;
if (strlen($pogoda))
{
	$type="bigwx_metric_cond/language/polish";
	$ret_mod = "<center><div id=\"pog".$pogoda."\">
				<a href=\"http://www.wunderground.com/global/stations/".$pogoda.".html\">
				<img border=\"0\" alt=\"Prognoza pogody\" src=\"http://banners.wunderground.com/banner/".$type."/global/stations/".$pogoda.".gif\">
				</a></div></center><br><br>";
	echo $ret_mod;
}
?>
