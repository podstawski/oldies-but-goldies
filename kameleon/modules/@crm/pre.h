<?
	global $MODULES;

	foreach ($MODULES->crm->files AS $plik)
	{
		if ($plik->file==$html && strlen($plik->action->form))
		{

			$form=$plik->action->form;
			$cmd="global \$$form; \$${form}[edit]=\"edit.php?page=$page&page_id=$WEBTD->page_id&pri=$WEBTD->pri\";";
			eval($cmd);
		}
	}

	include("$INCLUDE_PATH/.pre.h");
?>
