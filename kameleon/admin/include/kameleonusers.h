<?
	include ("include/users.h");
	include ("include/user.h");
?>
<form name=robota method=post>
  <input type=hidden name=action value="">
  <input type=hidden name=server value="">
  <input type=hidden name=SetLogin value="">
</form>

<script>
	function newuser()
	{
		user=prompt("<?echo label("Enter the name")?>","");
		if (user == null) return;
		document.robota.SetLogin.value=user;
		document.robota.action.value="adduser";
		document.robota.submit();
	}
	function deluser(user)
	{
		c=confirm("<?echo label("Are you sure to delete user")?>: "+user+" ?");
		if (!c) return;
		document.robota.SetLogin.value=user;
		document.robota.action.value="deluser";
		document.robota.submit();
	}
	function delright(serwer,nazwa)
	{
		c=confirm("<?echo label("Are you sure to revoke rights to")?> "+nazwa+" <?echo label("for")?> <?echo $login?> ?");
		if (!c) return;
		document.robota.server.value=serwer;
		document.robota.SetLogin.value="<?echo $login?>";
		document.robota.action.value="delright";
		document.robota.submit();
	}
</script>
