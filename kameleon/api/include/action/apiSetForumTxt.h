<?
	$api_action="";
	if ($SERVICE!="forum")
	{
		return;
	}
	
	include("include/forumfun.h");
	include_once("include/captcha/kcaptcha.php");	
	if ($error = KCAPTCHA::error())
	{
		 return;
	}
	
	$msg=toText($msg);
	
	$forum_osoba=validateText($forum_osoba);
	if (!strlen($forum_osoba)) $error = label("Type your name");
	if (strlen($error)) return;
	
	$forum_temat=validateText($forum_temat);
	if (!strlen($forum_temat)) $error = label("Type subject");
	if (strlen($error)) return;
	
	$forum_msg=validateText($forum_msg);
	if (!strlen($forum_msg)) $error = label("Type any text");
	if (strlen($error)) return;

	$e_forum_msg=$forum_msg;
	$e_forum_temat=$forum_temat;
	$e_forum_osoba=$forum_osoba;

	$query="SELECT slownik FROM forum_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
		parse_str(ado_ExplodeName($result,0));

	if (strlen($slownik))
	{
		$patterns=explode(":",$slownik);
		for ($p=0;$p<count($patterns);$p++)
		{
			$pattern=$patterns[$p];
			if (strlen($pattern))
			{
				$forum_msg=eregi_replace("$pattern","***",$forum_msg);
				$forum_temat=eregi_replace("$pattern","***",$forum_temat);
				$forum_osoba=eregi_replace("$pattern","***",$forum_osoba);
			}
		}
	}



	SetForumTxt($adodb,$serwisID,$pokazuj+0,0,$forum_temat,$forum_msg, $forum_osoba);

	include("include/sendmail.h");
   	$query="SELECT * FROM forum_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
	{
		parse_str(ado_ExplodeName($result,0));
		if (strlen($email))
		{
			$from=$email;
			$to=$email;
	        $subject="$e_forum_temat | $subject";
	        $msg="$e_forum_osoba\n$e_forum_msg";
	        sendmail($from,$to,$subject,$msg);
		}
	}
	$forumuser = GetForumUser($adodb,$pokazuj+0);

	if ( $pokazuj>0 && $forumuser>0 )	
	{
		$sendmail_action="SetForumTxt";
		$mailto=UserMailOnId($forumuser);
		$temat=trim(GetForumSubject($adodb,$pokazuj+0));
		if ($api_km)
			$href="$api_next&";
		else
			$href="$api_next?";
		$link="${href}pokazuj=$pokazuj&forumid=$pokazuj";
		
	//	$action="SendMail";
	}
	
	if (isset($forum_ekspert_id))
	{
		$sendmail_action="ExpertQuery";
		
		$EXPERT_NAME=UserNameOnId(0+$forum_ekspert_id);
	    $EXPERT_MAIL=UserMailOnId(0+$forum_ekspert_id);
	//	$action="SendMail";	
	}
	
?>
