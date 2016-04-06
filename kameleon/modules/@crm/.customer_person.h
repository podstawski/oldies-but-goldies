<?

	if (!module_select($MODULES->crm->files->customer_slave))
	{
		module_update($MODULES->crm->files->customer_slave);
		module_select($MODULES->crm->files->customer_slave);
	}
	_display_form($MODULES->crm->files->customer_slave);


?>
