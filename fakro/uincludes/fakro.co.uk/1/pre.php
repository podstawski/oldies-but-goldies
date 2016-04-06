<?
	if (!isset($WEBTD->sid) OR !$WEBTD->sid)
	{
		foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
		foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	}

	$CONST_SENDMAIL_PATH="/var/www/html/site/fakro.co.uk/tools/sendmail_site_fakro_co_uk";

?>
