<?
	include_once("modules/@crm/crmfun.h");

	if ( module_update($MODULES->crm->files->state) )
	{
		$title=toText($STATE[ps_title]);
	}


?>
