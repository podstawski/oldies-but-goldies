<?
include ("include/search.h");
$action="";
$katalog=$UIMAGES;
if ($BASIC_RIGHTS) $katalog.="/$USERNAME";
 
$path=urldecode($_POST['path']);
  
$dir=$katalog."/".$path;
// $dir=ereg_replace(" ","\\ ",$dir); 

$obrazek = $_FILES['obrazek']['tmp_name'];
$remotefile = $_POST['remotefile'];

if ( !@filesize($obrazek) && strstr($remotefile,":") )
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
$obrazek_name=ereg_replace("[^a-z|A-Z|0-9|\-|\_|\.]","_",$obrazek_name); 

if ($obrazek!="none")
 {
  if (file_exists("$dir/$obrazek_name"))
  {
    if ($overwrite)
    {
      move_uploaded_file($obrazek,"$dir/$obrazek_name");
    }
    else
    {
     $sysinfo=label("If you want to overwrite this file, check: overwrite and try again");
	}
  }
  else
  {
	move_uploaded_file($obrazek,"$dir/$obrazek_name");
  }
 }
 $path.="/".$curdir;
?>
