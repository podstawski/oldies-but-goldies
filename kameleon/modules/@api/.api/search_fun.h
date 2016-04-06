<?
	function search_tree($page,$wyniki=null)
	{
		global $SERVER_ID,$ver,$lang;
		global $adodb;


		$wynik=array();
		if (!is_array($wyniki)) $wyniki=array($page);

		$query="SELECT id FROM webpage 
				WHERE server=$SERVER_ID AND prev=$page
				AND lang='$lang' AND ver=$ver";
		$res=$adodb->Execute($query);

		for ($i=0; $i<$res->RecordCount(); $i++)
		{
			parse_str(ado_ExplodeName($res,$i));
			if (!in_array($id,$wyniki)) $wynik[]=$id;
		}

		foreach ($wynik AS $page)
		{

			$sub=search_tree($page,array_merge($wyniki,$wynik));
			if (count($sub)) $wyniki=array_merge($wyniki,$sub);
		}



		return array_unique(array_merge($wynik,$wyniki));
	}

?>
