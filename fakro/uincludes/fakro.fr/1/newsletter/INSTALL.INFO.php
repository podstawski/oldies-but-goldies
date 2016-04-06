<?
if (strlen($WEBTD->xml)) echo '<br><br>';
echo $WEBTD->xml;


if (!$WEBTD->cos) return;
echo '<br><br>';

if (!file_exists("$SZABLON_PATH/themes/$INSTALL_NAME/body.html"))
	echo "Brak pliku <b>$SZABLON_PATH/themes/$INSTALL_NAME/body.html</b> <br><br>";

if (!file_exists("$SZABLON_PATH/newsletter_tokens.php"))
	echo "Brak pliku <b>$SZABLON_PATH/newsletter_tokens.php	</b> <br><br>";

if ($WEBTD->cos>0) $submit="Zakoñcz instalacjê";

?>