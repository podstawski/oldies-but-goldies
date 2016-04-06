<?
if (!function_exists("haveRight2File"))
{
	function haveRight2File($filename, $user)
	{		
		if (!strlen($user) || !strlen($filename))
			return 0;

		global $_SESSION, $db, $KAMELEON_MODE, $SERVER_ID;	
		
		$filename = "a_".ereg_replace("/","_",$filename);		
		$filename = ereg_replace("att_","",$filename);

		$file_array = $_SESSION["file_array"];
		$_SESSION["file_array"] = $file_array;
/*
		$obj_array = $_SESSION["obj_array"];
		$_SESSION["obj_array"] = $obj_array;

		$CAUTH = $_SESSION["CAUTH"];
		$_SESSION["CAUTH"] = $CAUTH;
*/
		$sql = "SELECT count(*) AS c FROM system_user WHERE su_parent IS NOT NULL";
		parse_str(query2url($sql));

		if (strlen($c) && $c < 2) return 1;
		
		$AUTH_PAGES = $_SESSION["AUTH_PAGES"];

		if (!is_array($AUTH_PAGES))
		{
			$sql = "SELECT sao_klucz FROM system_acl_obiekt GROUP BY sao_klucz";
			$res = pg_exec($db,$sql);
			$AUTH_PAGES = array();
			for ($i=0; $i < pg_numrows($res); $i++)
			{
				parse_str(pg_explodename($res,$i));
				$AUTH_PAGES[] = trim($sao_klucz);
			}

			$_SESSION["AUTH_PAGES"] = $AUTH_PAGES;
		}
		
		if (!in_array($filename,$AUTH_PAGES))
			return 1;

		if (is_array($file_array) && count($file_array))
		{
			if (in_array($filename,$file_array))
				return 1;
			else
				return 0;
		}
		else
		{
			$sql = "SELECT sag_grupa_id FROM system_acl_grupa
					WHERE sag_user_id = $user";
			$res = pg_exec($db,$sql);
			$zbior_id = "";
			for ($i=0; $i < pg_Numrows($res); $i++)
			{
				parse_str(pg_explodename($res,$i));
				$zbior_id.= "$sag_grupa_id,";
			}
			$zbior_id = substr($zbior_id,0,-1);
			if (!strlen($zbior_id))	return 0;

			$sql = "SELECT sao_klucz FROM system_acl_obiekt
					WHERE sao_grupa_id IN ($zbior_id) AND sao_klucz LIKE 'a_%'";
			$res = pg_exec($db,$sql);
			$file_array = array();
			for ($i=0; $i < pg_Numrows($res); $i++)
			{
				parse_str(pg_explodename($res,$i));
				$file_array[] = $sao_klucz;
			}
			
			if (!count($file_array)) return 0;
			
			$_SESSION["file_array"] = $file_array;

			if (in_array($filename,$file_array))
				return 1;
			else
				return 0;
		}
	}
}

if (!function_exists("haveRight"))
{
	function haveRight($obj,$user,$target = "")
	{
		global $KAMELEON_MODE, $_SESSION, $db;
		
		$obj_array = $_SESSION["obj_array"];
		$_SESSION["obj_array"] = $obj_array;

		if (!strlen($obj_array["total_count"]))
		{
			$sql = "SELECT count(*) AS c FROM system_user WHERE su_parent IS NOT NULL";
			parse_str(query2url($sql));
		}
		else $c=trim($obj_array["total_count"]);
		
		if (strlen($c) && $c < 2 || (is_array($obj_array) && $obj_array["total_count"] < 2 && strlen($obj_array["total_count"]))) 
		{
			$obj_array["total_count"] = $c;
			$_SESSION["obj_array"] = $obj_array;
			return 1;	
		}
		
		$AUTH_PAGES = $_SESSION["AUTH_PAGES"];
		
		if (!is_array($AUTH_PAGES))
		{
			$sql = "SELECT sao_klucz FROM system_acl_obiekt GROUP BY sao_klucz";
			$res = pg_exec($db,$sql);
			$AUTH_PAGES = array();
			for ($i=0; $i < pg_numrows($res); $i++)
			{
				parse_str(pg_explodename($res,$i));
				$AUTH_PAGES[] = trim($sao_klucz);
			}

			$_SESSION["AUTH_PAGES"] = $AUTH_PAGES;
		}

		if (strlen($target))
			if (!in_array("p_".$target,$AUTH_PAGES))
				return 1;

		if (strlen($obj))
			if (!in_array($obj,$AUTH_PAGES))
				return 1;

		if ((!strlen($obj) && !strlen($target)) || !strlen($user))
		{
			$_SESSION["obj_array"] = $obj_array;
			return 0;
		}

		if (is_array($obj_array) && count($obj_array))
			if (!strlen($target))
			{
				$_SESSION["obj_array"] = $obj_array;
				if (in_array($obj,$obj_array))
					return 1;
				else
					return 0;
			}
			else
			{
				$_SESSION["obj_array"] = $obj_array;
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
		//  WYCIЅGAMY ID GRUP DLA DANEGO USERA     //
		//+++++++++++++++++++++++++++++++++++++++++//
		$sql = "SELECT sag_grupa_id FROM system_acl_grupa
				WHERE sag_user_id = $user";
		$res = pg_exec($db,$sql);
		$zbior_id = "";
		for ($i=0; $i < pg_Numrows($res); $i++)
		{
			parse_str(pg_explodename($res,$i));
			$zbior_id.= "$sag_grupa_id,";
		}
		$zbior_id = substr($zbior_id,0,-1);
		if (!strlen($zbior_id))	return 0;

		//+++++++++++++++++++++++++++++++++++++++++++++++++++//
		//  WYCIЅGAMY KLUCZE OBIEKTOW DLA ZNALEZIONYCH GRUP  //
		//+++++++++++++++++++++++++++++++++++++++++++++++++++//
		$sql = "SELECT sao_klucz FROM system_acl_obiekt
				WHERE sao_grupa_id IN ($zbior_id)";
		$res = pg_exec($db,$sql);
		for ($i=0; $i < pg_Numrows($res); $i++)
		{
			parse_str(pg_explodename($res,$i));
			$obj_array[] = $sao_klucz;
		}

		if (count($obj_array))
			if (!strlen($target))
			{
				$_SESSION["obj_array"] = $obj_array;
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
				$_SESSION["obj_array"] = $obj_array;
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

		$_SESSION["obj_array"] = $obj_array;
		return 0;
	}

}	
?>