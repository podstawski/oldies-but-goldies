<?

	function crm_file($page_id)
	{
		global $SERVER_ID;

		$page_id+=0;
		$query="SELECT file_id FROM crm_page 
			 WHERE server=$SERVER_ID 
			 AND page_id=$page_id
			 LIMIT 1";
		parse_str(ado_query2url($query));

		return $file_id;
	}

	function customer_id_on_page($page_id)
	{
		global $SERVER_ID;

		$page_id+=0;
		$query="SELECT c_id FROM crm_customer
			 WHERE c_server=$SERVER_ID 
			 AND c_page_id=$page_id
			 LIMIT 1";
		parse_str(ado_query2url($query));
		return ($c_id+0);
	}

	function process_id_on_page($page_id)
	{
		global $SERVER_ID;

		$page_id+=0;
		$query="SELECT p_id FROM crm_proc
			 WHERE p_server=$SERVER_ID 
			 AND p_page_id=$page_id
			 LIMIT 1";
		parse_str(ado_query2url($query));
		return ($p_id+0);
	}

	function obj_id_on_page($page_id)
	{
		global $SERVER_ID;

		$page_id+=0;
		$query="SELECT id FROM crm_page
			 WHERE server=$SERVER_ID 
			 AND page_id=$page_id
			 LIMIT 1";
		parse_str(ado_query2url($query));
		return ($id+0);
	}


	function crm_calendar()
	{
		global $INCLUDE_PATH;
		include_once("$INCLUDE_PATH/calendar.h");
	}


	function explode_path($path,$result_as_key=false)
	{
		$wynik="";
		$path=explode(":",$path);

		if (is_Dir($path[0]))
		{
			$handle=opendir($path[0]);
			$dn=$path[0];
			while (($file = readdir($handle)) !== false) 
			{
				if ($file=="." || $file=="..") continue;
				if (is_dir("$dn/$file"))
				{
					$path[]="$dn/$file";
					continue;
				}
				if ($result_as_key) $wynik["$dn/$file"]="";	
				else $wynik[]="$dn/$file";
			}
			closedir($handle);
		}

		for ($i=1;$i<count($path);$i++ )
		{
			$w=explode_path($path[$i],$result_as_key);
			if (is_array($w)) $wynik=array_merge($wynik,$w);

		}

		return($wynik);
	}


	function crm_users($selected_user="",$include="",$exclude="")
	{
		global $adodb,$SERVER_ID;

		$query="SELECT passwd.username AS crm_user,fullname 
			 FROM rights,passwd 
			 WHERE server=$SERVER_ID
			 AND passwd.username=rights.username";

		if (strlen($include)) 
		{
			$include=ereg_replace(",","','","'$include'");
			$query.=" AND passwd.username IN ($include)";
		}
		if (strlen($exclude)) 
		{
			$exclude=ereg_replace(",","','","'$exclude'");
			$query.=" AND passwd.username NOT IN ($exclude)";
		}

		$query.=" ORDER BY passwd.username";


		$user_res=$adodb->Execute($query);
		$user_count=$user_res->RecordCount();
		$crm_users="";
		for ($u=0;$u<$user_count;$u++)
		{
			parse_str(ado_ExplodeName($user_res,$u));
			$sel=($crm_user==$selected_user)?" selected":"";
			if (!strlen($fullname)) $fullname=$crm_user;
			$crm_users.="<option$sel value=\"$crm_user\">$fullname</option>\n";
		}

		return $crm_users;
		
	}


	function crm_toolbar($obj,$self,$inactive=null)
	{
		global $const_crm_toolbar_counter;

		$const_crm_toolbar_counter+=0;

		$wynik="<table cellspacing=2 cellpadding=3><tr>";
		reset($obj->icon);
		while ( list( $key, $icon ) = each( $obj->icon ) )
		{
			$imgbase=kameleon_global($icon->imgbase);
			$img=kameleon_global($icon->img);
			$imga=kameleon_global($icon->imga);
			$label=kameleon_global($icon->label);
			$alt=addslashes($label);

			$link=$self;
			$link.=strstr($self,"?")?"&":"?";
			
			if ($inactive[$key]) continue;

			reset($icon->var);
			while ( list( $var_key, $var_val ) = each( $icon->var ) )
			{
				$link.=$var_key."=".urlencode(kameleon_global($var_val))."&";
			}
		

			$mouse="onMouseOver=\"this.style.borderColor=this.aColor\"
				onMouseOut=\"this.style.borderColor=this.nColor\"";


			$const_crm_toolbar_counter++;
			$id="_toolbar_$const_crm_toolbar_counter";
			$wynik.="<td id=\"$id\" nowrap
				style=\"border-style:solid; border-width:1px; \" $mouse>";

			

			if (!$inactive[$key]) $wynik.="<a href=\"$link\">";
			if (file_exists("$imgbase/$img") && strlen($img)) 
				$wynik.="<img alt=\"$alt\" src=\"$imgbase/$img\" border=\"0\">";
			else
				$wynik.=$label;
			if (!$inactive[$key]) $wynik.="</a>";

			$wynik.="</td>";
			$wynik.="<script>
					document.all['$id'].style.borderColor=obj_bgcolor(document.all['$id']);
					document.all['$id'].nColor=document.all['$id'].style.borderColor;
					document.all['$id'].aColor=obj_acolor(document.all['$id'].nColor);			
				</script>";
		}
		
		$wynik.="</tr></table>";

		return ($wynik);
	}



?>
