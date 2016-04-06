<?
	include_once('include/webver.h');

	$sid=0;
	$sql="SELECT sid,noproof,title,proof_autor,unproof_autor 
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

	$sql="UPDATE webpage SET noproof=-1,unproof_comment=unproof_comment || '$proof_comment' WHERE sid=$sid";


	//echo "$proof_autor,$unproof_autor<br>".nl2br($sql); return;

	if ($adodb->execute($sql)) logquery($sql);
	

	if (!strlen($kameleon->user[email])) return;
		
	include_once('include/sendmail2.h');
	$mail = new sendmail_obj;
	$mail->from='<'.$kameleon->user[email].'> '.$kameleon->user[fullname].' ('.$kameleon->user[username].')';
	
	$fullname='';$email='';$username='';
	$sql="SELECT fullname,email,username FROM passwd WHERE username='$unproof_autor'";
	parse_str($adodb->ado_query2url($sql));
	$mail->to='<'.$email.'> '.$fullname.' ('.$username.')';
	
	
	$mail->subject=label("Page changes rejected").": ".$kameleon->current_server->nazwa." ($page)";

	
	$dir=substr(dirname($SCRIPT_NAME),1);

	if ($dir=='') $dir='.';
	if ($dir=='.') $dir='';
	else $dir.='/';

	if (strlen($dir)>1 || $dir=='') $dir="/$dir";

	$mail->msg=label("I reject");
	$mail->msg.=":\nhttp://$HTTP_HOST${dir}index.php?page=$page&SetServer=".$kameleon->current_server->nazwa;
	if (strlen($title)) $mail->msg.=" - $title ($page)";

	if (strlen($proof_comment))
	{
		$mail->msg.="\n\n".stripslashes($_REQUEST[proof_comment]);
	}

	$mail->msg.="\n\n".$kameleon->user[fullname];


	$fullname='';$email='';$username='';
	$sql="SELECT fullname,email,username FROM passwd WHERE username='$proof_autor'";
	parse_str($adodb->ado_query2url($sql));
	if (strlen($email)) $mail->cc=array('<'.$email.'> '.$fullname.' ('.$username.')');
	

	sendmail2($mail);
