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
		$grupysel.= "<option class=k_select value='$SCRIPT_NAME?SetGroup=$id' $SetGroupSelected>$groupname</opiton>";
	}
	$grupysel.= "</select>";
	
	
	$usersel="<select class=\"km_select\"	onchange=\"document.location.href=this[this.selectedIndex].value\">";

	$SetGroupSelected="";
	if (strlen($grupa)) $SetGroupSelected = "selected";
	$usersel.= "<option class=k_select value='$SCRIPT_NAME?SetLogin=&SetGroup=$groupid' $SetGroupSelected style=\"background-color : #E0E0E0;\">".label("Select user")."</opiton>";	
		
	if (strlen($grupa))
   		$query="SELECT username, groupid,fullname FROM passwd,groups WHERE groupid=id AND id='$grupa' ORDER BY username";
	else
   		$query="SELECT username, groupid FROM passwd WHERE groupid NOT IN(SELECT id FROM groups) OR groupid=NULL ORDER BY username";


//	$adodb->debug=1;

	$res=$adodb->Execute($query);


	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		
		$SetGroupSelected="";
		if ($login==$username) $SetGroupSelected = "selected";
		$usersel.= "<option class=k_select value='$SCRIPT_NAME?SetLogin=$username&SetGroup=$groupid' $SetGroupSelected>$fullname [$username]</opiton>";
	}
	$usersel.= "</select>";
?>
<div class="km_toolbar">
  <ul>
    <li class="km_label">
	     <label><?echo label("Select group")?>:</label>
       <?=$grupysel?>
    </li>
    <?	if (strlen($grupa) && !$hidenew)	{ ?>
    <li><a class="km_icon km_iconi_new" href="javascript:newuser()" title="<?echo label("Create new user")?>"><?echo label("Create new user")?></a></li>
    <? } ?>
    <li class="km_sep"></li>
    <li class="km_label">
      <label><?=label("Select user")?>:</label>
      <?=$usersel?>
    </li>
  </ul>
</div>
