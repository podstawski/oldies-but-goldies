<?
	$xml="";

	
	$costxt="<xml>\n";
	while(is_array($SEARCH) && list($k,$v)=each($SEARCH))
	{
		$costxt.="	<$k>";
		$costxt.=htmlspecialchars($v);
		$costxt.="</$k>\n";
	}
	$costxt.="</xml>";

	unset($SEARCH);
?>
