<?
	global $SENDMAIL;


	$SENDMAIL[from]="";
	$SENDMAIL[to]="";
	$SENDMAIL[cc]="";
	$SENDMAIL[bcc]="";
	$SENDMAIL[att]="";
	$SENDMAIL[page]=$page;
	$SENDMAIL[debug_checked]="";
	$SENDMAIL[action]=$mod_action;
	$SENDMAIL[subject]="";

	$obj=xml2obj($costxt);
	if (is_Object($obj) )
	{
		$obj=$obj->xml;
		$SENDMAIL[to]=$obj->to;
		$SENDMAIL[from]=$obj->from;
		$SENDMAIL[subject]=stripslashes($obj->subject);
		if (is_array($obj->bcc) ) $SENDMAIL[bcc]=implode("\n",$obj->bcc);
		else $SENDMAIL[bcc]=$obj->bcc;
		if (is_array($obj->cc) ) $SENDMAIL[cc]=implode("\n",$obj->cc);
		else $SENDMAIL[cc]=$obj->cc;

		if ($obj->debug) $SENDMAIL[debug_checked]="checked";
		if ($obj->interactive) $SENDMAIL[interactive_checked]="checked";


	}

	eval("\$fpath = \"$CONST_SENDMAIL_UFILES\";");
	$SENDMAIL[cookieufpath]="$fpath";
	mkdir_p("$UFILES/$fpath");

	if (strlen($fpath) && file_exists("$UFILES/$fpath"))
	{
		$att=explode_path("$UFILES/$fpath");

		for ($i=0; is_array($att) && $i<count($att); $i++)
		{
			if (strlen($SENDMAIL[att])) $SENDMAIL[att].=", ";
			$SENDMAIL[att].=basename($att[$i]);
		}
	}


	_display_form($MODULES->crm->files->sendmail);
?>