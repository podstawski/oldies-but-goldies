<?php

		
		function acl_users_rights($oid,$oname)
		{
			global $adodb,$SERVER_ID;
			global $_kameleon_debug;

			$wynik=array();


			$query="SELECT * FROM kameleon_acl,kameleon_acl_users
					WHERE ka_server=$SERVER_ID AND kau_server=$SERVER_ID
					AND ka_username=kau_username
					AND ka_oid=$oid AND ka_resource_name='$oname'";

			$res=$adodb->Execute($query);

			if (!$res->RecordCount()) return $wynik;
			
			for ($i=0;$i<$res->RecordCount();$i++)
			{
				parse_str(ado_ExplodeName($res,$i));
				
				if (strlen($kau_password)) $hard_rights[$kau_username]=array($kau_password,$ka_rights);
				$groups[$kau_username]=$ka_rights;
			}

			while(1)
			{
				$start_group_count=count($groups);

							
				reset($groups);
				while (list($g_name,$g_rights)=each($groups))
				{
					$query="SELECT * FROM kameleon_acl_users
						 WHERE kau_server=$SERVER_ID
						 AND kau_inherits~':$g_name:'";
					$res=$adodb->Execute($query);

					for ($i=0;$i<$res->RecordCount();$i++)
					{
						parse_str(ado_ExplodeName($res,$i));
						if (strlen($kau_password)) 
							$wynik[$kau_username]=array($kau_password,$g_rights);
						$groups[$kau_username]=$ka_rights;
					}
				}
				if ($start_group_count == count($groups)) break;
			}

			while ( is_array($hard_rights) && list($user,$passwd)=each($hard_rights)) $wynik[$user]=$passwd;


			return $wynik;
		}

		function acl2db($acl,$key_pattern,$filename,$separator=":")
		{
			global $page,$lang,$ver,$SERVER_ID;
			
			if (!function_exists("dbmopen")) return false;

			$dbm=@dbmopen($filename,"c");
			if (!$dbm) return false;
			if (!is_array($acl)) return;

			while(list($key,$users)=each($acl))
			{
				eval("\$k = \"$key_pattern\" ;");
				dbmreplace ($dbm, $k, "1");
				while(list($user,$passwd)=each($users))
				{
					$value=base64_encode($passwd[1].$separator.$passwd[0]);
					dbmreplace ($dbm, "$k$separator$user", $value);
				}
			}

			dbmclose($dbm);

		}
