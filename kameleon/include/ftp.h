<?
	if (!$FTP_RIGHTS) return;
	if (!$editmode) echo "<script>location.href='/';</script>"; 


	$query="SELECT count(*) AS editor_count FROM rights 
			WHERE server=$SERVER_ID AND (nexpire IS NULL OR nexpire<=".time().")";
	parse_str(ado_query2url($query));



	if ($editor_count==1 || $ADMIN_RIGHTS)
	{
		$query="SELECT ftp_server, ftp_dir, ftp_pass, ftp_user 
				FROM servers WHERE id=$SERVER_ID";
		parse_str(ado_query2url($query));

		if (!strlen($ftp_server) || !strlen($ftp_pass) || !strlen($ftp_user) ) 
			$server_configuration=1;
	}
	else
	{
		$server_configuration=0;
	}


	include('include/kameleon_ftp.h');

?>
<html>

<head>
    <title>KAMELEON: <?echo label("FTP");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
	  <link rel="stylesheet" type="text/css" media="all" href="<? echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]; ?>/calendar.css" title="win2k-cold-1" />
<?

	switch($lang)
	{
		case "p":
		case "i":
		case "pl":
			$clang = "pl";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "d":
		case "de":
			$clang = "de";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "f":
		case "fr":
			$clang = "fr";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "r":
		case "ru":
			$clang = "ru";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "t":
			$clang = "cs";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "s":
			$clang = "es";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "h":
			$clang = "hu";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;
		case "g":
			$clang = "gr";
			$ctimeFormat = '%d-%m-%Y %H:%M';
			break;


		default: 
			$clang="en";
			$ctimeFormat = '%d-%m-%Y %H:%M';
	}

	include_js("calendar");
	include_js("calendar-$clang");
	include_js("calendar-setup");
	include_js("jquery-1.4");
	include_js("jquery-ui.min");
	include_js("kameleon");

	include("ajax_variables.php");
?>
</head>
<body>


<?
	include("include/navigation.h");
	
	$alt_all=label("Re-publish all pages");
	$alt=label("Start new FTP transfer");
	$limitalt=label("Limit to one page");
	$limittree=label("Limit to the tree");



	$sql="DELETE FROM ftp WHERE t_begin IS NULL AND t_start < EXTRACT(EPOCH FROM now()) - 3600;";
	$sql.="UPDATE ftp SET t_end=EXTRACT(EPOCH FROM now()) WHERE t_end IS NULL AND t_begin < EXTRACT(EPOCH FROM now()) - 24*3600;";
	
	$adodb->execute($sql);
	
?>

<div class="km_toolbar">
<form id="publikacje_form" <?if ($debug_mode) echo 'target="DEBUG_TARGET"'?>>
	<input type="hidden" name="start" value="1">
	<input type="hidden" name="ftpall" id="ftpall" value="0">
  <ul>
    <li><a class="km_icon km_iconi_ftp_all" href="javascript:document.getElementById('ftpall').value=1;document.getElementById('ftplimit').value='';document.getElementById('publikacje_form').submit()" title="<?=$alt_all?>"><?=$alt_all?></a></li>
    <li><a class="km_icon km_iconi_ftp" href="javascript:document.getElementById('ftplimit').value='';document.getElementById('publikacje_form').submit()" title="<?=$alt?>"><?=$alt?></a></li>    
    
    <? if (!$BASIC_RIGHTS) { ?>
  	<li class="km_sep"></li>
  	<li class="km_label">
      <label for="ftplimit"><?=label('Page')?></label>
      <input type="text" value="<?=$referpage?>" name="ftplimit" id="ftplimit" size="8" class="km_text">
    </li>
  	<li><a class="km_icon km_iconi_ftp_one" href="javascript:document.getElementById('publikacje_form').submit()" title="<?=$limitalt?>"><?=$limitalt?></a></li>
  	<li><a class="km_icon km_iconi_ftp_down" href="javascript:document.getElementById('ftplimit').value=document.getElementById('ftplimit').value+'+';document.getElementById('publikacje_form').submit()" title="<?=$limittree?>"><?=$limittree?></a></li>
  	<? } else { ?>
    <li class="km_sep"><input type="hidden" name="ftplimit" value=""></li>
    <? } ?>
    <li><a class="km_icon km_iconi_ftp_img" href="javascript:document.getElementById('ftplimit').value='img';document.getElementById('publikacje_form').submit()" title="<?=label("Transfer images")?>"><?=label("Transfer images")?></a></li>
    <? if (!$BASIC_RIGHTS && $CONST_REMOTE_INCLUDES_ARE_HERE) { ?>
    <li><a class="km_icon km_iconi_ftp_inc" href="javascript:document.getElementById('ftplimit').value='inc';document.getElementById('publikacje_form').submit()" title="<?=label("Transfer include modules")?>"><?=label("Transfer include modules")?></a></li>
    <? } ?>
    <? if (file_exists($UFILES)) { ?>
    <li><a class="km_icon km_iconi_ftp_att" href="javascript:document.getElementById('ftplimit').value='att';document.getElementById('publikacje_form').submit()" title="<?=label("Transfer attachment")?>"><?=label("Transfer attachment")?></a></li>
    <? } if (!$BASIC_RIGHTS) { ?>
    <li class="km_sep"></li>
    <li class="km_label">
      <label for="ftpatid"><?=label("Specify date and time of the FTP process")?></label>
      <input type="text" value="<?=$referpage?>" name="ftpat" id="ftpatid" size="20" title="<?=label("Specify date and time of the FTP process")?>" class="km_text">
    </li>
    <? if ($ADMIN_RIGHTS || $editor_count==1) { ?>
    <li class="km_sep"></li>
    <li><a class="km_icon km_iconi_ftp_setup" href="<?=$SCRIPT_NAME?>?server_configuration=1" title="<?=label("FTP server configuration")?>"><?=label("FTP server configuration")?></a></li>
    <? } }     
    echo "
    <li class=\"km_sep\"></li>";
	$langicon = in_array($lang,array("no","nl","tr","t","gr","g","bg","cz","cz2","hu","h","it","lt","l","sp","s","fr","f","ru","r","en","e","de","d","pl","p","i","pr")) ? $lang : "other";
	echo "<li id=\"km_lang_link\"><span class=\"km_icon km_iconi_lang_".$langicon."\" title=\"".label($lang)."\">".label($lang)."</span></li>";
	
    ?>
  </ul>
