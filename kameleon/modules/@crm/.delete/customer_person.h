<?
	include_once("modules/@crm/crmfun.h");


	$query="SELECT sid FROM webtd WHERE server=$SERVER_ID
		AND page_id=$page_id AND ver=$ver AND lang='$lang'
		AND pri=$pri ";
	parse_str(ado_query2url($query));


	module_delete($MODULES->crm->files->customer_slave);

?>