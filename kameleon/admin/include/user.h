<?
	if ($login=="" || $login=="kameleon") return;

	$skins=array();


	$skindir="../$CONST_SKINS_DIR";
	$handle=opendir($skindir);

	while (($file = readdir($handle)) !== false ) 
	{
		if ($file[0]==".") continue;
		if (is_dir("$skindir/$file")) $skins[]=$file;
	}
	closedir($handle);
	sort($skins);


	$query="SELECT admin,groupid AS gid,total_time,limit_time,fullname,email,skin,ulang
			FROM passwd WHERE username='$login'";

	$res=$adodb->Execute($query);
	if ($res->RecordCount())
	   	parse_str(ado_ExplodeName($res,0));
	else
		return;

	if (!strlen($skin)) $skin="kameleon";

	$query = " SELECT id, groupname FROM groups ORDER BY groupname";
  	$res=$adodb->Execute($query);
	for ($i=0;$i<$res->RecordCount();$i++)
 	{
	   parse_str(ado_ExplodeName($res,$i));
		if ($id==$groupid)
			$selected="selected";
		else
			$selected="";
		$gr.="<option value=$id $selected>$groupname</option>\n";
	}
	$gr="<select class=k_select name=id_grupy>$gr</select>";

	$sk="";

	foreach ($skins AS $s)
	{
		$sel=($skin==$s)?"selected":"";
		$sk.="<option value=\"$s\" $sel>$s</option>\n";
	}
	$sk="<select class=k_select name=id_skin>$sk</select>";
?>
	<form name="userpasswd" id="userpasswd" method="post" action="<?echo $SCRIPT_NAME?>" >
	<input type=hidden name=action value=passwd>
	<input type=hidden name=login value='<?echo $login?>'>
	<div class="secname">
    <a class="km_icon km_iconi_delete_m" href="javascript:deluser('<?echo $login?>')" title="<?echo label("Delete user").": $login" ?>"><?echo label("Delete user").": $login" ?></a> 
		<a class="km_icon km_iconi_save_m" href="javascript:document.getElementById('userpasswd').submit()" title="<?echo label("Set")?>"><?echo label("Set")?></a>
    <h2><?echo label("Setting for user").": <b>$login</b>"?></h2>
  </div>
  <div class="formularz">
    <div class="litem_1">
      <label><?echo label("new password")?>:</label>
      <div class="inputer">
        <input type="password" name="f_password" value="" />
      </div>
    </div>
    <div class="litem_2">
      <label><?echo label("full name")?>:</label>
      <div class="inputer">
        <input type="text" name="f_fullname" size="30" value="<?echo $fullname?>" />
      </div>
    </div>
    <div class="litem_1">
      <label><?echo label("e-mail")?>:</label>
      <div class="inputer">
        <input type="text" name="f_email" size="45" value="<?echo $email?>" />
      </div>
    </div>
    <div class="litem_2">
      <label><?echo label("group name")?>:</label>
      <div class="inputer">
        <?echo $gr?>
      </div>
    </div>
    <div class="litem_1">
      <label><?echo label("skin")?>:</label>
      <div class="inputer">
        <?echo $sk?>
      </div>
    </div>
    <div class="litem_2">
      <label><?echo label("language")?>:</label>
      <div class="inputer">
        <input type="text" name="ulang" size="2" value="<?echo $ulang?>" />
      </div>
    </div>
    <div class="litem_1">
      <label><?echo label("administrator privilege")?>:</label>
      <div class="inputer">
        <input type="checkbox" name="admin" value="1" <?if ($admin) echo "checked";?> />
      </div>
    </div>
    <div class="litem_2">
      <label><?echo label("time limit [hours]")?>:</label>
      <div class="inputer">
        <input type="text" name="limit_time" value="<? echo round($limit_time/3600)+0;?>" size="8"> / <? echo label("used").": ".floor($total_time/3600); ?>
      </div>
    </div>
    <div class="litem_1">
      <label><?echo label("new svn password")?>:</label>
      <div class="inputer">
        <input type="password" name="svn_pass" />
      </div>
    </div>
  </div>
	</form>

	<script language="javascript">

		function forgetpassword()
		{
			document.userpasswd.f_password.value='';
			document.userpasswd.svn_pass.value='';
		}

		setTimeout(forgetpassword,200);
	</script>

