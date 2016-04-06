<?
	$norights=label("Insufficient rights");
	$label_pages=label("Pages");
	$label_menu=label("Menu");
	
	$save_and_restore_dir="$UFILES/.html";

	$page+=0;


	
	$noproof="";
	$query="SELECT noproof,unproof_autor FROM webpage  WHERE id=$page AND webpage.ver=$ver 
			AND webpage.lang='$lang' AND webpage.server=$SERVER_ID";

	parse_str(ado_query2url($query));
	
	if (!$MAY_PROOF)
	{
		
		if ( strlen($noproof) && abs($noproof)!=1 ) 
		{
			if ($action!='KameleonChpass' && $action!='KameleonLogout') $error=$norights;
		}

		$mark_page_as_unproved_if_required=",noproof=1,unproof_date=".time()."
						,unproof_autor='$KAMELEON[username]',unproof_counter=unproof_counter+1";

	}
	else
	{
		if ((!$noproof && $unproof_autor==$KAMELEON[username]) || !strlen($noproof)) 
			$mark_page_as_unproved_if_required=",noproof=0,unproof_counter=unproof_counter+1,unproof_autor='$KAMELEON[username]',unproof_date=".time();
	}


	$adodb->action_state=1;
	if ($debug_mode) $adodb->debug=1;

	if ( strlen($action) && !strlen($error)) include('include/webver.h');

	while ( strlen($action) && !strlen($error))
	{
		$adodb->action_name=$action;
		if (file_exists("include/action/$action.h")) 
		{
			$_action_redirect=1;
			$oldaction=$action;
			include ("include/action/$action.h");
			if ($oldaction==$action) $action="";
		}
		else break;
	}
	$adodb->action_state=0;
	
	if (is_object($auth_acl) && $mybasename=="ajax") $error=""; // cartman, aby wszedł w ajax

	if (strlen($error)) echo "<script>alert('".addslashes($error)."'); history.back()</script>";
	if (strlen($error)) exit();

	
	if (is_array($PAGE_ID_TRANSLATION) || is_array($MENU_ID_TRANSLATION))
	{
		include('include/action/ID_translation.h');
	}

	$adodb->debug=0;


