<?
$LOCK_VERSION=0.9;




$C_LOGFILE="log/webadmin.log";
if (!strlen($KAMELEON_VERSION)) $KAMELEON_VERSION="5.07";


$CONST_TRASH=-1;
$KAMELEON_HELP="http://www.webkameleon.com/help/help.pdf"; //adres serwisu z helpem je�eli pusta to ikona help sie nie wyswietla

$KAMELEON_EXT="php";

$SWF_OBJECT_PARAMS=array(
						'allowScriptAccess'=>'sameDomain|yes',
						'play'=>'true|false',
						'quality'=>'high|low',
						'scale'=>'noscale|yescale',
						'salign'=>'lt|lb|rt|rb',
						'wmode'=>'transparent|nontransparent',
						'devicefont'=>'true|false');

if (!$CONST_MAX_TIME_REQUIRED_FOR_HELP) $CONST_MAX_TIME_REQUIRED_FOR_HELP=3600;
if (!$CONST_HELP_WIDTH) $CONST_HELP_WIDTH=180;


$DEFAULT_PATH_IMAGES="images/szablon\$ver";
$DEFAULT_PATH_UIMAGES="images";
$DEFAULT_PATH_UFILES="att";
$DEFAULT_PATH_PAGES="p\$lang\$ver";
$DEFAULT_PATH_INCLUDE="include";

$DEFAULT_TEXTFILE_CSS="textstyle.css";

$DEFAULT_PATH_KAMELEON_UIMAGES="uimages/\$SERVER_ID/\$ver";
$DEFAULT_PATH_KAMELEON_UINCLUDES="uincludes/\$SERVER_NAME/\$ver";
$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN="uincludes/\$SERVER_NAME/\".\$kameleon->user[username].\"";
$DEFAULT_PATH_KAMELEON_UFILES="ufiles/\$SERVER_ID-att";
$EREG_REPLACE_KAMELEON_UIMAGES="uimages/$SERVER_ID/[0-9]+";

$DEFAULT_ACL_WEBPAGE_INCLUDE=".auth.h";
$DEFAULT_ACL_WEBPAGE_DB_FILE=".webpage_\$lang\$ver.db";
$DEFAULT_ACL_WEBPAGE_KEY="\$lang:\$ver:\$page";
$DEFAULT_ACL_NAME="Access list";


$CONST_LANGS=array("pl","en","de","fr","es",'ru');

$CHARSET_TAB=array(	"p"=>"Windows-1250",
			"i"=>"ISO-8859-2",
			"r"=>"ISO-8859-5",
			"e"=>"ISO-8859-1",
			"f"=>"ISO-8859-1",
			"d"=>"ISO-8859-1",
			"s"=>"ISO-8859-1",
			"l"=>"ISO-8859-13",
			"y"=>"ISO-8859-2",
			"h"=>"ISO-8859-2",
			"t"=>"ISO-8859-2",
			"bu"=>"ISO-8859-5",
			"g"=>"ISO-8859-7",
			"t"=>"ISO-8859-9",
			"fr"=>"ISO-8859-1",
			"pl"=>"ISO-8859-2",
			"nl"=>"ISO-8859-1",
			"en"=>"ISO-8859-1",
			"ru"=>"ISO-8859-5",
			"de"=>"ISO-8859-1",
			"no"=>"ISO-8859-1",
			""=>"ISO-8859-1");

$CHARSET_TAB=array(	"p"=>"Windows-1250",// polski
			"i"=>"ISO-8859-2",// polski
			"r"=>"ISO-8859-5",// rosyjski
			"e"=>"ISO-8859-1",// angielski
			"f"=>"ISO-8859-1",// francuski
			"d"=>"ISO-8859-1",// niemiecki
			"s"=>"ISO-8859-1",// hiszpa�ski
			"l"=>"ISO-8859-13",// litewski
			"y"=>"ISO-8859-2",
			"h"=>"ISO-8859-2",// w�gierski
			"cz2"=>"ISO-8859-2",// czeski
			"bu"=>"utf-8",// bu�garski
			"g"=>"ISO-8859-7",// grecki
			"t"=>"ISO-8859-9",// turecki			
			""=>"utf-8");// inne

$CHARSET=$CHARSET_TAB[$lang];
if (strlen($lang)==2 || !strlen($CHARSET)) $CHARSET='utf-8';

$MULTI_HF_STEP=100;



if ($KAMELEON_MODE && !is_array($APIS))
{
	$APIS[]=array("",label("Choose"));
	//$APIS[]=array("news",label("News"));
	$APIS[]=array("search",label("Search engine"));
	//$APIS[]=array("ogloszenia",label("Hyde Park"));
	//$APIS[]=array("ksiega",label("Guest book"));
	//$APIS[]=array("forum",label("Forum"));
	$APIS[]=array("kontakt",label("Contact formular"));
	//$APIS[]=array("polecam",label("Inform friends"));
	//$APIS[]=array("counter",label("Counter"));
	$APIS[]=array("kameleon:sitemap",label("Site map"));
	$APIS[]=array("kameleon:formvalidator",label("Form validator"));
	$APIS[]=array("sendform",label("Post form mail sender"));
	$APIS[]=array("kameleon:navigator",label("Page navigator"));
}


define('CONST_WINDOWS', stristr(PHP_OS,'win')&&!stristr(PHP_OS,'darwin'));
define('CONST_PATH_SEP', (CONST_WINDOWS)?';':':');

if (CONST_WINDOWS) 
{
	$_7z=dirname(dirname(__FILE__)).'\win\7z.exe';
	define('CONST_UNZIP_UNTAR_EXE', '"'.$_7z.'" -y -so x {file} >pliku.tar ; "'.$_7z.'" -y x pliku.tar');
	define('CONST_UNZIP_EXE', '"'.$_7z.'" -y x {file}');
	define('CONST_ZIP_EXE', '"'.$_7z.'" a -tzip {file}');
	define('CONST_UNTAR_EXE', '"'.$_7z.'" -y x {file}');
	define('CONST_TAR_EXE', '"'.$_7z.'" a -ttar -r {file}');
} 
else 
{
	$CONST_UNZIP_EXE=str_replace('-o','',$CONST_UNZIP_EXE);
	define('CONST_UNZIP_UNTAR_EXE', "$CONST_UNZIP_EXE -o -p {file} | tar -xf - ");
	if (strlen($CONST_UNZIP_EXE)) define('CONST_UNZIP_EXE', "$CONST_UNZIP_EXE -o {file}");
	else define('CONST_UNZIP_EXE','');
	define('CONST_UNTAR_EXE', 'tar -xf {file}');
	define('CONST_TAR_EXE', 'tar -cf {file}');
	if (strlen($CONST_ZIP_EXE)) define('CONST_ZIP_EXE', "$CONST_ZIP_EXE {file}");
	else define('CONST_ZIP_EXE','');
}	


$CONST_SKINS_DIR='skins';

$CONST_EXPORT_SERVER_TOKEN="kameleonToken_87343248";
$CONST_EXPORT_VER_TOKEN="kameleonToken_4234235312";
$CONST_EXPORT_NL_TOKEN="kameleonToken_23482734";
$CONST_EXPORT_LANG_TOKEN="kameleonToken_62872849jd3";
$CONST_EXPORT_PAGE_TOKEN="kameleonToken_26349j2g63";
$CONST_EXPORT_USER_TOKEN="kameleonToasken_632hs542";
$CONST_EXPORT_SQL="kameleon.sql";
$CONST_EXPORT_TRANSLATION_PHP="kameleon-trans.php";

