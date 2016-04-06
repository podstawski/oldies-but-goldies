<?
	include("../include/request.h");
	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');

	if (strlen($WKSESSID)) $_COOKIE["WKSESSID"]=$WKSESSID;

    define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");


	include_once ("../include/fun.h");
	include_once("../include/kameleon.h");
	include("../include/request.h");		

	include("../include/const.h");

	eval("\$KAMELEON_UIMAGES=\"$DEFAULT_PATH_KAMELEON_UIMAGES\";");
	$UIMAGES=$KAMELEON_UIMAGES;	

	$SZABLON_PATH=$adodb->getCookie('_SZABLON_PATH');
	include('../include/ufiles_const.h');
	//echo $rootdir."<br />";
	//echo $_GET["plik"];
	$_GET["plik"]=str_replace($rootdir."/","",$_GET["plik"]);
	if (strlen($_GET[plik]))
	{
		$plik=$_GET[plik];
		$dir="../$rootdir";
		$adodb->setCookie('img_plik',$plik);
		$adodb->setCookie('img_dir',$dir);
		$adodb->setCookie('img_galeria',$galeria);
	}
	else
	{
		$plik=$adodb->getCookie('img_plik');
		$dir=$adodb->getCookie('img_dir');
		$galeria=$adodb->getCookie('img_galeria');
	}


	$IMConfig['base_url'] = $dir;
	$IMConfig['base_dir'] = $dir;
	$IMConfig['server_name'] = $_SERVER[SERVER_NAME];

	if (!isset($_GET['img'])) $_GET['img']=$plik;

	$path="$dir/$plik";

	

	$IMConfig['tmp_prefix'] = $CONST_IMG_EDITOR;


	/*
		Specify the paths of the watermarks to use (relative to $IMConfig['base_dir']).
		Specifying none will hide watermarking functionality.
	*/

	$IMConfig['watermarks']=array();

	

	if (is_dir("$dir/watermarks"))
	{
		$handle=@opendir("$dir/watermarks");

		while (($file = @readdir($handle)) !== false) 
		{
			clearstatcache();
			if (is_file("$dir/watermarks/$file") && (strstr(strtolower($file),'gif') || strstr(strtolower($file),'png')) )
			{
				$IMConfig['watermarks'][]="/watermarks/$file";
			}
		}
		@closedir($handle); 
	}


	$title=label('Image editor');

	$adodb->Close('');

	switch ($lang)
	{
		case 'p':
		case 'i':
		case 'pl':
			$IMConfig[lang]='pl';
			break;
		case 'e':
		case 'en':
			$IMConfig[lang]='en';
			break;
		case 'r':
		case 'ru':
			$IMConfig[lang]='ru';
			break;
		case 'd':
		case 'de':
			$IMConfig[lang]='de';
			break;
		case 'f':
		case 'fr':
			$IMConfig[lang]='fr';
			break;

		case 'nl':
			$IMConfig[lang]='nl';
			break;

		default:
			$IMConfig[lang]='en';
			break;
	}
?>