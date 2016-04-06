<?
	include_once("modules/@crm/crmfun.h");


	$query="SELECT sid AS _sid FROM webtd WHERE server=$SERVER_ID
		AND page_id=$page_id AND ver=$ver AND lang='$lang'
		AND pri=$pri ";
	parse_str(ado_query2url($query));

	global $sid;
	$sid=$_sid;


	module_update($MODULES->crm->files->customer_slave);
	$title=toText($PERSON[c_person]);	


?>