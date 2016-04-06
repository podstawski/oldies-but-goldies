<?
	include ("include/groups.h");
	include ("include/group.h");
?>
<form name=nowagrupa method=post>
  <input type=hidden name=action value=addgroup>
  <input type=hidden name=nazwa value="">
</form>

<form name=usungroup method=post>
  <input type=hidden name=action value=delgroup>
  <input type=hidden name=gid value="">
</form>

<script>
	function newgroup()
	{
		user=prompt("<?echo label("Input group name")?>","");
		if (user == null) return;
		document.nowagrupa.nazwa.value=user;
		document.nowagrupa.submit();
	}
	function delgroup(groupid)
	{
		c=confirm("<?echo label("Do you realy want to delete this group ?")?>");
		if (!c) return;
		document.usungroup.gid.value=groupid;
		document.usungroup.submit();
	}
</script>