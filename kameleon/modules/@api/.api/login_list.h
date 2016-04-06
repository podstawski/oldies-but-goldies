<?
	error_reporting(7);	

	include_once("include/search.h");

	if (!$size) $size=25;

	if ($xml->email2)
	{
		$EMAIL2_warunek="AND c_email2='$xml->email2'";
	}

	if (!$ile)
	{
		$query="SELECT count(*) AS ile FROM crm_customer WHERE c_server=$SERVER_ID $EMAIL2_warunek";
		parse_str(ado_query2url($query));
	}

	

	$start+=0;

	$navi=naviIndex("index.php?page=$page",$start,0,$ile,$size);


	$PARENT_warunek=$cokolwiek ? "c_parent=".(0+$AUTH[c_id]) : "(c_parent=0 OR c_parent IS NULL)";


	$query="SELECT *,oid FROM crm_customer 
			WHERE c_server=$SERVER_ID $EMAIL2_warunek
			AND $PARENT_warunek
			ORDER BY c_username,c_email,c_id
			LIMIT $size
			OFFSET $start
			";

	//echo nl2br($query);

	$users=ado_ObjectArray($adodb,$query);

	echo $navi;

	$imgpath="$CMS_API_HOST/img";
?>


<img src="<?echo $imgpath?>/i_new_n.gif"
	onMouseOver="this.src='<?echo $imgpath?>/i_new_a.gif'"
	onMouseOut="this.src='<?echo $imgpath?>/i_new_n.gif'"
	style="cursor:hand"
	onClick="api2_adduser()" >

<hr size=1>
<table cellpadding=3 cellspacing=0 class="api2_auth_table" width="100%">
<?
	$style="style=\"cursor:hand;\"";


	$mouse="onMouseOver=\"parentNode.className='api_auth_list_a'\" 
			onMouseOut=\"parentNode.className='api_auth_list_n'\"";

	$delete="<img src=\"$imgpath/i_delete_min_n.gif\" border=0
			onMouseOver=\"this.src='$imgpath/i_delete_min_a.gif'\"
			onMouseOut=\"this.src='$imgpath/i_delete_min_n.gif'\" >";

	for ($i=0;$i<count($users) && is_Array($users);$i++ )
	{
		$U=$users[$i];

		$onclick="onClick=\"api2_loginasuser('$U->c_username','$U->c_email','$U->c_password')\"";
		$del=base64_encode($U->c_id.":".$U->oid);
		
		echo "<tr>";
		
		if (strlen($U->c_username)) echo "
					<td $style $mouse $onclick>
						$U->c_username
					</td>";

		if (strlen($U->c_email)) echo "
					<td $style $mouse nowrap $onclick>
						$U->c_email
					</td>";
		echo "
					<td $style $mouse nowrap $onclick>
						$U->c_name
					</td>
					<td onClick=\"api2_deluser('$del')\" $style $mouse align=\"right\">
						$delete
					</td>
			</tr>";
	}


	$newpass=uniqid("");

	$prompt=label("Submit the login");
	$sure=label("Are you sure to delete user");
	

	
	$selfuj="";
	if ($cokolwiek)	$selfuj = "document.api2_adminlogin_form.action='$more';";

	$JScript.="
			function api2_loginasuser(u,e,p)
			{
				document.all._login_username.value=u;
				if (!u.length) document.all._login_username.value=e;
				document.all._login_c_email.value=e;
				document.all._login_password.value=p;
				$selfuj
				document.api2_adminlogin_form.submit();
			}
			function api2_adduser()
			{
			
				u=prompt('$prompt','');
				if (!u) return;
				if (!u.length) return;

				if (u.search('@')>0)
					document.all._login_c_email.value=u;
				else
					document.all._login_username.value=u;

				document.all._login_password.value='$newpass';
				document.all._login_action.value='NewCustomer';

				document.api2_adminlogin_form.action='$sefl';
				document.all._login_action.name='action';

				document.api2_adminlogin_form.submit();
			}

			function api2_deluser(_id)
			{
				if (!confirm('$sure ?')) return;

				document.all._login_action.value='DelCustomer';
				document.api2_adminlogin_form.action='$sefl';
				document.all._login_action.name='action';
				document.all._login_id.value=_id;

				document.api2_adminlogin_form.submit();
			}
			";

?>
</table>

<form method="post" name="api2_adminlogin_form" action="<?echo $next?>">
	<input type="hidden" name="ACTION_VAR" value="AUTH">
	<input type="hidden" name="AUTH[id]" id="_login_id">
	<input type="hidden" name="AUTH[parent]" 
		value="<? 
				$parent=$AUTH[c_parent];
				if (!$parent) $parent=$AUTH[c_id];
				if ($cokolwiek) echo $parent;
				?>">
	<input type="hidden" name="AUTH[username]" id="_login_username">
	<input type="hidden" name="AUTH[password]" id="_login_password">
	<input type="hidden" name="AUTH[c_email]" id="_login_c_email">
	<input type="hidden" name="_action" id="_login_action">
	<input type="hidden" name="AUTH[email2]" value="<? echo $xml->email2 ?>">
</form>

<?

	echo $navi;
?>