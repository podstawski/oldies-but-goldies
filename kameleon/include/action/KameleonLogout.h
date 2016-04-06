<?php
	if (is_array($_COOKIE)) foreach ( array_keys($_COOKIE) AS $k ) SetCookie($k,"");
	Header("Location: $SCRIPT_NAME");
	$adodb->session_destroy();
	$adodb->close();

	if (is_object($auth_acl)) $auth_acl->logout();
	exit();
