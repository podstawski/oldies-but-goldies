<?
	global $CRMUSERS;
	$xml="";
	
	$costxt = "";
	if (is_array($CRMUSERS))
		while (list ($key, $val) = each ($CRMUSERS)) 
			$costxt.= ":$key=$val";

?>
