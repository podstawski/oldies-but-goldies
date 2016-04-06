<?
	include_once('include/webver.h');

	$sid=0;
	$sql="SELECT sid,noproof,title,id AS page_id
		FROM webpage WHERE id=$page AND server=$SERVER_ID AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($sql));
	
	if (!$sid) return;
	$sql="UPDATE webpage SET unproof_comment='' WHERE sid=$sid AND unproof_comment IS NULL";
	if ($adodb->execute($sql)) logquery($sql);


	$proof_comment=trim(addslashes(stripslashes($proof_comment)));
	$noproof=abs($noproof)+1;

	if (strlen($proof_comment))
	{
		$kto=$kameleon->user[fullname];
		if (strlen($kto)) $kto.=' ('.$kameleon->user[username].')';
		else $kto=$kameleon->user[username];
		
		$kto.=', '.date('d-m-Y H.i');

		$proof_comment="$kto\r\n$proof_comment\r\n \r\n";
	}

	$sql="UPDATE webpage SET noproof=$noproof,unproof_comment=unproof_comment || '$proof_comment' WHERE sid=$sid";

	//echo nl2br($sql); return;

	if ($adodb->execute($sql)) logquery($sql);
	
	if (!strlen($kameleon->user[email])) return;
		

	$sql="SELECT fullname,email,proof,passwd.username AS un 
			FROM passwd,rights WHERE server=$SERVER_ID 
			AND passwd.username=rights.username
			AND (ftp IS NULL OR ftp=0)";
	$res=$adodb->execute($sql);

	if (!$res->recordCount())
	{
		$sql="SELECT fullname,email,proof,passwd.username AS un 
				FROM passwd,rights WHERE server=$SERVER_ID 
				AND passwd.username=rights.username
				";
		$res=$adodb->execute($sql);
	}

	if (!$res->recordCount()) return;

	include_once('include/sendmail2.h');
	$mail = new sendmail_obj;

	$mail->from='<'.$kameleon->user[email].'> '.$kameleon->user[fullname].' ('.$kameleon->user[username].')';
	$mail->to=$mail->from;
	$mail->subject=label("Proof request").": ".$kameleon->current_server->nazwa." ($page)";

	
	$dir=substr(dirname($SCRIPT_NAME),1);

	if ($dir=='') $dir='.';
	if ($dir=='.') $dir='';
	else $dir.='/';


	if (strlen($dir)>1 || $dir=='') $dir="/$dir";

	$mail->type='html';
	
	$mail->msg='<h1>'.label("Page unproved").':</h1>';
	
	$href="http://$HTTP_HOST${dir}index.php?page=$page_id&SetServer=".$kameleon->current_server->nazwa;
	$mail->msg.="<h2><a href='$href'>$href";
	if (strlen($title)) $mail->msg.=" - $title ($page)";
	$mail->msg.="</a></h2>";
	if (strlen($proof_comment))
	{
		$mail->msg.="<p>".stripslashes($_REQUEST[proof_comment]).'</p>';
	}
	
	
	$mail->msg.="<p>".$kameleon->user[fullname].'</p>';

	for ($i=0;$i<$res->recordCount() ; $i++)
	{
		$fullname='';$email='';$proof='';
		parse_str(ado_explodeName($res,$i));
		if (!checkRights($page,$proof)) continue;

		if (!strlen($email)) continue;
		
		$mail->cc[]="<$email> $fullname ($un)";
	
		//echo "<br>$noproof ($title) $fullname,$email,$proof ";
	}

	if (is_array($mail->cc))
	{
		$mail->to=implode(',',$mail->cc);
		$mail->cc='';
	}

	sendmail2($mail);

	