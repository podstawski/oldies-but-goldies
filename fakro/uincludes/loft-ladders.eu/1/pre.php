<?
	if (!$WEBTD->sid)
	{
		foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
		foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	}

	$CONST_SENDMAIL_PATH="/var/www/html/fakro/kameleon01/sendmail_site_fakro_co_uk";

?>
