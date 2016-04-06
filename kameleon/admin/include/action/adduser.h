<?
	$action="";

	$login=$SetLogin;
	if (!strlen($login) || !strlen($grupa)) return;
	if ($login=="kameleon")
		$error=label("Username not allowed");
	if (strlen($error)) return;

	$query="SELECT count(*) AS c FROM passwd WHERE username='$login'";
	parse_str(ado_query2url($query));
	if ($c) $error=label("Username already created. Choose another username !");
	if ($c) return;	
	$query="INSERT INTO passwd
		 (username,password,groupid)
		  VALUES
		 ('$login','***ijshdjkfhskdfjk***',$grupa)";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;
?>