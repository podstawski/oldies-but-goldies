<?

if (!function_exists("system_auth_user"))
{
	function system_auth_user($obj, $db=null)
	{
		global $SYSTEM;
		global $projdb;

		$CAUTH = $obj;
		
		$pole = "su_login";
		if ($SYSTEM[auth]=="email") 
			$pole = "su_email";
		$sql = "SELECT su_pass, su_id FROM system_user WHERE $pole = '$CAUTH[user]' AND (su_parent IS NOT NULL OR su_parent > 0)";
	
		$su_pass = "";
		parse_str(ado_query2url($sql));

		if (!strlen($su_pass) || ($su_pass != $CAUTH[password] && $su_pass != crypt($CAUTH[password],$su_pass)))
			return 0;
		else
			return $su_id;		
	}
}
if (!function_exists("system_user_priviliges"))
{
	function system_user_priviliges($id, $db=null)
	{
		global $projdb;
		if (!strlen($id)) return 0;
		$priviliges = array("p_admin","p_price","p_order");
		$ret = array();
		$sql = "SELECT sg_admin FROM system_grupa, system_acl_grupa
				WHERE sag_user_id = $id AND sg_id = sag_grupa_id";
		$res = $projdb->execute($sql);
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			for ($k=0; $k < count($priviliges); $k++)
				if (pow(2,$k) & $sg_admin)
				{
					$akey = $priviliges[$k];
					$ret[$akey] = 1;
				}								
		}
		return $ret;
	}
}
 
if (!function_exists("system_user_additional"))
{
	function system_user_additional($obj, $db=null)
	{
		//if ($obj[projekt]) return $obj; // ustawiam tylko raz na sesję

		global $SERVER_ID;
		
		$query="SELECT * FROM system_user WHERE su_id=$obj[id]";
		parse_str(ado_query2url($query));
		
		$obj[user]=$su_login;
		$obj[imiona]=$su_imiona;
		$obj[nazwisko]=$su_nazwisko;
		$obj[projekt]=$su_server;
		$obj[parent]=$su_parent;
		$obj[email]=$su_email;
		$obj[tel]=$su_telefon;
		$priv = system_user_priviliges($obj[id],$db);
		$obj[p_admin] = $priv[p_admin];
		$obj[p_price] = $priv[p_price];
		$obj[p_order] = $priv[p_order];
		$obj[blokady]=$su_blokady;
		$query="SELECT su_telefon, su_nazwisko, su_login, su_dostawa, su_platnosc, su_blokady,
			su_ulica,su_kod_pocztowy,su_miasto, su_nip FROM system_user WHERE su_id=$obj[parent]";
		parse_str(ado_query2url($query));
		$obj[nazwa]=$su_nazwisko;				
		$obj[kod]=$su_login;				
		$obj[dostawa]=$su_dostawa;				
		$obj[platnosc]=$su_platnosc;				
		$obj[blokady].=$su_blokady;
		$obj[tel]=$su_telefon;
		$obj[adres_dostawy]=$su_adres1;
		$obj[adres] = $su_ulica.", ".$su_kod_pocztowy." ".$su_miasto;
		$obj[nip] = $su_nip;
		return $obj;
	}
}

?>
