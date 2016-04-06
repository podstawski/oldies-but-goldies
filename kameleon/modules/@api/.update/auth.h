<?
	$xml=obj2xml($AUTH);
	unset($AUTH);

	if (strstr($costxt,"<xml>")) $costxt="";

	$cos=$cos_auth_required?0:1;
?>