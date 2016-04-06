<?php
/**
 * ZapiszMenu
 *
 * @version $Id$
 * @copyright 2005 
 **/

$action="";

$sid		= $_POST['sid'];
$ver 		= $_POST['ver'];
$page_id 	= $_POST['page_id'];
$page 		= $_POST['page'];
$server 	= $_POST['server'];
$lang 		= $_POST['lang'];
$menu_id 	= $_POST['menu_id'];

if ( $sid == '' || $ver == '' || $page_id == '' || $page == '' || $server == '' || $lang == '' || $menu_id == '' )
{
	return;   
}

 
if ($menu_id == -1)
{
	include ("include/menu_max.h");
}

$menu_id += 0;
if (!$menu_id) $menu_id="NULL";

$update_and = ( $page_id >= 0) ? "AND id=$page_id" : "";
	
$query = "UPDATE webtd SET menu_id=$menu_id, nd_update=".time()." 
		 WHERE sid=$sid AND server=$server AND page_id=$page_id AND lang='$lang';
		 UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
		 WHERE server=$server AND ver=$ver AND lang='$lang' $update_and;";	

//echo $query;
		 
if ($adodb->Execute($query)) 
			logquery($query) ;
?>