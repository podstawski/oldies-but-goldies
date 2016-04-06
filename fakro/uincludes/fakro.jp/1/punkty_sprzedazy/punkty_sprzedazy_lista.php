<?
include($INCLUDE_PATH.'/punkty_sprzedazy/punkty_sprzedazy_config.php');

$lista = $DB->fetch_assoc($DB->query("SELECT * FROM usa_dystrybutor,usa_stan WHERE usa_dystrybutor.id_stan = usa_stan.id_stan ORDER BY usa_stan.id_stan"));
$lista_count = $DB->getvalues($DB->query("SELECT count(id_dystrybutor) AS total FROM usa_dystrybutor,usa_stan WHERE usa_dystrybutor.id_stan = usa_stan.id_stan ORDER BY usa_stan.id_stan"));
$LIST['ile'] = $lista_count['total'];

if(!$lista_count['total']) {
	echo "<br><br><div align=\"center\"><strong>No matching records found or the list is empty.</strong></div>";
	return;
	}
?>

<br>

<table class="tl" cellspacing="0" cellpadding="3" width="100%">
<tbody>
<?
$i = 0;
foreach($lista as $key => $value) {
	echo "<tr class=\"".(($i && ($i%2))?'even':'odd')."\">";
	echo '<td class="name" valign="top">'.(($lista[$key]['www'])?"<a href=\"http://".$lista[$key]['www']."\" target=\"_blank\"><img src=\"".$INCLUDE_PATH."/punkty_sprzedazy/i_www.gif\" align=\"right\" width=\"19\" height=\"28\" border=\"0\"></a>":"").' '.stripslashes($lista[$key]['firma']).'</td>';
	echo '<td valign="top">'.$lista[$key]['ulica'].'<br>'.$lista[$key]['kod'].' '.$lista[$key]['miejscowosc'].',<br>'.$lista[$key]['name'].'<br></td>';
	echo '<td valign="top" style="font-weight:bold">'.$lista[$key]['telefon'].'</td>';
	echo '</tr>';
	$i++;
	}
?>
</tbody>
</table>