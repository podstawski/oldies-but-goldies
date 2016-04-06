<?
include ("include/search.h");

$action="";
$katalog=$UFILES;
if ($BASIC_RIGHTS) $katalog.="/$USERNAME";

$path=urldecode($path);

$newdir=unpolish($newdir);
$newdir=ereg_replace("[^a-z|A-Z|0-9|\-|\_|\.]","_",$newdir); 


if (strlen($newdir))
{
	clearstatcache();
	$dir=$katalog."/".$path;
 	$msg=label("Directory already exists !");
 	if (is_dir("$dir/$newdir"))
		echo "<script>alert('$msg')</script";
	else
	{
		mkdir("$dir/$newdir",0755);
		$curdir=$newdir;
	}
}
?>
