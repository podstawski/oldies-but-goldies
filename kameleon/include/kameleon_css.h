<?php
//debug zawsze wy³aczony
$adodb->debug_ips = array();

$action = $_GET['act'];
require_once ('include/cache.h');

$CSS_CACHE_TOKEN='css2.'.$action.'.'.$SERVER_ID.'.'.$ver.'.js';



if (kameleonCache($CSS_CACHE_TOKEN))
{
	echo kameleonCacheContent($CSS_CACHE_TOKEN);
	exit();
}
ob_start();

include("include/userclass.h");


//echo '<pre>';print_r($USERCLASS);

echo "CKEDITOR.addStylesSet( 'kameleon_styles',\n[\n";
$firstline_puked=false;
foreach ( $USERCLASS AS $class)
{
	if (!strlen($class[0]) || !strlen($class[1])) continue;

	if ($firstline_puked) echo ",\n";
	$firstline_puked=true;

	$pos=strpos($class[0],' ');
	if ($pos) $class[0]=substr($class[0],0,$pos);
	$pos=strpos($class[1],' ');
	if ($pos) $class[1]=substr($class[1],0,$pos);
	

	echo "{ name : '$class[0]', element : 'span', attributes : { 'class' : '$class[1]' } }";
	
}

echo "\n]);\n";




$out=ob_get_contents();
ob_end_clean();	
if (strlen($CSS_CACHE_TOKEN)) kameleonCache($CSS_CACHE_TOKEN,$out);
echo $out;


