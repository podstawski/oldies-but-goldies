<?

	global $SENDMAIL,$MODULES;

	$SENDMAIL[from]="";
	$SENDMAIL[to]="";
	$SENDMAIL[cc]="";
	$SENDMAIL[bcc]="";
	$SENDMAIL[subject]="";
	$SENDMAIL[action]=$WEBTD->mod_action;
	$SENDMAIL[title]=$WEBTD->title;
	$msg_pre="";
	$msg_post="";


	$obj=xml2obj($WEBTD->costxt);

	if (is_Object($obj) )
	{
		$obj=$obj->xml;
		$SENDMAIL[to]=$obj->to;
		$SENDMAIL[from]=$obj->from;
		$SENDMAIL[subject]=stripslashes($obj->subject);
		if (is_array($obj->bcc) ) $SENDMAIL[bcc]=$obj->bcc;
		else $SENDMAIL[bcc]=array($obj->bcc);
		if (is_array($obj->cc) ) $SENDMAIL[cc]=$obj->cc;
		else $SENDMAIL[cc]=array($obj->cc);

		$SENDMAIL[debug]=$obj->debug;
		$SENDMAIL[interactive]=$obj->interactive;

	}
	
	if (file_exists("$UIMAGES/textstyle.css"))
		$msg_pre.="<link href=\"$UIMAGES/textstyle.css\" rel=\"stylesheet\" type=\"text/css\">";

	if (strlen($WEBTD->bgimg))
	{
		$msg_pre.="<body background=\"$UIMAGES/$WEBTD->bgimg\">";
		$msg_post="</body>";

	}
	$sid=$WEBTD->sid;

	eval("\$fpath = \"$CONST_SENDMAIL_UFILES\";");
	$att="";
	if ($fpath && file_exists("$UFILES/$fpath"))
	{
		$att=explode_path("$UFILES/$fpath",true);
	}

	$SENDMAIL[att]=$att;

	$SENDMAIL[msg] = stripslashes($WEBTD->plain);
	
	
	$SENDMAIL[self]=$self;
	$SENDMAIL[next]=$next;

	$SENDMAIL[cc_imploded]=implode("<br>",$SENDMAIL[cc]);
	$SENDMAIL[bcc_imploded]=implode("<br>",$SENDMAIL[bcc]);


	$subject=ereg_replace("\"","\\\"",$SENDMAIL[subject]);


	if ($SENDMAIL[interactive])
	{
		$SENDMAIL[hidden] = "<INPUT TYPE=\"hidden\" name=\"action\" value=\"CrmSendMail\">
		<INPUT TYPE=\"hidden\" name=\"from\" value=\"$SENDMAIL[from]\">
		<INPUT TYPE=\"hidden\" name=\"to\" value=\"$SENDMAIL[to]\">
		<INPUT TYPE=\"hidden\" name=\"debug\" value=\"$SENDMAIL[debug]\">
		<INPUT TYPE=\"hidden\" name=\"subject\" value=\"$subject\">	
		<TEXTAREA COLS=1 ROWS=1 STYLE=\"visibility:hidden\" name=\"msg\">$msg_pre$SENDMAIL[msg]$msg_post</TEXTAREA>
		";
	
		while ( is_array($SENDMAIL[cc]) && list($key,$val) = each($SENDMAIL[cc]) )
		{
			if (!strlen($val)) continue;
			$SENDMAIL[hidden].="<INPUT TYPE=\"hidden\" name=\"cc[$key]\" value=\"$val\"> \n";
		}

		while ( is_array($SENDMAIL[bcc]) && list($key,$val) = each($SENDMAIL[bcc]) )
		{
			if (!strlen($val)) continue;
			$SENDMAIL[hidden].="<INPUT TYPE=\"hidden\" name=\"bcc[$key]\" value=\"$val\"> \n";
		}

		$SENDMAIL[interactive_disable]="";
	}
	else
	{
		$SENDMAIL[interactive_disable]="style=\"display:none\"";
	}

	$SENDMAIL[date]="";

	$SENDMAIL[att_imploded]="";
	while ( is_array($SENDMAIL[att]) && list($key,$val) = each($SENDMAIL[att]) )
	{
		if ($SENDMAIL[interactive]) $SENDMAIL[hidden].="<INPUT TYPE=\"hidden\" name=\"att[$key]\" value=\"$val\"> \n";

		if (strlen($SENDMAIL[att_imploded])) $SENDMAIL[att_imploded].="<br>";
		$SENDMAIL[att_imploded].="<a href=\"$key\">".basename($key)."</a>";
	}

	

	if ($action_progress)
	{
		$SENDMAIL[hidden]="";
		$SENDMAIL[msg]="$msg_pre$SENDMAIL[msg]$msg_post";
		global $HTTP_POST_VARS;
		$HTTP_POST_VARS = "";
		if (strlen($auto_module_att)) 
		{
			$att=explode_path($auto_module_att,true);
			$SENDMAIL[att]=array_merge($SENDMAIL[att],$att);
		}
	
		include("$INCLUDE_PATH/action/CrmSendMail.h");

		$SENDMAIL[att_imploded]="";
		while ( is_array($SENDMAIL[att]) && list($key,$val) = each($SENDMAIL[att]) )
		{	
			if (!file_exists($key)) continue;
			if (strlen($SENDMAIL[att_imploded])) $SENDMAIL[att_imploded].="<br>";
			$SENDMAIL[att_imploded].="<a href=\"$key\">".basename($key)."</a>";
		}

		$SENDMAIL[cc_imploded]=implode("<br>",$SENDMAIL[cc]);
		$SENDMAIL[bcc_imploded]=implode("<br>",$SENDMAIL[bcc]);

		$SENDMAIL[date]=date("d-m-Y, H:i");
	}
	

	_display_view($MODULES->crm->files->sendmail);


?>