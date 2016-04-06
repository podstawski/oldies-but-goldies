<?
	

	$action="";
	$change="";
	$f_password=trim($f_password);
   	if (strlen($f_password)>0)
   	{
		$p=crypt($f_password);
   		$change="password='$p',groupid=$id_grupy";
	}
   	else
	{
    	$change="groupid=$id_grupy";
	
	}

	if (strlen($svn_pass)) $change.=",svn_pass='$svn_pass'";


	
	$ulang=substr(trim(strtolower($ulang)),0,2);

  $admin = (int)$_POST["admin"];
	$change.=",admin=".$admin;
	if (!strlen($limit_time) ) $limit_time="0";
	eval("\$limit_time = 0 + $limit_time;");
	$lt=$limit_time*3600;
	$change.=",limit_time=$lt";

	$ulang=$_REQUEST['ulang'];
	$change.=",fullname='$f_fullname',email='$f_email',skin='$id_skin',ulang='$ulang'";
	$SetGroup=$grupa;
	$query="UPDATE passwd SET $change WHERE username='$login'";
	
//	echo nl2br($query);return;
//	$adodb->debug=1;


	if ($adodb->Execute($query)) logquery($query) ;


