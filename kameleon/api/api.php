<?
	include_once("../include/request.h");


	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');

	if (strlen($WKSESSID)) $_COOKIE["WKSESSID"]=$WKSESSID;

    define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");
		
	include_once ("include/apifun.h");
	include ("include/validateform.h");

	include_once ("../include/const.h");


	// tak musi byc
	$API_URL=$API_SERVER;

	//$dbapi=$db;
	parse_str(rozkoduj_url($QS));

	$lang=$api_lang;
	$ver=$api_ver;

	if (strstr(strtolower($CHARSET),'utf') ) $adodb->adodb->SetCharSet('UTF-8');

	$kameleon->init(strlen($KAMELEON_LANG)?$KAMELEON_LANG:$lang,$ver,$SERVER_ID,$CHARSET,$page);

	include ("include/update.h");
	if (file_exists("include/$SERVICE".".h")) include ("include/$SERVICE".".h");

	$adodb->dontSaveSession=true;
	$adodb->Close('',$persistant_connection);
