<?
if (!function_exists("haveRight"))
{
	function haveRight($obj,$user,$target = "")
	{
		global $KAMELEON_MODE, $SKLEP_SESSION, $db;
		global $projdb;
		$obj_array = $SKLEP_SESSION["obj_array"];
		$SKLEP_SESSION["obj_array"] = $obj_array;

		if (!strlen($obj_array["total_count"]))
		{
			$sql = "SELECT count(*) AS c FROM system_user WHERE su_parent IS NOT NULL";
			parse_str(ado_query2url($sql,true));
		}
		else $c=trim($obj_array["total_count"]);

		if (strlen($c) && $c < 2 || (is_array($obj_array) && $obj_array["total_count"] < 2 && strlen($obj_array["total_count"]))) 
		{
			$obj_array["total_count"] = $c;
			$SKLEP_SESSION["obj_array"] = $obj_array;
			return 1;	
		}
		
		$AUTH_PAGES = $SKLEP_SESSION["AUTH_PAGES"];
		
		if (!is_array($AUTH_PAGES))
		{
			$sql = "SELECT sao_klucz FROM system_acl_obiekt GROUP BY sao_klucz";
			$res = $projdb->execute($sql);
			$AUTH_PAGES = array();
			for ($i=0; $i < $res->RecordCount(); $i++)
			{
				parse_str(ado_explodename($res,$i));
				$AUTH_PAGES[] = trim($sao_klucz);
			}

			$SKLEP_SESSION["AUTH_PAGES"] = $AUTH_PAGES;
		}

		if (strlen($target))
			if (!in_array("p_".$target,$AUTH_PAGES))
				return 1;

		if (strlen($obj))
			if (!in_array($obj,$AUTH_PAGES))
				return 1;
		if (!strlen($obj) && !strlen($target))
			return 1;

		if ((!strlen($obj) && !strlen($target)) || !strlen($user))
		{
			$SKLEP_SESSION["obj_array"] = $obj_array;
			return 0;
		}

		if (is_array($obj_array) && count($obj_array))
			if (!strlen($target))
			{
				$SKLEP_SESSION["obj_array"] = $obj_array;
				if (in_array($obj,$obj_array))
					return 1;
				else
					return 0;
			}
			else
			{
				$SKLEP_SESSION["obj_array"] = $obj_array;
				if (in_array("p_".$target,$obj_array))
					return 1;
				else
				{
					if ($KAMELEON_MODE)
						echo "<a href=\"index.php?page=$target&seteditmode=1\">index.php?page=$target&seteditmode=1</a>";
					return 0;
				}
			}

		//+++++++++++++++++++++++++++++++++++++++++//
		//  WYCI¥GAMY ID GRUP DLA DANEGO USERA     //
		//+++++++++++++++++++++++++++++++++++++++++//
		$sql = "SELECT sag_grupa_id FROM system_acl_grupa
				WHERE sag_user_id = $user";
		$res = $projdb->execute($sql);
		$zbior_id = "";
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$zbior_id.= "$sag_grupa_id,";
		}
		$zbior_id = substr($zbior_id,0,-1);
		if (!strlen($zbior_id))	return 0;

		//+++++++++++++++++++++++++++++++++++++++++++++++++++//
		//  WYCI¥GAMY KLUCZE OBIEKTOW DLA ZNALEZIONYCH GRUP  //
		//+++++++++++++++++++++++++++++++++++++++++++++++++++//
		$sql = "SELECT sao_klucz FROM system_acl_obiekt
				WHERE sao_grupa_id IN ($zbior_id)";
		$res = $projdb->execute($sql);
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$obj_array[] = $sao_klucz;
		}

		if (count($obj_array))
			if (!strlen($target))
			{
				$SKLEP_SESSION["obj_array"] = $obj_array;
				if (in_array($obj,$obj_array))
				{
					return 1;
				}
				else
				{
					return 0;
				}
			}
			else
			{
				$SKLEP_SESSION["obj_array"] = $obj_array;
				if (in_array('p_'.$target,$obj_array))
				{
					return 1;
				}
				else
				{
					if ($KAMELEON_MODE)
						echo "<a href=\"index.php?page=$target&seteditmode=1\">index.php?page=$target&seteditmode=1</a>";
						return 0;
				}
			}

		$SKLEP_SESSION["obj_array"] = $obj_array;
		return 0;
	}

}	
?>
