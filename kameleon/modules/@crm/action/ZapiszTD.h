<?
	push($INCLUDE_PATH,$html);

	global $HTTP_POST_VARS,$HTTP_GET_VARS;
	global $MODULES,$KAMELEON;
	$__a=array_merge($HTTP_POST_VARS,$HTTP_GET_VARS);
	while (is_array($__a) && list($k,$v)=each($__a)) eval("\$$k = \$v;");

	if (file_exists("include/action/$action.h")) include("include/action/$action.h");

	pop(&$INCLUDE_PATH,&$html);

	//$action="";
?>