</form>
</div>


<?

	include ("include/lang-change.h");

	$configurator=false;
	if ($server_configuration)
	{
		include("include/ftp_conf.h");
		unset($start);
		$configurator=true;
	}


	if (isset($start))
	{
		$id=0;
		$ftpids=array();
		$query="SELECT id FROM ftp WHERE t_end IS NULL AND server=$SERVER_ID";
		parse_str(ado_query2url($query));

		$query="INSERT INTO ftp (username,server,lang,ver) VALUES ('$USERNAME',$SERVER_ID,'$lang',$ver)";
		if (!$id || strlen($ftplimit)) 
		{
			$adodb->Execute($query);
			$start=1;
		}
		else
			$start=0;

		$query="SELECT max(id) AS id FROM ftp WHERE server=$SERVER_ID AND username='$USERNAME'";
		parse_str(ado_query2url($query));
		
		$explore=$id;
		$ftpids[]=$id;
		
		if ($start && is_array($FTP_ALSO_VERSION))
		{
			for ($i=0;$i<count($FTP_ALSO_VERSION);$i++)
			{
				$query="INSERT INTO ftp (username,server,lang,ver) VALUES ('$USERNAME',$SERVER_ID,'$lang',$FTP_ALSO_VERSION[$i])";
				$adodb->Execute($query);
				
				$query="SELECT max(id) AS id FROM ftp WHERE server=$SERVER_ID AND username='$USERNAME'";
				parse_str(ado_query2url($query));
				$ftpids[]=$id;
				
			}
		}
	}

	$LIMIT="";
	if (strlen($ftplimit)) 
	{
		switch ($ftplimit)
		{
			case "img":
			case "images":
				$LIMIT="limitimages=1";
				break;
			case "inc":
				$LIMIT="limitinc=1"; 
				break;
			case "att":
				$LIMIT="limitatt=1"; 
				break;
			default:
				$LIMIT="limitpage=$ftplimit";
				break;
		}	
		if (strstr($ftplimit,"+")) $LIMIT.=" limitpage_all_tree=1";
	}

	$ALL=($ftpall)?"no_date_check=1":"";

	$H="CMS_API_HOST=".urlencode($CMS_API_HOST);

	$cmd="";
	if (is_array($FTP_ALSO_VERSION)) if (!CONST_WINDOWS) $cmd.=" { ";


	$ftp="ftp.php";
	$cmd.="$PHP_PREFIX$PHP_EXE $ftp $explore $LIMIT $ALL $H ";


	if (strlen($ftpat)) $ALL.=' startat='.formatujdatesql($ftpat).' ';
	

	if ($start) 
	{
		$cwd=getcwd();
		//chdir("$cwd/tools");
	
		kameleon_ftp($ftpids,"$LIMIT $ALL $H"); 
	
		chdir($cwd);
	}
	
	$explore+=0;

	$query="SELECT * FROM ftp WHERE server=$SERVER_ID AND t_end >0 AND id<>$explore ORDER BY id DESC";
	if (!$showall) $query.="\n LIMIT 10";
	$ftpy_end=ado_ObjectArray($adodb,$query);

	$query="SELECT * FROM ftp WHERE server=$SERVER_ID AND t_end IS NULL OR id=$explore ORDER BY id DESC";
	$ftpy_current=ado_ObjectArray($adodb,$query);


	if ( !is_Array($ftpy_current) ) unset($ftpy_current);
	if ( !is_Array($ftpy_end) ) unset($ftpy_end);


	echo "<table class=\"tabelka\" cellpadding=\"1\" cellspacing=\"0\">\n";

	if ($configurator==false) 
	{
		echo " <tr>\n";
		echo "  <th>".label("No")."</th>\n";
		echo "  <th>".label("Username")."</th>\n";
		echo "  <th>".label("Language")."</th>\n";
		echo "  <th>".label("Version")."</th>\n";
		echo "  <th>".label("Begin date")."</th>\n";
		echo "  <th>".label("Finish date")."</th>\n";
		echo " </tr>\n";
	}
	for ($i=0;$i<count($ftpy_current)+count($ftpy_end) && !$server_configuration;$i++)
	{
		$begin="";
		$end="";

		
		$ftpy[$i] = ($i>=count($ftpy_current)) ? $ftpy_end[$i-count($ftpy_current)] : $ftpy_current[$i];

		
		if ($ftpy[$i]->t_begin)
		{
			$begin=$DT->getPLDate($ftpy[$i]->t_begin);
		}
		if ($ftpy[$i]->t_end)
		{
			$end=$DT->getPLDate($ftpy[$i]->t_end);
		}

		if (strlen($begin) && !strlen($end)) $forbid_new=1;

		//if (!strlen($begin) ) $begin=label("in")." ".((60-time()%60)%60)." ".label("sec.");


		echo "<tr class=\"line_".($i % 2)."\">\n";
		$lp=$i+1;
		$id=$ftpy[$i]->id;


		$alt=label("Stop FTP process");
		if (!strlen($end) && ( $ftpy[$i]->username=="$PHP_AUTH_USER" || $ADMIN_RIGHTS)  ) 
			$end="<img onclick='stopftp($id)'
					border=0 src='img/i_stop_n.gif'
					onmouseover=\"this.src='img/i_stop_a.gif'\"
					onmouseout=\"this.src='img/i_stop_n.gif'\"
					alt='$alt'>";

		if ($explore==$id) $e="<a href=$SCRIPT_NAME>-</a>";
		else $e="<a href=$SCRIPT_NAME?explore=$id>+</a>";
		echo "  <td>$lp: $e</td>\n";
		echo "  <td>".$kameleon->userFullName($ftpy[$i]->username)."</td>\n";
		echo "  <td>".label($ftpy[$i]->lang)."</td>\n";
		echo "  <td>".$ftpy[$i]->ver."</td>\n";
		echo "  <td>$begin</td>\n";
		echo "  <td>$end</td>\n";

		echo " </tr>\n";
		if ($explore==$id )
		{
			$query="SELECT * FROM ftplog WHERE ftp_id=$id ORDER BY id";
			$log=ado_ObjectArray($adodb,$query);
			for ($l=0;is_array($log) && $l<count($log);$l++)
			{
				echo " <tr class=\"ftp_raport\">\n";
				echo "  <td colspan=5 align=right>".$log[$l]->rozkaz."</td>\n";
				echo "  <td>".$DT->getPLDate($log[$l]->nczas).", ".$log[$l]->wynik."</td>\n";
				echo " </tr>\n";


			}
		}
	}
	if (!$showall && !$server_configuration)
	{
		echo "<tr><td colspan=6 align=right>";
		echo "<a href=$SCRIPT_NAME?showall=1>";
		echo label("More");
		echo "</a></td></tr>\n"; 
	}
	echo "</table> \n";


	

?>



<form name="ftpstop" method="post" action="<?echo $SCRIPT_NAME?>">
	<input type=hidden name="action" value="FtpStop">
	<input type=hidden name="ftpid" value="">
</form>

<script language="Javascript">
	
    Calendar.setup({
        inputField     :    "ftpatid",   // id of the input field
        ifFormat       :    "<?php echo $ctimeFormat;?>",       // format of the input field
        showsTime      :    true,
		align          :    "Tl",           
        timeFormat     :    "24"
    });
	function stopftp(id)
	{
		if (!confirm("<?echo label("Are you sure")?> ?")) return;

		document.ftpstop.ftpid.value=id;
		document.ftpstop.submit();
	}
</script>

</body>
</html>
