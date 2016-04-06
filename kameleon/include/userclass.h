<?
	$USERCLASS[]=array("",label("Choose"));

	$query="SELECT DISTINCT(nazwa) FROM class WHERE server=$SERVER_ID
		 AND nazwa LIKE '\\.%' AND nazwa NOT LIKE '%:%'
		 AND nazwa NOT LIKE '\\.api_%'
		 AND ver=$ver
		 ORDER BY nazwa";
	$style=ado_ObjectArray($adodb,$query);


	for ($i=0;is_array($style) && $i<count($style);$i++)
		$USERCLASS[]=array(substr($style[$i]->nazwa,1),substr($style[$i]->nazwa,1));
?>