<?
	if (!$gid) return;


	echo "<form method='POST' id='servers_form' action='$SCRIPT_NAME'>";
	echo "<input type=hidden name=action value=modrights>\n";
	echo "<input type=hidden name=login value='$login'>";
	$prawa="<select class=k_select name=NewRight>\n";
	$prawa.="<option value=''>".label("Select server");

	$query="SELECT nazwa,id
			FROM servers
			WHERE groupid>0
			ORDER BY nazwa";
	$res=$adodb->Execute($query);
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$query="SELECT count(*) AS c FROM rights
			WHERE server=$id AND username='$login'";
		parse_str(ado_query2url($query));
		if ($c) continue;

		$prawa.="<option value=$id>$nazwa</option>";
	}

	$prawa.="</select>\n&nbsp;\n";	
	$prawa.="<input type=submit class=k_button value='".label("Add rights")."'>\n";

	$query="SELECT servers.nazwa,servers.id, 
			pages,menus,ftp,class,basic,nexpire,proof,acl,accesslevel AS al,template
		FROM servers,rights
		WHERE rights.username='$login'
		AND rights.server=servers.id
		ORDER BY servers.nazwa";

	$res=$adodb->Execute($query);

	$mesg=label("User rights to servers");
	echo "<div class=\"secname\">".$prawa.$mesg."</div>";
	
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$ftp_checked=($ftp)?"checked":"";
		$class_checked=($class)?"checked":"";
		$basic_checked=($basic)?"checked":"";
		$acl_checked=($acl)?"checked":"";
		$templ_checked=($template)?"checked":"";


		if (strlen($nexpire)) $expire=FormatujDate($nexpire);

		$expiration_date = strlen($nexpire)?$expire:"";
		echo "
    <div class=\"secname2\">
      <a class=\"km_icon km_iconi_delete_m\" href=\"javascript:delright('$id','$nazwa')\" title=\"".label("Delete user").": ".$login."\">".label("Delete user").": ".$login."</a>
      <a class=\"km_icon km_iconi_save_m\" href=\"javascript:document.getElementById('servers_form').submit()\" title=\"".label("Set")."\">".label("Set")."</a>
      <h2>".label("Server name").": <b>$nazwa</b><a name=\"$nazwa\" href=\".\"></a></h2>
    </div>
    <div class=\"formularz\">
      <div class=\"litem_1\">
        <label>".label("FTP").": </label>
        <div class=\"inputer\">
          <input type=\"checkbox\" value=\"1\" name='ftp[$id]' ".$ftp_checked." />
        </div>
      </div>
      <div class=\"litem_2\">
        <label>".label("Class").":</label>
        <div class=\"inputer\">
          <input type=\"checkbox\" value=\"1\" name=\"class[$id]\" ".$class_checked." />
        </div>
      </div>
      <div class=\"litem_1\">
        <label>".label("Template icon").":</label>
        <div class=\"inputer\">
          <input type=\"checkbox\" value=\"1\" name=\"template[$id]\" ".$templ_checked." />
        </div>
      </div>
      <div class=\"litem_2\">
        <label>".label("Basic").":</label>
        <div class=\"inputer\">
          <input type=\"checkbox\" value=\"1\" name=\"basic[$id]\" ".$basic_checked." />
        </div>
      </div>
      <div class=\"litem_1\">
        <label>".label("ACL").":</label>
        <div class=\"inputer\">
          <input type=\"checkbox\" value=\"1\" name=\"acl[$id]\" ".$acl_checked." />
        </div>
      </div>
      <div class=\"litem_2\">
        <label>".label("Pages").":</label>
        <div class=\"inputer\">
          <input type=\"text\" value=\"".$pages."\" name=\"pages[$id]\" size=\"60\" />
        </div>
      </div>
      <div class=\"litem_1\">
        <label>".label("Page correction").":</label>
        <div class=\"inputer\">
          <input type=\"text\" value=\"$proof\" name=\"proof[$id]\" size=\"60\" />
        </div>
      </div>
      <div class=\"litem_2\">
        <label>".label("Menus").":</label>
        <div class=\"inputer\">
          <input type=\"text\" value=\"".$menus."\" name=\"menus[$id]\" size=\"60\" />
        </div>
      </div>
      <div class=\"litem_1\">
        <label>".label("Access level").":</label>
        <div class=\"inputer\">
          <input type=\"text\" value=\"".$al."\" name=\"accesslevel[$id]\" size=\"5\" />
        </div>
      </div>
      <div class=\"litem_2\">
        <label>".label("Expiration date").":</label>
        <div class=\"inputer\">
          <input id=\"waznosc_".$i."\" type=\"text\" value=\"".$expiration_date."\" name=\"expire[$id]\" size=\"12\" />
        </div>
      </div>
    </div>
				
		<script>
		if(typeof(Calendar) == 'function')
		Calendar.setup({
			inputField     :    \"waznosc_".$i."\",   // id of the input field
			ifFormat       :    \"%d-%m-%Y\",       // format of the input field
			showsTime      :    false,
			align          :    \"Tl\",           
			timeFormat     :    \"24\"
		});
		</script>
		";
	}
	echo "</form>\n";



?>
