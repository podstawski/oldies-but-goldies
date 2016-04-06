<?
	//print_r($CALENDAR);

	$costxt="<xml>\n";
	while ( is_Array($CALENDAR) && list($k,$v)=each($CALENDAR) )
	{
		$costxt.="	<$k>$v</$k>\n";	
	}
	$costxt.="</xml>";


?>