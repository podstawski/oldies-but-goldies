<?php
	$uri=$_SERVER['REQUEST_URI'];
	//echo "$uri <br>";

	$pos=0;
	if (strlen($NAVI['print']['needle']))
	{
		$len=strlen($NAVI['print']['needle']);
		if (substr($uri,0,$len)==$NAVI['print']['needle']) $pos=$len;
	}
	if (strlen($NAVI['print']['target']))
	{
		$uri=substr($uri,0,$pos).$NAVI['print']['target'].substr($uri,$pos);
	}


	$print_html='';
	
	$scn=$uri;
	if ($pos=strpos($uri,'?')) $scn=substr($scn,0,$pos);

	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$scn))
	{
		$print_html='<a href="'.$uri.'">'.$uri.'</a>';
		$uri='';
		
	}

	$print_params='x='.$NAVI['print']['x'].', y='.$NAVI['print']['y'].', width='.$NAVI['print']['w'].', height='.$NAVI['print']['h'];
	$print_params.=', scrollbars=yes';

	$url_favorite = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$href_favorite = "window.external.AddFavorite('".$url_favorite."','".$NAVI['fav']['prefix']."'+JSTITLE)";


	if (!function_exists('kameleon_sendmail'))
	{
		function kameleon_sendmail($sendmail_path,$MAILF,$html)
		{

			$charset=strlen($MAILF[charset])?$MAILF[charset]:'iso-8859-2';

			$mail = "From: ".$MAILF[mailfrom]."\nTo: ".$MAILF[mailto]."\nSubject: ".$MAILF[subject]."\nMIME-Version: 1.0";
			$mail.= "\nContent-Type: text/html; charset=\"$charset\"\nContent-Transfer-Encoding: base64\n\n";

			$mail.= chunk_split(base64_encode("<html><body>$html</body></html>"));

	
			if (!strlen($sendmail_path)) $sendmail_path = ini_get ("sendmail_path");


			$prg=popen($sendmail_path,"w");
			fwrite($prg,$mail);
			pclose($prg);
		}
	}

	global $REMARK,$RECOMMEND;

	if (is_array($REMARK))
	{
		
		$REMARK[mailto]=$NAVI['remark']['email'];
	
		$html="<div>".$REMARK[text]."</div>";
		$href='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$html.="<a href=\"$href\">".$REMARK[title]." [$page]</a>";

		kameleon_sendmail($NAVI['remark']['sendmail'],$REMARK,$html);

	}
	
	if (is_array($RECOMMEND))
	{

		$html="<div>".$RECOMMEND['name'].$NAVI['recommend']['header']."</div>";
		$href='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$html.="<a href=\"$href\">".$RECOMMEND[title]."</a>";
		$html.="<div>".$RECOMMEND['text']."</div>";
		$html.="<div>".$NAVI['recommend']['footer']."</div>";

		kameleon_sendmail($NAVI['recommend']['sendmail'],$RECOMMEND,$html);

	}
	



?>


<script language="javascript" type="text/javascript">
	var print_window=null;
	var email_regex=/^[a-zA-Z0-9\.\-\_]{1,90}@[a-zA-Z0-9\-\.]{1,128}.[a-zA-Z0-9]+$/;

	function kameleon_print_window()
	{
		print_window.print();
	}

	function kameleon_print()
	{
		print_window=open('<?echo $uri?>','print','<?echo $print_params?>');

		<?php 
			if (strlen($print_html)) echo "print_window.document.writeln('$print_html');";
			else echo "setTimeout(kameleon_print_window,2000);"
		?>

	}

	function kameleon_favorite()
	{
		<?php echo $href_favorite; ?>
		
	}
	
	function kameleon_remark()
	{
		id='remark_<?echo $NAVI['sid']?>';
		obj=document.getElementById(id);

		if (obj.style.display == 'none')
		{
			obj.style.display='inline';
			obj.style.top=-1*obj.clientHeight-10;
			
		}
		else
			obj.style.display='none';
		

	}

	function kameleon_remark_form_submit(f,a)
	{
		if (f.k_remark_email.value.length==0) 
		{
			alert(a);
			f.k_remark_email.focus();
			return false;
		}
		

		if (!email_regex.test(f.k_remark_email.value))
		{
			alert(a);
			return false;
		}

		return true;
	}

	function kameleon_recommend_form_submit(f,a)
	{
		if (f.k_recommend_mailto.value.length==0) 
		{
			alert(a);
			f.k_recommend_mailto.focus();
			return false;
		}
		

		if (!email_regex.test(f.k_recommend_mailto.value))
		{
			alert(a);
			f.k_recommend_mailto.focus();
			return false;
		}

		if (f.k_recommend_mailfrom.value.length==0) 
		{
			alert(a);
			f.k_recommend_mailfrom.focus();
			return false;
		}
		

		if (!email_regex.test(f.k_recommend_mailfrom.value))
		{
			alert(a);
			f.k_recommend_mailfrom.focus();
			return false;
		}

		return true;
	}

	function kameleon_recommend()
	{
		id='recommend_<?echo $NAVI['sid']?>';
		obj=document.getElementById(id);

		if (obj.style.display == 'none')
		{
			obj.style.display='inline';
			obj.style.top=-1*obj.clientHeight-10;
			
		}
		else
			obj.style.display='none';
		
	}

</script>
