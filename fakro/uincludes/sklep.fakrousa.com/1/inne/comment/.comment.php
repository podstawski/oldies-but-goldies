<?
global $db,$lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID,$SERVER_NAME,$mode,$id_comment;

$projdb->debug=0;
$KEY = $SERVER_NAME;
$KsiegaGrupa=$page;

echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>System komentarzy (FAKRO) - sid: ".$WEBTD->sid."</legend>
<br>
<br><br>
</fieldset>";

if($mode == "usun") {
	$query = "DELETE FROM ksiega2 WHERE id=$id_comment ";
	$projdb->execute($query);
	unset($mode);
	}

$query = "SELECT * FROM ksiega2 ORDER BY grupa, id DESC LIMIT 25";
$result = $projdb->execute($query);

for($i=0; $i < $result->RecordCount(); $i++) {
	parse_str(ado_explodename($result,$i));
	
	$delete="<a href=".$self."&mode=usun&id_comment=".$id."> <img src=$API_URL/img/ikona-smietnik-b.gif alt='".sysmsg("Delete")."' width=12 height=12 border=0></a>";
	$wpis=date("Y-m-d H:i:s",$wpis);
	$osoba=stripslashes($osoba);
	$opis=stripslashes($opis);
	$ranking=stripslashes($ranking);
	echo "$delete <img src=".$INCLUDE_PATH."/inne/comment/images/comment_star".$ranking.".gif width=100 height=16 border=0> [$grupa] <b>$email, $osoba </b><br><i>".sysmsg("Date").": $wpis</i><br><br>";
	echo nl2br($opis);
	echo "<hr noshade size=1>";
	}
?>