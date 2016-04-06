<form name="trashall" method="post" action="servers.php">
  <input type="hidden" name="action" value="trashservers">
  <input type="hidden" name="SetGroup" value="-1">

<?

	$query="SELECT servers.nazwa,groupname,groups.id AS groupid,servers.id,
					sum(tout-tin)/3600 AS godz,min(tin) AS first,max(tin) AS last			
		FROM servers,groups,login_all
		WHERE groups.id=servers.groupid
		AND login_all.server=servers.id
		GROUP BY groupname,servers.nazwa,servers.id,groups.id;";


	$res=$adodb->Execute($query);
	

	echo "
  <div class=\"km_toolbar\"></div>
  <table class=\"tabelka\" cellspacing=\"0\" cellpadding=\"0\">\n";	

	$bgcolors=array("#D0D0D0","#E0E0E0");

	for ($i=0;$i<$res->RecordCount() || $SubQueryInProgress;$i++)
	{

		if ($i==$res->RecordCount() && $SubQueryInProgress)
		{		
			pop(&$i,&$res);
			$SubQueryInProgress=0;	
			continue;
		}

		parse_str(ado_ExplodeName($res,$i));


		if ($oldgroupname!=$groupname || $i==$res->RecordCount()-1 )
		{
			if ($oldgroupname==$groupname) $oldgroupid=$groupid;

			if ($oldgroupid && !$subqueried[$oldgroupid])
			{
				$query="SELECT servers.nazwa,servers.id, 0 AS godz,0 AS first,0 AS last,
						'$oldgroupname' AS groupname,groupid
					FROM servers
					WHERE groupid=$oldgroupid
					AND id NOT IN (SELECT server FROM login_all WHERE server=servers.id)
					ORDER BY servers.nazwa";

				
				$subres=$adodb->Execute($query);

				if ($subres->RecordCount() )
				{	
					push($i-1,&$res);
					$i=-1;
					$res=$subres;
					$SubQueryInProgress=1;	
					$subqueried[$oldgroupid]=1;
					continue;
				}
	
			}

		}


		$godz=round($godz*100)/100;
		$in=date("d-m-Y",$first);
		$out=date("d-m-Y",$last);
		
		if (!$first) $in="-";
		if (!$last) $out="-";

		$query="SELECT username,nexpire,nexpire<".time()." AS expired 
				FROM rights WHERE server=$id
				ORDER BY username";
		$resu=$adodb->Execute($query);
		$users=$resu->RecordCount().": ";
		$please_delete=" <font color=Red>".label("Delete")." !!!";
		for ($u=0;$u<$resu->RecordCount();$u++)
		{
			parse_str(ado_ExplodeName($resu,$u));


			if ($users[strlen($users)-2]!=":") $users.=", ";
			
			$disabled=($nexpire && $expired=="t")?"disabled":"";
			if (!strlen($disabled)) $please_delete="";

			$users.="<a $disabled style=\"text-decoration:none\" 
						href=\"kameleonusers.php?SetLogin=$username&SetGroup=$groupid#$nazwa\">";
			$users.=htmlspecialchars($username);
			if ($nexpire) $users.=" (".FormatujDate($nexpire).")";
			$users.="</a>";
			
		}
		
		if ($oldgroupname!=$groupname )
		{
			$colorcount++;
			$colorcount=($colorcount%2);
			$bgcolor="line_".$colorcount;
			$lp1=0;

		}
		$oldgroupname=$groupname;
		$oldgroupid=$groupid;

		
		echo "<tr class=\"$bgcolor\">\n";
		
		$href=strlen($please_delete)?"servers.php?SetGroup=$groupid&nazwa=$nazwa#$nazwa":"server.php?server=$id&SetGroup=$groupid";

		$lp++;
		$lp1++;
		echo "	<td>$lp.</td>\n";
		echo "	<td><a href=\"$href\" style=\"text-decoration:none\" >$nazwa$please_delete</a></td>\n";
		echo "	<td nowrap>$lp1. $groupname</td>\n";
		echo "	<td>".$godz."</td>\n";
		echo "	<td nowrap>".$in."</td>\n";
		echo "	<td nowrap>".$out."</td>\n";
		echo "	<td>".$users."&nbsp;</td>\n";

		$delchbx=strlen($please_delete)?"<input type=\"checkbox\" value=1 name=\"del_list[$id]\">":"&nbsp;";

		echo "	<td nowrap>$delchbx</td>\n";

		echo "</tr>\n";
	}

	$query="SELECT username,groupid FROM passwd WHERE username NOT IN 
			(SELECT username FROM rights WHERE username=passwd.username)
			ORDER BY username";
	$resu=$adodb->Execute($query);

	if ($resu->RecordCount())
	{
		$colorcount++;
		$colorcount=($colorcount%2);
		$bgcolor="line_".$colorcount;
		echo "<tr class=\"$bgcolor\">\n";

		echo "<td colspan=\"6\">&nbsp;</td><td>";
	
		for ($u=0;$u<$resu->RecordCount();$u++)
		{
			parse_str(ado_ExplodeName($resu,$u));
			if ($u) echo ", ";
			echo "<a style=\"text-decoration:none\" 
				href=\"kameleonusers.php?SetLogin=$username&SetGroup=$groupid\">
				$username</a>";
			$trash_users.="$username:";
		}
		echo "</td>";
		echo "<td><input type=\"checkbox\" value=\"$trash_users\" name=\"del_users\"></td>";

		echo "</tr>\n";
	}

	$colorcount++;
	$colorcount=($colorcount%2);
	$bgcolor="line_".$colorcount;
	echo "<tr class=\"$bgcolor\">\n";
	echo "<td colspan=6>&nbsp;</td>";
	echo "<td colspan=2>
		<input class=\"k_button\" type=\"submit\" value=\"".label("Trash selected servers or users")."\">
		</td>";
	echo "</tr>\n";
	echo "</table>\n";
?>
</form>