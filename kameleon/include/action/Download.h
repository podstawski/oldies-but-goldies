<?
	include ("include/search.h");

	$action="";

	include("include/ufiles_const.h");
	include_once('include/file.h');

	$katalog=$rootdir;
	$path=urldecode($_POST['lista']);
	
	  
	$file=$katalog."/".$path;

	$wf_accesslevel=webfile($path,$galeria,'wf_accesslevel');
	if (strlen($wf_accesslevel)) 
	{
		$name=basename($file);
		Header("Content-Type: application/x; name=\"$name\"");
		Header("Content-Length: ".filesize($file));
		Header("Content-Disposition: attachment; filename=\"$name\"");
		readfile($file);
		die();
	}
	else $error=label("Insufficient rights");


?>