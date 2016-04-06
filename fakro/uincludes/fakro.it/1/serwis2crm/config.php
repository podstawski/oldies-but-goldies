<?

$config = array(
	'_MYSQL_HOSTNAME' => 'localhost',
	'_MYSQL_USERID' => 'fakroexternal',
	'_MYSQL_PASSWORD' => 'fakroexternal',
	'_MYSQL_DATABASE_PROD' => 'fakro_crm_prodpre',
	'_MYSQL_DATABASE_EXTERNAL_SUFFIX' => '_obsluga_www',
	'_FILES_URL' => '../www_write/fakro/beta/system_uploads_up/',
	'_ID_OSOBY' => '29',
	'_ID_SERWIS' => '990',
	'_ID_WALUTY' => '1',
	'_LANG' => '5',
);

foreach ($config as $key => $value)
{
	define($key, $value);
}

define('_MYSQL_DATABASE', _MYSQL_DATABASE_PROD . _MYSQL_DATABASE_EXTERNAL_SUFFIX);

?>
