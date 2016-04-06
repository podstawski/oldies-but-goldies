<?



if (file_exists(dirname(__FILE__).'/../const.php')) include_once(dirname(__FILE__).'/../const.php'); else include_once(dirname(__FILE__).'/../const.h');

$CONST_LICENSE_NAME="";
$CONST_LICENSE_VALID="";
$CONST_LICENSE_SERVERS=0;
$CONST_LICENSE_SECRET="klj7G#h^*de@#^*jhjf";
if (strlen($CONST_LICENSE_KEY))
{
	$_md5=$md5=substr($CONST_LICENSE_KEY,0,strpos($CONST_LICENSE_KEY,"g"));
	$_rest=substr($CONST_LICENSE_KEY,strpos($CONST_LICENSE_KEY,"g")+1);
	$_rmd5=md5($_rest.$CONST_LICENSE_SECRET);
	if ($_md5==$_rmd5)
	{
		$_rest=base64_decode($_rest);
		$CONST_LICENSE_NAME=substr($_rest,strpos($_rest,":")+1);
		$pos=strpos($CONST_LICENSE_NAME,":");
		if ($pos)
		{
			$CONST_LICENSE_HOST=substr($CONST_LICENSE_NAME,$pos+1);
			$CONST_LICENSE_NAME=substr($CONST_LICENSE_NAME,0,$pos);
		}

		$CONST_LICENSE_VALID = $unix = substr($_rest,0,strpos($_rest,":"))+0;
		//$CONST_LICENSE_VALID=date("20y-m-d",$unix);
		$d=date("Y-m-d",$CONST_LICENSE_VALID);
		$CONST_LICENSE_SERVERS=$unix-mktime(1,0,0,substr($d,5,2),substr($d,8,2),substr($d,0,4));
		if (time()>$unix) $CONST_LICENSE_INVALID=1;
	}

	unset($_md5); unset($_rest); unset($_rmd5);
}
unset($CONST_LICENSE_SECRET);
unset($CONST_LICENSE_KEY);

define ('ADODB_DIR',dirname(__FILE__).'/../adodb/');
include(dirname(__FILE__)."/../include/request.h");
include_once (dirname(__FILE__)."/../include/adodb.h");
include_once (dirname(__FILE__)."/../include/fun.h");
include_once(dirname(__FILE__)."/../include/kameleon.h");
include_once(dirname(__FILE__)."/../include/smekta.h");
include(dirname(__FILE__)."/../include/request.h");

$kameleon->init(strlen($KAMELEON_LANG)?$KAMELEON_LANG:$lang,1,0,"",$referpage);

//if (!$noauth) include("auth.php");
$KAMELEON_MODE = strlen($_SERVER['REMOTE_ADDR']) ? 1 : 0;
if (!$noauth) include(dirname(__FILE__)."/../include/auth.h");

if (file_exists(dirname(__FILE__).'/../include/const.php')) include_once(dirname(__FILE__).'/../include/const.php'); else include_once(dirname(__FILE__).'/../include/const.h');

$kameleon->setpagelang($lang);

if (strstr(strtolower($CHARSET),'utf') ) $adodb->adodb->SetCharSet('UTF-8');

if (isset($_GET["return_path"])) 
{
	$referpage=base64_decode($_GET["return_path"]);
	$adodb->SetCookie("referpage",$referpage);
}

// ŁADOWANIE PLUGINU
$plugin_path = substr(stristr($_SERVER["PHP_SELF"],"plugins"),8);
$plugin_name = str_replace(stristr($plugin_path,"/"),"",$plugin_path);
if (strlen($plugin_name)==0) die('Problem with plugin directory');

$CONST_PLUGIN_SUBNAME='';

@include $plugin_name."/const.php";
@include $plugin_name."/ver.php";

$check_plugin_sql="SELECT pl_version AS version FROM plugins WHERE pl_name='$plugin_name' AND pl_subname='$CONST_PLUGIN_SUBNAME'";

if (!$sql = @pg_query($adodb->adodb->_connectionID, $check_plugin_sql)) die('Kameleon too old version. Please update to 5.02 version');
if (pg_num_rows($sql)==0) 
{
  $version=0;
  $sql="";
  if (file_exists('update/db.sql'))
  {
    $sql.=smekta(file_get_contents('update/db.sql'),get_defined_vars());

  }

  $sql.="; INSERT INTO plugins (pl_name, pl_subname, pl_version, pl_update) VALUES ('".$plugin_name."','$CONST_PLUGIN_SUBNAME' , 1, NOW());";
  

  $kameleon->adodb->execute($sql);

}
else
{
  list($version)=pg_fetch_row($sql);
}

// UPDATE BAZY - WERYFIKACJA


if ($plugin_version>0 && $version<$plugin_version)
{
  
  for ($c=$version+1;$c<=$plugin_version;$c++)
  {
    $sql="";
    if (file_exists(dirname(__FILE__).'/'.$plugin_name.'/update/'.$c.'.sql'))
    {
	  $file=dirname(__FILE__).'/'.$plugin_name.'/update/'.$c.'.sql';
	  $sql.=smekta(file_get_contents($file),get_defined_vars());
    }
    $sql.=";\nUPDATE plugins SET pl_version=".$c.", pl_update=NOW() WHERE pl_name='".$plugin_name."' AND pl_subname='$CONST_PLUGIN_SUBNAME';";
    $res=$kameleon->adodb->execute($sql);
	if (!$res)
	{
		$kameleon->adodb->debug=1;
		$kameleon->adodb->execute($sql);
		die('<pre>'.$sql);
	}
  }
}

// USTAWIENIE WERSJI JEZYKOWEJ
$lng=strlen($ulang)?$ulang:$lang;
if (!file_exists('lang/'.$lng.'.php')) $lng='en';
@include('lang/'.$lng.'.php');

// ZAŁADOWANIE CONSTA
$SZABLON_PATH = "../../".$adodb->getCookie("_SZABLON_PATH");
if (file_exists("$SZABLON_PATH/const.h") && !strlen($error)  ) include("$SZABLON_PATH/const.h");
if (file_exists("$SZABLON_PATH/const.php") && !strlen($error)) include("$SZABLON_PATH/const.php");

// USTAWIENIE UIMAGES
$ver = $adodb->getCookie("ver");
push($ver);
for (;$ver>0;$ver--)
{
	if (is_array($CONST_EXCLUDE_MINOR_VERS)) if (in_array($ver,$CONST_EXCLUDE_MINOR_VERS)) continue;

	eval("\$KAMELEON_UIMAGES=\"$DEFAULT_PATH_KAMELEON_UIMAGES\";");

	$UIMAGES=$KAMELEON_UIMAGES;
	if (file_exists($KAMELEON_UIMAGES)) break;
}
$UIMAGES_VER=$ver;
$ver=pop();

