<?
	//print_R($AUTH);


	if (!strlen($AUTH[username]) && !strlen($AUTH[c_email]))
	{
		$error=label("One of email and username should be submited");
	}
	if (strlen($error)) return;


	$warunek=strlen($AUTH[username]) ? "c_username='$AUTH[username]'" : "c_email='$AUTH[c_email]'" ;

	$query="SELECT count(*) AS c FROM crm_customer 
		WHERE $warunek
		AND c_server=$SERVER_ID";
	parse_str(query2url($query));


	if ($c)
	{
		$error=label("Username exists")." ";
		$error.=strlen($AUTH[username])?$AUTH[username]:$AUTH[c_email];
	}

	if ( strlen($AUTH[c_email]) && !strpos($AUTH[c_email],"@") )
	{
		$error=label("Wrong email format");
	}


	if (strlen($error)) return;



	push ($INCLUDE_PATH);
	$INCLUDE_PATH="";
	include("include/modules.h");
	$INCLUDE_PATH=pop();	



	if (strlen($AUTH[username])) $AUTH[c_username]=$AUTH[username];
	if (strlen($AUTH[password])) $AUTH[c_password]=$AUTH[password];
	else 
	{
		$rnd=time()%4;
		$AUTH[c_password]=substr(uniqid(""),$rnd,4);
		$AUTH[password]=$AUTH[c_password];
	}
	if (strlen($AUTH[email2])) $AUTH[c_email2]=$AUTH[email2];


	$AUTH[c_id]="(NULL)";


	if (strlen($AUTH[username]))
	{
		$AUTH[token]="c_username";
	}
	else
	{
		$AUTH[token]="c_email";
		$AUTH[username]=$AUTH[c_email];
	}


	if ($AUTH[parent]) $AUTH[c_parent]=$AUTH[parent];


	//$adodb->debug=1;
	if (module_update($MODULES->api->files->login) )
	{
		if (!$AUTH[c_parent])
			$JScript=" 
				document.cookie = \"CAUTH[password]=$AUTH[password]\"; 
				document.cookie = \"CAUTH[username]=$AUTH[username]\";
				document.cookie = \"CAUTH[token]=$AUTH[token]\"; 	
			";
	}
?>
