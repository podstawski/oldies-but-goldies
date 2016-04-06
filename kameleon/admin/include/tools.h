<?
	$langs=array();
	$vers=array();
	$users=array();

	$sql="SELECT lang AS __lang FROM webtd WHERE server=$server GROUP BY lang";
	$res=$adodb->Execute($sql);
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$langs[]=$__lang;
	}

	$sql="SELECT ver AS __ver FROM webtd WHERE server=$server GROUP BY ver ORDER BY ver";
	$res=$adodb->Execute($sql);
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$vers[]=$__ver;
		$max_ver=$__ver;
	}

	$sql="SELECT ver AS primary_ver,lang AS primary_lang, trans
			FROM servers WHERE id=$server";
	parse_str(ado_query2url($sql));

	if (strlen($trans)) $trans=unserialize($trans);

	$alllangs=array_keys($CHARSET_TAB);
	$kcht=$adodb->GetCookie('KAMELEON_CHARSET_TAB');
	if (is_array($kcht)) $alllangs=array_keys($kcht);	


	$sql="SELECT username FROM rights WHERE server=$server";
	$res=$adodb->Execute($sql);
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$users[]=$username;
	}

	sort($users);
?>

<iframe name="tools" id="toolsIF" style="width=100%;display:none;"></iframe>

  <div class="formularz">
  
    <form action="tools.php" method="post" onSubmit="return sure(this,true)" target="tools">
      <input type="hidden" name="action" value="tools">
      <input type="hidden" name="server" value="<?echo $server?>">
      <input type="hidden" name="tool" value="cp_lang2lang">
      <div class="litem_1">
        <label><? echo label('cp_lang2lang')?></label>
        <div class="inputer">
          <select name="src" class="k_select">
      		<?
      			foreach ($langs AS $l)
      			{
      				$sel=($l==$primary_lang)?'selected':'';
      				echo "<option $sel value=\"$l\">".label($l);
      			}
      		?>
      		</select>
      		&raquo;
      		<select name="dst" class="k_select">
      		<option value="">
      		<?
      			echo label('Choose language');
      			foreach ($alllangs AS $l)
      			{
      				if (!strlen($l)) continue;
      				if (in_array($l,$langs)) continue;
      				echo "<option value=\"$l\">".label($l);
      			}
      		?>
      		</select>
      		<select name="_ver" class="k_select">
      		<option value="">
      		<?
      			echo label('All versions');
      			foreach ($vers AS $v)
      			{
      				
      				echo "<option value=\"$v\">$v";
      			}
      		?>
      		</select>
      		<input type="submit" class="k_button" value="<? echo label('Run')?>" />
        </div>
      </div>
    </form>
    
    <form action="tools.php" method="post" onSubmit="return sure(this,true)" target="tools">
      <input type="hidden" name="action" value="tools">
      <input type="hidden" name="server" value="<?echo $server?>">
      <input type="hidden" name="tool" value="cp_ver2ver">
      <div class="litem_2">
        <label><? echo label('cp_ver2ver')?></label>
        <div class="inputer">
          <select name="src" class="k_select">
      		<?
      			foreach ($vers AS $v)
      			{
      				$sel=($v==$primary_ver)?'selected':'';
      				echo "<option $ver value=\"$v\">$v";
      			}
      		?>
      		</select>	
      		&raquo;
      		<input name="dst" class="k_input" style="width:50px">
      		<input type="submit" class="k_button" value="<? echo label('Run')?>" />
        </div>
      </div>
    </form>
    
    <form action="servers.php" method="post" onSubmit="return sure(this,false)">
      <input type="hidden" name="action" value="deletelangver">
      <input type="hidden" name="server" value="<?echo $server?>">
      <div class="litem_1">
        <label><? echo label('Delete part')?></label>
        <div class="inputer">
          <select name="_lang" class="k_select">
      		<option value="">
      		<?
      			echo label('Choose language');
      			foreach ($langs AS $l)
      			{
      				echo "<option value=\"$l\">".label($l);
      			}
      		?>
      		</select>
      		<select name="_ver" class="k_select">
      		<option value="">
      		<?
      			echo label('Choose version');
      			foreach ($vers AS $v)
      			{
      				
      				echo "<option value=\"$v\">$v";
      			}
      		?>
      		</select>
      		<input type="submit" class="k_button" value="<? echo label('Run')?>" />
        </div>
      </div>
    </form>
    
    <form action="tools.php" method="post" onSubmit="return sure(this,true)" target="tools">
      <input type="hidden" name="action" value="begintrans">
      <input type="hidden" name="server" value="<?echo $server?>">
      <div class="litem_2">
        <label><? echo label('Start translation')?></label>
        <div class="inputer">
          <select name="trans[lang]" class="k_select">
      		<option value="">
      		<?
      			echo label('Choose language');
      			foreach ($langs AS $l)
      			{
      				if (is_array($trans) && in_array($l,array_keys($trans))) continue;
      
      				echo "<option value=\"$l\">".label($l);
      			}
      		?>
      		</select>
      		<select name="trans[ver]" class="k_select">
      		<option value="">
      		<?
      			echo label('Choose version');
      			foreach ($vers AS $v)
      			{
      				
      				echo "<option value=\"$v\">$v";
      			}
      		?>
      		</select><br />
      		<?
      			foreach ($users AS $u)
      			{
      				
      				echo "<input class=\"k_chk\" type=\"checkbox\" name=\"transusers[$u]\" value=1> $u<br />";
      			}
      		?>
      		<input type="submit" class="k_button" value="<? echo label('Run')?>" />
        </div>
      </div>
    </form>
    
    <? if (is_array($trans)) foreach (array_keys($trans) AS $tl) {?>

    <form action="servers.php" method="post" >
      <input type="hidden" name="action" id="action_input" value="changetrans">
      <input type="hidden" name="server" value="<?echo $server?>">
      <input type="hidden" name="_lang" value="<?echo $tl?>">
      <div class="litem_1"  style="display:<?if (!strlen($trans)) echo 'none'?>">
        <label><? echo label('Translation in progress')?><br /><? echo label($tl)?></label>
        <div class="inputer">
          <?
        		echo label('Total items');
        		$sql="SELECT count(*) AS ti FROM webtrans WHERE wt_server=$server AND wt_lang='$tl'";
        		parse_str(ado_query2url($sql));
        		echo ": $ti<br>";
        		echo label('Items translated');
        		$sql="SELECT count(*) AS it FROM webtrans WHERE wt_server=$server AND wt_lang='$tl' AND wt_translation>0";
        		parse_str(ado_query2url($sql));
        		echo ": $it<br>";
        		echo label('Items verified');
        		$sql="SELECT count(*) AS iv FROM webtrans WHERE wt_server=$server AND wt_lang='$tl' AND wt_verification>0";
        		parse_str(ado_query2url($sql));
        		echo ": $iv<br>";
        	?>
        	<br />
        	<input type='button' value="<?echo label('Cancel and delete all translation modules')?>" class='k_button' onclick="if (sure(this,false)) {action_input.value='canceltrans'; submit()}" />
        	<input type='button' value="<?echo label('Confirm and apply')?>" class='k_button' <? if (!$iv) echo 'disabled'?> onclick="<? if (!$iv) echo 'return;'?>action_input.value='confirmtrans'; submit()" />
        	<?
      			foreach ($users AS $u)
      			{
      				$ch=in_array($u,$trans[$tl][users])?'checked':'';
      				echo "<input class=\"k_chk\" type=\"checkbox\" $ch name=\"transusers[$u]\" value=1> $u<br>";
      			}
      		?>
      		<input type="submit" class="k_button" value="<? echo label('Change')?>" />
        </div>
      </div>
    </form>
    <?}?>
    
  </div>

<script language="javascript">

function sure(f,statusIframe)
{
	if (!confirm('<?echo label('Are you sure')?> ?')) return false;

	if (statusIframe) { document.getElementById('toolsIF').style.display='block';  }

	return true;
}

</script>