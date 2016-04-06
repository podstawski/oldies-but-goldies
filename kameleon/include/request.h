<?
	// objescie reggister_globals = Off

	foreach ( array_keys($_REQUEST) AS $k ) @eval("\$$k=\$_REQUEST[\"$k\"];");
	if ($KAMELEON_MODE) foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	
	
	if ($_REQUEST[api_req])
	{
		$API_REQUEST=unserialize(base64_decode($_REQUEST[api_req]));
		unset($_REQUEST[api_req]);
		if (is_array($API_REQUEST))
			//AM doda³em @ przed eval
			foreach ( array_keys($API_REQUEST) AS $k ) @eval("\$$k=\$API_REQUEST[\"$k\"];");
	}

	$ciastka="";
	if (method_exists($adodb,"getCookies")) $ciastka=$adodb->getCookies();
	if (is_array($ciastka)) foreach ( array_keys($ciastka) AS $k )
	{

		eval("\$$k=\$ciastka[\"$k\"];");
		$_COOKIE[$k]=$ciastka[$k];
		$_REQUEST[$k]=$ciastka[$k];
		$HTTP_COOKIE_VARS[$k]=$ciastka[$k];
	}
