<html>
<head>
    <title>KAMELEON: <? echo label("Users"); ?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type"
	content="text/html; charset=<?echo $CHARSET?>">
</head>

<? if (!$ACL_RIGHTS) return; ?>

<body topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 
	onselectstart="return false;" bgcolor="#c0c0c0">



<table border=1 cellspacing=0 cellpadding=0 width=100% bgcolor=silver><tr><td>
<?
$new_user = "<a href='javascript:NewKameleonUser()' class=k_a>
			<img src=img/i_new_n.gif onmouseover=\"this.src='img/i_new_a.gif'\" onmouseout=\"this.src='img/i_new_n.gif'\" border=0 alt='".label("Add new user")."' width=23 height=22></a>";


echo $new_user;
?>
</td></tr></table>


<table border=1  cellspacing=0 cellpadding=3 style='margin-top: 5px' align='center' width='95%'>
<tr >
	<td class='k_formtitle' width='20px'>
		<? echo label("Username")?>
	</td>
	<td class='k_formtitle' width='115px'>
		<? echo label("Password")?>
	</td>

	<td class='k_formtitle'>
		<? echo label("Rights also inherited from")?>
	</td>

	<td class='k_formtitle'>
		<? echo label("Options")?>
	</td>

</tr>

<?
	$query="SELECT * FROM kameleon_acl_users WHERE kau_server=$SERVER_ID
			ORDER BY kau_username";

	$res=$adodb->Execute($query);
	for ($i=0;$i<$res->RecordCount();$i++ )
	{
		parse_str(ado_explodeName($res,$i));

		echo "<tr>";
		echo "<td class='k_text' id='u_$kau_username'  valign=top>
			<span style='font: bold; cursor:Move' onmousedown=\"MoveUser('$kau_username')\">
				$kau_username
			</span>	</td>";

		echo "<td class='k_text' id='p_$kau_username' valign=top> 
			<span style='cursor:hand' onclick='InsertInput(parentNode)'>";
		echo strlen($kau_password)?ereg_replace(".","*",$kau_password):"<font color='red'>".label("no password")."</font>";
		echo "</span></td>";

		echo "<td valign=top class='k_text'>";
		
		$first=1;
		foreach (explode(":",$kau_inherits) AS $group)
		{
			if (!strlen($group)) continue;

			if (!$first) echo ", ";
			$first=0;
			echo "<a href=\"javascript:UnsubscribeUserFromGroup('$kau_username','$group')\">";
			echo $group;
			echo "</a>";

		}

		echo "&nbsp;</td>";


	    	echo "<td valign=top>";
			
		if (strlen($callback_fun))
			echo "<img src='img/i_file_n.gif'
				onClick=\"top.opener.${callback_fun}('$kau_username'); window.close()\" 
				style='cursor:hand;' border=0 width=23 height=22
				onmouseover=\"this.src='img/i_file_a.gif'\" 
				onmouseout=\"this.src='img/i_file_n.gif'\" 
				alt='".label("Include user in the access list")."'>";

		echo "<img src='img/i_delete_n.gif' onclick=\"DelKameleonUser('$kau_username')\"
				onmouseover=\"this.src='img/i_delete_a.gif'\" onmouseout=\"this.src='img/i_delete_n.gif'\"  
				border=0 width=23 height=22 alt='".label("Delete user")." $kau_username'>
			</td>";

		echo "</tr>";
	}

?>
</table>

<div style="visibility:hidden;" id="inp_pass">
	<input class='k_input' type='password' onblur="KameleonUserChpass(parentNode.id,this.value)">
</div>

<div style="visibility:hidden; font:bold; position:absolute" id="drag_div" class="k_text" >
</div>

<form method=post action="<?echo $SCRIPT_NAME ?>" name="users" >
	<input type=hidden name=action value="">
	<input type=hidden name=callback_fun value="<?echo $callback_fun?>">
	<input type=hidden name=K_USERS[name] id=name value="">
	<input type=hidden name=K_USERS[pass] id=pass value="">
	<input type=hidden name=K_USERS[group] id=group value="">
</form>

<script>

function KameleonUserChpass(user,pass)
{
	user=user.substr(2);

	document.users.name.value=user;
	document.users.pass.value=pass;
	document.users.action.value="ZmienHasloUsera";
	document.users.submit();
}

function NewKameleonUser()
{
	u=prompt("<?echo label("Issue username")?>","");
	if (!u) return;
	if (!u.length) return;
	
	document.users.name.value=u;
	document.users.action.value="DadajUsera";
	document.users.submit();
}

function InsertInput(obj)
{
	obj.innerHTML=document.all.inp_pass.innerHTML; 
	o=obj.childNodes(0);
	o.focus();

}
function DelKameleonUser(user)
{
	c=confirm("<?echo label("Are you sure to delete user")?> "+user+" ?" );
	if (!c) return;

	document.users.name.value=user;
	document.users.action.value="UsunUsera";
	document.users.submit();
}

function UserMovement()
{
	kameleon_drag_obj.style.posLeft=event.clientX-10;
	kameleon_drag_obj.style.posTop=event.clientY-15;
}

function MoveUser(username)
{

	document.all.drag_div.username=username;
	document.all.drag_div.innerHTML="+ "+event.srcElement.innerHTML;
	document.all.drag_div.style.visibility="visible";
	document.all.drag_div.style.position="absolute";

	kameleon_drag_obj=document.all.drag_div;

	event.srcElement.style.cursor="Normal";

	UserMovement();

	document.onmousemove=UserMovement;
	document.onmouseup=SubscribeUserToGroup;

	return false;
}

function SubscribeUserToGroup()
{
	document.onmousemove=null;
	document.onmouseup=null;
	document.all.drag_div.style.visibility="hidden";

	document.users.name.value=document.all.drag_div.username;
	document.users.group.value=event.srcElement.id.substr(2);
	document.users.action.value="ZapiszUseraDoGrupy";
	document.users.submit();

}

function UnsubscribeUserFromGroup(user,group)
{
	document.users.name.value=user;
	document.users.group.value=group;

	document.users.action.value="WypiszUseraZGrupy";
	document.users.submit();
}

</script>

</body>
</html>
