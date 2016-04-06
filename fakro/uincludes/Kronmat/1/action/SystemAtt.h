<?
$file=$_REQUEST[path];
$file="$UFILES/$file";
if (!file_exists($file)) return;

if (!strlen($AUTH[id])) include_once("$INCLUDE_PATH/autoryzacja/auth.h");
if (!haveRight2File(dirname($file),$AUTH[id])) 
{
	Header("Location: /att.php?path=$_REQUEST[path]");
	exit();
}

$type=@mime_content_type($file);
// uwaga: funkcja mime_content_type wymaga komilacji php: 
// --with-mime-magic 
$size=filesize($file);
$name=ereg_replace("_"," ",basename($file));

$ext=strtolower(substr($file,-4));

$save=0;
if ($ext==".doc")
{
	$save=1;
	$type="application/msword";
}

Header("Content-Length: $size");
Header("Content-Type: $type; name=\"$name\"");
if ($save) Header("Content-Disposition: attachment; filename=\"$name\"");
readfile($file);
exit();

?>