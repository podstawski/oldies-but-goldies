<?
 $action="";
 $katalog=$UFILES;
 if ($BASIC_RIGHTS) $katalog.="/$USERNAME";
 
 $path=urldecode($path);

 clearstatcache();
 $msg=label("You can not delete the root directory !");
 if ($lista=="")
   echo "<script>alert('$msg')</script>";
 else
 {
 	$dir = "$katalog/".$lista; 
	$handle=opendir("$dir");
	$lp=0;
	while (($file = readdir($handle)) !== false) 
	{
		if ($file=="." || $file=="..") continue;
		$lp++;
	}
	closedir($handle); 
	if ($lp==0)
	{
		rmdir("$dir");
		$curdir="";
	}
	else
	{
	     $alert=label("Directory is not empty. First delete all files from this directory");
	     echo "<script>alert('$alert')</script>";
	}
 }
 $path.="/".$curdir;
?>
