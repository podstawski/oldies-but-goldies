<?php

	$sid = $_POST['sid'];
 	$atrybut = $_POST['atrybut'];
	$wartosc = $_POST['wartosc'];
	$page_id = $_POST['page_id'];
	$server = $_POST['server'];
	$lang = $_POST['lang'];
	
	if (!$kameleon->checkRight('write','box',$sid) || !$kameleon->checkRight('write','page',$page_id))
	{
		$error=$norights;
		return;
	}	
	
$update_and =( $page_id >= 0) ? "AND id=$page_id" : "";	

if ( $sid == '' || $atrybut == '' || $wartosc == '' || $page_id == '' || $server == '' )
{
	return;   
}

switch($atrybut){
	case 'usluga': 
		$updateField = 'api';
		break;
	case 'typ': 
		$updateField = 'type';
		break;
	case 'level':
		$updateField = 'level';
		break;
} // switch

if ( $wartosc == 'NULL' ) 
{
    $updateField .= '=NULL';
}
else
{
	$updateField .= '=\''.$wartosc.'\'';
}

$query = "UPDATE webtd SET $updateField, nd_update=".time()."
		 WHERE sid=$sid AND server=$server AND page_id=$page_id AND lang='$lang';
		 UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
		 WHERE server=$server AND ver=$ver AND lang='$lang' $update_and;";	

//echo "$query";
if ($adodb->Execute($query)) 
{
	logquery($query);
	webver_td(0,0,$action,$sid);
}

