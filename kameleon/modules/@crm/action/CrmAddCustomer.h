<?
	global $page_id,$page, $referer,$MODULES;
	global $PHP_AUTH_USER,$DEFAULT_TD_LEVEL,$pri,$page_id;
	global $HTTP_GET_VARS;


	push($page,$page_id,$HTTP_GET_VARS);

	$referer=$page;
	$page_id=-1;
	$page=-1;
	include("include/action/DodajStrone.h");
	$page=$page_id;

	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->customer_master->file;
	$HTTP_GET_VARS="";
	$HTTP_GET_VARS["html"]=$_html;
	include("$INCLUDE_PATH/action/CrmAddTD.h");

	module_update($MODULES->crm->files->customer_master);
	$customer_page=$page_id;


	pop(&$page,&$page_id,&$HTTP_GET_VARS);

	$location_reload="edit.php?page=$customer_page&page_id=$customer_page&pri=1";

?>