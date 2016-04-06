<?
	
	$grupysel="<select class=\"km_select\" onchange=\"document.location.href=this[this.selectedIndex].value\">";
	
	$SetGroupSelected="";
	if (strlen($grupa)) $SetGroupSelected = "selected";
	$grupysel.= "<option class=k_select value='$SCRIPT_NAME?SetGroup=' $SetGroupSelected style=\"background-color : #E0E0E0;\">".label("Select group")."</opiton>";
		
	$query="SELECT groupname,id FROM groups ORDER BY groupname";
	$res=$adodb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		if ($i) $grupy.= " - ";
		parse_str(ado_ExplodeName($res,$i));
		
		$SetGroupSelected="";
		if ($grupa==$id) $SetGroupSelected = "selected";
		$grupysel.= "<option value='$SCRIPT_NAME?SetGroup=$id' $SetGroupSelected>$groupname</opiton>";
	}
	$grupysel.= "</select>";
	


?>
<div class="km_toolbar">
  <ul>
    <li class="km_label">
      <label><?=label("Select group")?></label>
      <?=$grupysel?>
    </li>
    <li><a class="km_icon km_iconi_new" href="javascript:newgroup()" title="<?echo label("Create new group")?>"><?echo label("Create new group")?></a></li>
  </ul>
</div>
