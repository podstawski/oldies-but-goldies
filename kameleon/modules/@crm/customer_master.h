<?
	global $MODULES;

	if (!module_select($MODULES->crm->files->customer_master)) return;
	_display_view($MODULES->crm->files->customer_master);


?>
