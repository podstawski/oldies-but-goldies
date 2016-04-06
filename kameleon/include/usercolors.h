<?
	function addcolor($color)
	{
		global $USERCOLORS;

		$color=strtoupper($color);

		if (strlen($color)!=7) return;

		for ($i=0;is_array($USERCOLORS) && $i<count($USERCOLORS);$i++)
			if ($USERCOLORS[$i]==$color) return;
		
		$USERCOLORS[]=$color;
		
	}

	$query="SELECT wart FROM class WHERE server=$SERVER_ID
		 AND pole LIKE '%color%'";
	$kolory=ado_ObjectArray($adodb,$query);

	for ($i=0;is_array($kolory) && $i<count($kolory);$i++)
		addcolor($kolory[$i]->wart);

	$query="SELECT fgcolor,bgcolor,tbgcolor,tfgcolor 
			FROM webpage 
			WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang'
			GROUP BY fgcolor,bgcolor,tbgcolor,tfgcolor";
	$kolory=ado_ObjectArray($adodb,$query);

	for ($i=0;is_array($kolory) && $i<count($kolory);$i++)
	{
		addcolor("#".$kolory[$i]->bgcolor);
		addcolor("#".$kolory[$i]->fgcolor);
		addcolor("#".$kolory[$i]->fgcolor);
		addcolor("#".$kolory[$i]->tfgcolor);
	}

	$query="SELECT fgcolor FROM weblink
			WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang'
			GROUP BY fgcolor";
	$kolory=ado_ObjectArray($adodb,$query);

	for ($i=0;is_array($kolory) && $i<count($kolory);$i++)
		addcolor("#".$kolory[$i]->fgcolor);

	$query="SELECT bgcolor FROM webtd
			WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang'
			GROUP BY bgcolor";
	$kolory=ado_ObjectArray($adodb,$query);

	for ($i=0;is_array($kolory) && $i<count($kolory);$i++)
		addcolor("#".$kolory[$i]->bgcolor);


	return;

	for ($i=0;is_array($USERCOLORS) && $i<count($USERCOLORS);$i++)
		echo $USERCOLORS[$i]."<br>"; 

?>