<?
 if ($SERVICE!="forum")
 {
	$api_action="";
	return;
 }

$api_action="";
include("include/forumfun.h");
DeleteForumItem($adodb,$api_id)

?>
