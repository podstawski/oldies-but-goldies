<?
	$nazwa_org=$nazwa;

	//name=\"openserver\" 
	$grupysel= "<select class=\"km_select\"	onchange=\"document.location.href=this[this.selectedIndex].value\">";
	$query="SELECT id, groupname FROM groups ORDER BY groupname";
	$res=$adodb->Execute($query);
	
	$SetGroupSelected="";
	if (strlen($grupa)) $SetGroupSelected = "selected";
	$grupysel.= "<option class=k_select value='$SCRIPT_NAME?SetGroup=' $SetGroupSelected style=\"background-color : #E0E0E0;\">".label("Select group")."</opiton>";
	
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		if ($i) $grupy.= " - ";
		parse_str(ado_ExplodeName($res,$i));
		
		$SetGroupSelected="";
		if ($grupa==$id) 
		{
			$SetGroupSelected = "selected";
			$currentgroupname=$groupname;
		}
		$grupysel.= "<option class=k_select value='$SCRIPT_NAME?SetGroup=$id' $SetGroupSelected>$groupname</opiton>";
	}
	$SetGroupSelected="";
	if ($grupa==$CONST_TRASH) $SetGroupSelected = "selected";
	$grupysel.= "<option class=k_select value='$SCRIPT_NAME?SetGroup=$CONST_TRASH' $SetGroupSelected style=\"background-color : #FF0000; color: #FFFFFF;\">".label("Trash")."</opiton>";
	
	$grupysel.= "</select>";
?>
<div class="km_toolbar">
  <ul>
      <li class="km_label">
        <label><?=label("Select group")?></label>
        <?=$grupysel?>
      </li>
      <?	if (strlen($grupa))	{ ?>
      <li><a class="km_icon km_iconi_new" href="javascript:new_group()" title="<?=label("Create new server")?>"><?=label("Create new server")?></a></li>
      <? } ?>
  </ul>
</div>
<form action="server.php" method="post" name="newgroup">
	<input type="hidden" name="action" value="addserver">
	<input type="hidden" name="nazwa">
</form>

<form name="deleteall" method="post" action="<?echo "$SCRIPT_NAME"?>">
  <input type="hidden" name="action" value="delservers">
