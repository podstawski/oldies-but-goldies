<?
	global $SENDMAIL,$MODULES;
	
	
	include("$INCLUDE_PATH/.api/sendmail_const.h");
	include_once("include/sendmail2.h");



	$UIMAGES=$KAMELEON_UIMAGES;
	$UFILES=$KAMELEON_UFILES;

	$SENDMAIL[from]="";
	$SENDMAIL[to]="";
	$SENDMAIL[cc]=null;
	$SENDMAIL[bcc]=null;
	$SENDMAIL[subject]="";
	$SENDMAIL[action]=$WEBTD->mod_action;
	$SENDMAIL[title]=$WEBTD->title;
	$msg_pre="";
	$msg_post="";


	$obj=xml2obj($WEBTD->costxt);

	if (is_Object($obj) )
	{
		$obj=$obj->xml;
//		print_r($obj);
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



	if (strlen($WEBTD->bgimg) || strlen($WEBTD->bgcolor) )
	{

		$body_params="";
		if (strlen($WEBTD->bgimg)) $body_params.=" background=\"$UIMAGES/$WEBTD->bgimg\"";
		if (strlen($WEBTD->bgcolor)) $body_params.=" bgcolor=\"#$WEBTD->bgcolor\"";

		$msg_pre.="<body$body_params>";
		$msg_post="</body>";

	}

	push($sid);
	$sid=$WEBTD->sid;
	eval("\$fpath = \"$CONST_SENDMAIL_UFILES\";");

	$sid=pop();

	$att="";
	if ($fpath && file_exists("$UFILES/$fpath"))
	{
		$att=explode_path("$UFILES/$fpath",true);
	}



	$SENDMAIL[att]=$att;

	$SENDMAIL[msg] = stripslashes($WEBTD->plain);
	
	$SENDMAIL[self]=$self;
	$SENDMAIL[next]=$next;

	$SENDMAIL[cc_imploded]="";
	$SENDMAIL[bcc_imploded]="";
	if (is_array($SENDMAIL[cc])) $SENDMAIL[cc_imploded]=implode("<br>",$SENDMAIL[cc]);
	if (is_array($SENDMAIL[bcc])) $SENDMAIL[bcc_imploded]=implode("<br>",$SENDMAIL[bcc]);



	if ($SENDMAIL[interactive])
	{
		$SENDMAIL[hidden] = "<INPUT TYPE=\"hidden\" name=\"action\" value=\"ApiSendMail\">
		<INPUT TYPE=\"hidden\" name=\"ACTION_VAR\" value=\"SENDMAIL,SENDMAIL_cc,SENDMAIL_bcc,SENDMAIL_att\">
		<INPUT TYPE=\"hidden\" name=\"SENDMAIL[from]\" value=\"$SENDMAIL[from]\">
		<INPUT TYPE=\"hidden\" name=\"SENDMAIL[to]\" value=\"$SENDMAIL[to]\">
		<INPUT TYPE=\"hidden\" name=\"SENDMAIL[debug]\" value=\"$SENDMAIL[debug]\">

		<TEXTAREA COLS=1 ROWS=1 STYLE=\"visibility:hidden\" name=\"SENDMAIL[subject]\">$SENDMAIL[subject]</TEXTAREA>
		<TEXTAREA COLS=1 ROWS=1 STYLE=\"visibility:hidden\" name=\"SENDMAIL[msg]\">$msg_pre$SENDMAIL[msg]$msg_post</TEXTAREA>
		";
	
		while ( is_array($SENDMAIL[cc]) && list($key,$val) = each($SENDMAIL[cc]) )
		{
			if (!strlen($val)) continue;
			$SENDMAIL[hidden].="<INPUT TYPE=\"hidden\" name=\"SENDMAIL_cc[$key]\" value=\"$val\"> \n";
		}

		while ( is_array($SENDMAIL[bcc]) && list($key,$val) = each($SENDMAIL[bcc]) )
		{
			if (!strlen($val)) continue;
			$SENDMAIL[hidden].="<INPUT TYPE=\"hidden\" name=\"SENDMAIL_bcc[$key]\" value=\"$val\"> \n";
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
		if ($SENDMAIL[interactive]) 
			$SENDMAIL[hidden].="<INPUT TYPE=\"hidden\" name=\"SENDMAIL_att[$key]\" value=\"$val\"> \n";

		if (strlen($SENDMAIL[att_imploded])) $SENDMAIL[att_imploded].="<br>";
		$SENDMAIL[att_imploded].="<a href=\"$key\">".basename($key)."</a>";
	}




	if ($action_progress)
	{
		$SENDMAIL[hidden]="";
		$SENDMAIL[msg]="$msg_pre$SENDMAIL[msg]$msg_post";
		include("$INCLUDE_PATH/.api/action/ApiSendMail.h");
	}
	else
	{
		$MODULES->api->files->sendmail->INCLUDE_PATH=$INCLUDE_PATH;
		_display_view($MODULES->api->files->sendmail);
	}


?>
