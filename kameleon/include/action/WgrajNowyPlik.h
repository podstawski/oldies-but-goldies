<?
include ("include/search.h");

$action="";

include("include/ufiles_const.h");
include_once('include/file.h');

$katalog=$rootdir;
if ($BASIC_RIGHTS) $katalog.="/$USERNAME";
 
$path=urldecode($_POST['path']);
  
$dir=$katalog."/".$path;

$remotefile = $_POST['remotefile'];
$obrazek = $_FILES['obrazek']['tmp_name'];
$obrazek_name = $_FILES['obrazek']['name'];

if ( !$_FILES['obrazek']['size'] && strstr($remotefile,":") )
{
	$f=@fopen($remotefile,"rb");
	
	if ($f)
	{
		$upload_temp_dir = $adodb->getUploadTempDir();
		$s=fread($f,10000000);
		fclose($f);
		
		if (!file_exists($obrazek)) $obrazek = $upload_temp_dir . uniqid($PHP_AUTH_USER);
		$f=fopen($obrazek,"wb");
		fwrite($f,$s);
		fclose($f);
		$obrazek_name=basename($remotefile);
	}
	else
		$obrazek="";
}

$obrazek_name=unpolish($obrazek_name);
$obrazek_name=ereg_replace("[^a-z|A-Z|0-9|_|\.\-]","_",$obrazek_name); 

if ($obrazek!="none")
{
	if (file_exists("$dir/$obrazek_name") && !$overwrite)
		$sysinfo=label("If you want to overwrite this file, check: overwrite and try again");
	else
	{
		$wf_accesslevel=webfile("$path/$obrazek_name",$galeria,'wf_accesslevel');
		if (strlen($wf_accesslevel)) move_uploaded_file($obrazek,"$dir/$obrazek_name");
		else $sysinfo=label("Insufficient rights");
	}
}


?>