<?

	if (strlen($grupa))
	{
		$query =" SELECT nazwa,id  FROM servers ";
		$query.=" WHERE groupid=$grupa ";
		$query.=" ORDER BY nazwa";
	}
	else
	{
		$query =" SELECT nazwa,id  FROM servers ";
		$query.=" WHERE groupid<>$CONST_TRASH AND groupid NOT IN (SELECT id FROM groups) ";
		$query.=" OR groupid=NULL";
		$query.=" ORDER BY nazwa";
	}


	$res=$adodb->Execute($query);

	$colspan = ($grupa==$CONST_TRASH) ? 4 : 3;
	
	echo "
  <table cellspacing=\"0\" cellpadding=\"1\" class=\"tabelka\">\n";
	if ($res->RecordCount())
		echo "<tr>
				<th>".label('No.')."</th>
				<th colspan=\"5\">".label('Server name')."</th></tr>\n";
	else
		echo "<tr class=\"line_2\"><td colspan=\"6\">".label('There is no servers in group').": $currentgroupname</td></tr>";

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		
		$ser_bgcolor=" class=\"line_0\"";
		if (($i&1)==0) $ser_bgcolor=" class=\"line_1\"";

		$label_usun=label("Delete server");
		$label_restore=label("Restore server");
		$label_export=label("Export");

		$_write=true;
		$_delete=true;
		if (is_object($auth_acl))
		{
			$_system=$auth_acl->system;
			
			$auth_acl->init($nazwa,$id);
			$_grant=$auth_acl->hasRight('grant','kameleon');
			$_write=$auth_acl->hasRight('write','kameleon');
			$_delete=$auth_acl->hasRight('delete','kameleon');		
			$auth_acl->system=$_system;
	
			if (!$_grant) continue;
			
		}

		$onclick=$_write?'':"onclick=\"alert('$norights'); return false\"";


		echo " <tr ".$ser_bgcolor.">\n";
		echo "   <td align=right><a $onclick name=\"$nazwa\">".($i+1)."</td>\n";
		$style=($nazwa_org==$nazwa)?"style=\"font-weight:bold;color:Red\"":"";
		echo "   <td width=100%><a $onclick href=\"server.php?server=$id\" $style>$nazwa</a></td>\n";

		if ($_delete) echo "   <td><a href=\"javascript:delserver('$id','$nazwa')\">
								<img align=\"absmiddle\" class=k_imgbutton border=0 src=\"../img/i_delete_n.gif\" 
								onmouseover=\"this.src='../img/i_delete_a.gif'\" 
								onmouseout=\"this.src='../img/i_delete_n.gif'\" 
								alt='$label_usun: $nazwa'></a></td>\n";
	
		
		
		if ($grupa==$CONST_TRASH && $_delete)
		  echo "<td><a href=\"javascript:untrashserver('$id','$nazwa')\">
				<img align=\"absmiddle\" class=k_imgbutton border=0 src=\"../img/i_enter_n.gif\" 
								onmouseover=\"this.src='../img/i_enter_a.gif'\" 
								onmouseout=\"this.src='../img/i_enter_n.gif'\" 
								alt=\"$label_restore: $nazwa\"></a></td>\n";


		

		if ($grupa==$CONST_TRASH)
		{
			if ($_delete) echo "<td><input type=\"checkbox\" value=1 name=\"del_list[$id]\"></td>";
		}
		else
		{
			echo "<td><a href='$SCRIPT_NAME?server=$id&action=exportserver'><img 
								align=\"absmiddle\" class=k_imgbutton border=0 src=\"../img/i_export_n.gif\" 
								onmouseover=\"this.src='../img/i_export_a.gif'\" 
								onmouseout=\"this.src='../img/i_export_n.gif'\" 
								alt=\"$label_export\"></a>
					</td>";
			echo "<td><a href='tools.php?server=$id'><img 
								align=\"absmiddle\" class=k_imgbutton border=0 src=\"../img/i_tools_n.gif\" 
								onmouseover=\"this.src='../img/i_tools_a.gif'\" 
								onmouseout=\"this.src='../img/i_tools_n.gif'\" 
								alt=\"$label_tools\"></a>
					</td>";


		}

		echo " </tr>\n";
	}

	if ($grupa==$CONST_TRASH)
	{
		echo " <tr>\n";
		echo "<td colspan=5 align='right'><input type='submit' value='".label("Delete selected servers")."' class='k_input'</td>";
		echo " </tr>\n";
	}

	echo "</table><br>";

	if ($grupa==$CONST_TRASH)
		$akcja="delserver";
	else
		$akcja="trashserver";
?>
</form>

<form name="usunserwer" method="post">
  <input type=hidden name=action value="">
  <input type=hidden name=server value="">
</form>

<script>
	function new_group()
	{
		groupname=prompt("<?echo label("Server name")?>","");
		if (groupname == null) return;
		document.newgroup.nazwa.value=groupname;
		document.newgroup.action.value="addserver";
		document.newgroup.submit();
	}
	
	function delserver(serwer,nazwa)
	{
		conf="<?
				if ($grupa>=0)
					echo label("Do you realy want to trash this server and ALL its content !?");
				else
					echo label("Do you realy want to delete this server and ALL its content !?");
		?>";

		c=confirm(conf+" "+nazwa);


		if (!c) return;
		document.usunserwer.action.value='<?echo $akcja?>';
		document.usunserwer.server.value=serwer;
		document.usunserwer.submit();
	}
	function untrashserver(serwer,nazwa)
	{
		c=confirm("Czy przywróciæ serwer "+nazwa+" ?");
		if (!c) return;
		document.usunserwer.action.value='untrashserver';
		document.usunserwer.server.value=serwer;
		document.usunserwer.submit();
	}
</script>
