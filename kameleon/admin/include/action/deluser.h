<?
	$action="";

	$query="
		DELETE FROM passwd WHERE username='$SetLogin';
		DELETE FROM rights WHERE username='$SetLogin';
		DELETE FROM login WHERE username='$SetLogin'; 
		DELETE FROM login_arch WHERE username='$SetLogin'; ";

//	echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;

	unset($SetLogin);
	unset($SetGroup);
	unset($login);
	unset($groupid);

?>