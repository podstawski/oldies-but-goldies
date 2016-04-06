<html>
<head>
    <title>KAMELEON: <?echo label("Proof pages");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" media="all" href="<? echo $kameleon->user[skinpath]; ?>/tdedit.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
    <?
	include_js("jquery-1.4");
	include_js("jquery-ui.min");
	include_js("kameleon");
	include("ajax_variables.php");
    ?>

</head>
<body>
<?
	include('include/helpbegin.h');
	include_once('include/proof-send.h');
	$comments='';

	include("include/navigation.h");
		
?>
<div class="km_toolbar">
  <ul>
  <?
	$langicon = in_array($lang,array("no","nl","tr","t","gr","g","bg","cz","cz2","hu","h","it","lt","l","sp","s","fr","f","ru","r","en","e","de","d","pl","p","i","pr")) ? $lang : "other";
	echo "<li id=\"km_lang_link\"><span class=\"km_icon km_iconi_lang_".$langicon."\" title=\"".label($lang)."\">".label($lang)."</span></li>";
  ?>
  </ul>
</div>

<?
include ("include/lang-change.h");
?>

<table class="tabelka" cellpadding="1" cellspacing="0">
<tr>
	<th><? echo label("Title")?></th>
	<th><? echo label("Proof action")?></th>
	<th><? echo label("Author")?></th>
	<th><? echo label("Date")?></th>
	<th><? echo label("Change count")?></th>
	<th><? echo label("Status")?></th>
</tr>

<?
	$query="SELECT id,title,unproof_autor,unproof_date,fullname,
					unproof_counter,sid AS webpagesid,noproof,unproof_comment,
					proof_date,nd_update
			FROM webpage LEFT JOIN passwd ON unproof_autor=username
			WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang'
			AND noproof IS NOT NULL
			ORDER BY unproof_date DESC";
	
	//$adodb->debug=1;
	$res=$adodb->Execute($query);
	$goto_alt=label('Goto page');

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		//$MAY_PROOF=checkRights($id,$PROOF_RIGHTS);
		$MAY_PROOF=$kameleon->checkRight('proof','page',$id);
		

		if (!$MAY_PROOF)
		{
			if ($unproof_autor!=$KAMELEON[username] ) continue;
		}
		else
		{
			
			
			if ($noproof==1) continue;

			if ($noproof==0 && !$FTP_RIGHTS && ($proof_date>=$nd_update || $unproof_autor!=$KAMELEON[username]) ) continue;

			if ($noproof==0 && $FTP_RIGHTS &&  sa_osoby_bez_ftpa_ale_proof($id) 
				&& $proof_date<$nd_update && $unproof_autor!=$KAMELEON[username]) continue;

			if ($noproof<0 && $FTP_RIGHTS &&  sa_osoby_bez_ftpa_ale_proof($id)) continue;

			if ($noproof>1 && $FTP_RIGHTS &&  sa_osoby_bez_ftpa_ale_proof($id)) continue;
		
		}

		
		if ($ser_bgcolor==" class=\"line_0\"") $ser_bgcolor=" class=\"line_1\"";
		else $ser_bgcolor=" class=\"line_0\"";

		if ($id==$referpage) $ser_bgcolor=" class=\"line_2\"";


		echo " <tr $ser_bgcolor>\n";

		echo "  <td><a href='index.php?page=$id'>$title [$id]</a>";
		
		$doproof='&nbsp;';	

		if (!$MAY_PROOF)
		{
			$label=addslashes(label('Send proof request'));
			$doproof.="<a href=\"$SCRIPT_NAME\" onclick=\"return proofSend('ProofRequest','$label',$id)\" class='k_icon'><img 
				class='k_icon' border=0 align=absMiddle src='img/i_proof_n.gif'
				alt='$label'></a>";

			$label=addslashes(label('Undo all changes'));
			$confirm=label('Are you sure');
			$doproof.="<a href=\"$SCRIPT_NAME?page=$id&action=ProofUndo\" onclick=\"return confirm('$confirm ?')\" class='k_icon'><img 
				class='k_icon' border=0 align=absMiddle src='img/i_noproof_n.gif'
				alt='$label'></a>";

			if (abs($noproof)!=1) $doproof='&nbsp;';
		}
		else
		{
			if (!$FTP_RIGHTS)
			{
				if ($noproof>=0 || $unproof_autor==$KAMELEON[username])
				{				
					$label=addslashes(label('Proof and send ftp request'));
					$doproof.="<a href=\"$SCRIPT_NAME\" onclick=\"return proofSend('ProofFtpRequest','$label',$id)\" class='k_icon'><img 
						class='k_icon' border=0 align=absMiddle src='img/i_doproof.gif'
						alt='$label'></a>";
				}
			}
			else
			{
				$label=addslashes(label('Proof and ftp page'));
				$doproof.="<a href=\"ftp.php?start=1&ftplimit=$id&action=proof\"  class='k_icon'><img 
					class='k_icon' border=0 align=absMiddle src='img/i_prooftp.gif'
					alt='$label'></a>";
			}


			if ($FTP_RIGHTS || abs($noproof)>1)
			{

				$label=addslashes(label('Reject changes'));

				$doproof.='<a href="'.$SCRIPT_NAME.'" onclick="return proofSend(\'ProofReject\',\''.$label.'\','.$id.')" class="k_icon"><img 
					class="k_icon" border="0" align="absMiddle" src="img/i_noproof.gif" width="23" height="22"
					alt="'.$label.'"></a>';
			}

			if ($kameleon->current_server->versions && $FTP_RIGHTS)
				$doproof.='<a class="k_icon" href="archiwum.php?wv_table=page&wv_sid='.$webpagesid.'&setreferpage='.$id.'"><img 
						class="k_icon"  border="0" align="absmiddle" src="img/i_arch.gif" width="23" height="22" alt="'.label("Check the archieve to retreive the best version").'"></a>';	

		}

		$label=addslashes(label('See confirmation log'));
		$doproof.='<a href="'.$SCRIPT_NAME.'" onclick="return proofComment('.$id.')" class="k_icon"><img 
						class="k_icon" border="0" width="23" height="22" align="absMiddle" src="img/i_help.gif" alt="'.$label.'"></a>';

		echo "<td>$doproof</td>";






		echo "  <td>";
		if (strlen($fullname)) echo $fullname." ($unproof_autor)"; else echo $unproof_autor;
		$action_label=label("action:$wv_action");
		if ($action_label=="action:$wv_action") $action_label=$wv_action;

		echo "  <td>".date('d-m-Y, H:i',$unproof_date);
	
		echo "  <td>$unproof_counter";

		$minus=($noproof==0 && $proof_date<$nd_update)?'-':'';

		echo "  <td>".label('prof_status_'.$minus.$noproof);

		echo "</td>";

			
		$comments.="commentsArray[$id]='".ereg_replace("[\r\n]+",'|',addslashes(stripslashes(trim($unproof_comment))))."';\n";
			
	}

	

?>
</table></td></tr>

</table>
<script language="javascript">
<?echo $comments?>
</script>
<?
	include("include/helpend.h");
?>

</body>
</html>
