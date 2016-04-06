<?
$config = array(
	'_MYSQL_HOSTNAME' => 'crm.fakro.pl',
	'_MYSQL_HOSTNAME' => '213.25.72.188',
	'_MYSQL_USERID' => 'fakro_www',
	'_MYSQL_PASSWORD' => 'www_fakro',
	'_MYSQL_DATABASE_PROD' => 'fakro_crm_prod',
	'_MYSQL_DATABASE_EXTERNAL_SUFFIX' => '_obsluga_www',
	'_FILES_URL' => './_pliki/',
	'_FILES_URL' => $UIMAGES.'/crm/',
	'_ID_OSOBY' => '29',
	'_ID_SERWIS' => '990',
	'_ID_WALUTY' => '1',
	'_LANG' => '5',
);

foreach ($config as $key => $value)
{
	define($key, $value);
}

define('_MYSQL_SET_NAMES_LATIN2', true);
define('_MYSQL_DATABASE', _MYSQL_DATABASE_PROD._MYSQL_DATABASE_EXTERNAL_SUFFIX);
?>
