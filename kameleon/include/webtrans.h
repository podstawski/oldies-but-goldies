<html>
<head>
    <title>KAMELEON: <? echo label8("WebKameleon translate aid"); ?></title>
    <link href="<? echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]; ?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body bgcolor="#c0c0c0" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0 onkeypress="return proofIfProofMode()">
<?

	include_once('include/transfun.h');
	include_once('include/utf8.h');

	if (strlen($set_trans_srclang))
	{
		$trans_srclang=$set_trans_srclang;
		$adodb->SetCookie('trans_srclang',$trans_srclang);
	}

	if (strlen($set_trans_ch_nt))
	{
		$trans_ch_nt=strtolower($set_trans_ch_nt)=='true'?1:0;
		$adodb->SetCookie('trans_ch_nt',$trans_ch_nt);
	}	

	if (strlen($set_trans_ch_nv))
	{
		$trans_ch_nv=strtolower($set_trans_ch_nv)=='true'?1:0;
		$adodb->SetCookie('trans_ch_nv',$trans_ch_nv);
	}	
	if (strlen($set_trans_ch_ep))
	{
		$trans_ch_ep=strtolower($set_trans_ch_ep)=='true'?1:0;
		$adodb->SetCookie('trans_ch_ep',$trans_ch_ep);
	}

	if (strlen($set_trans_order))
	{
		$trans_order=$set_trans_order;
		$adodb->SetCookie('trans_order',$trans_order);
	}

	if (isset($set_trans_search))
	{
		$trans_search=$set_trans_search;
		$adodb->SetCookie('trans_search',$trans_search);
	}	

	if (isset($set_trans_context))
	{
		$trans_context=$set_trans_context;
		$adodb->SetCookie('trans_context',$trans_context);
	}	

	
	if (is_array($set_trans_ch_ex))
	{
		list($key,$v)=each($set_trans_ch_ex);
		if ($v=='true') $trans_ch_ex[$key]=1;
		else unset($trans_ch_ex[$key]);
		$adodb->SetCookie('trans_ch_ex',$trans_ch_ex);
	}

	$moreand="";

	if ($trans_ch_nt) $moreand.=" AND wt_translation IS NULL AND wt_verification IS NULL";
	if ($trans_ch_nv) $moreand=" AND wt_verification IS NULL AND wt_translation IS NOT NULL";
	if ($trans_ch_ep) $moreand.=" AND wt_o_plain<>'' AND wt_o_plain IS NOT NULL";

	
	if (is_array($trans_ch_ex) && count($trans_ch_ex)) 
		$moreand.=" AND wt_table NOT IN ('".implode("','",array_keys($trans_ch_ex))."')";

	if (strlen($trans_search))
	{
		$s=addslashes(utf82lang(stripslashes($trans_search),$trans_srclang));
		$moreand.=" AND wt_o_plain~*'$s'";
	}

	if (strlen($trans_context))
	{
		$s=addslashes(utf82lang(stripslashes($trans_context),$trans_srclang));
		$moreand.=" AND wt_context='$s'";
	}

	if (!strlen($trans_order)) 
	{
		$trans_order="wt_o_plain";
		$adodb->SetCookie('trans_order',$trans_order);
	}

	$trans_goto+=0;
	if (!$trans_goto)
	{
		$sql="SELECT wt_sid AS trans_goto FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' 
				AND wt_translator='".$kameleon->user[username]."' $moreand
				ORDER BY wt_translation DESC
				LIMIT 1";
		parse_str(ado_query2url($sql));

		$trans_goto+=0;
		if (!$trans_goto)
		{
			$sql="SELECT wt_sid AS trans_goto FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' $moreand
					ORDER BY $trans_order,wt_sid
					LIMIT 1 ";
			parse_str(ado_query2url($sql));

		}
	}

	$wt_sid+=0;

	if (!strlen($trans_offset) || strlen($moreand))
	{
		$query="SELECT wt_sid FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' $moreand ORDER BY $trans_order,wt_sid";
		$res=$adodb->Execute($query);
		$trans_count=$res->RecordCount();
		for ($trans_offset=0;$trans_offset<$trans_count;$trans_offset++)
		{
			parse_str(ado_ExplodeName($res,$trans_offset));

			//echo "$trans_count $trans_offset | $wt_sid==$trans_goto <br> ";

			if ($wt_sid==$trans_goto) break;
		}
		
		
	}

	$sql="SELECT wt_similar FROM webtrans WHERE wt_sid=$trans_goto";
	parse_str(ado_query2url($sql));


	$saveall_exclude='';

	if ($ch_saveall && strlen($wt_similar)) $saveall_exclude=" AND wt_sid NOT IN ($wt_similar)"; 

	$next_offset=$trans_offset+1;
	if ($next_offset>=$trans_count) $next_offset=0;
	$query="SELECT wt_sid AS trans_next 
			FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' $moreand $saveall_exclude
			ORDER BY $trans_order,wt_sid
			LIMIT 1 OFFSET $next_offset";
	parse_str(ado_query2url($query));

	$next_link="index.php?trans_goto=$trans_next&trans_offset=$next_offset&trans_count=$trans_count";


	$prev_offset=$trans_offset-1;
	if ($prev_offset<0) $prev_offset=$trans_count-1;
	if ($prev_offset<0) $prev_offset=0;
	$query="SELECT wt_sid AS trans_prev 
			FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' $moreand $saveall_exclude
			ORDER BY $trans_order,wt_sid
			LIMIT 1 OFFSET $prev_offset";
	parse_str(ado_query2url($query));

	$prev_link="index.php?trans_goto=$trans_prev&trans_offset=$prev_offset&trans_count=$trans_count";


	
	$query="SELECT * FROM webtrans WHERE wt_sid=$trans_goto";
	parse_str(ado_query2url($query));

	$new_wt_o_html=kameleon_webtrans($wt_sid);

	if (!$wt_translatiion && !strlen($wt_o_plain)) $wt_translation=1;

?>
<table cellspacing=0 cellpadding=2 border=1 width="100%">

<tr>
	<td colspan=3 class=k_form>
	<div style="float:right"><a href="index.php?page=<?echo $referpage+0?>">WebKameleon</a></div>
	<?
		echo implode(' <span class=k_a>&raquo;</span> ', trans_decription($wt_sid))
	?>
	<br><? echo lang2utf8(stripslashes($wt_path),$trans_srclang) ?>
	</td>
</tr>

<tr>
	<td width="49%" class=k_formtitle>
		
			<?echo label8('Oryginal text');?>
			&nbsp;
			<select class="k_select" 
				onChange="if (this.value.length>0) location.href='index.php?trans_goto=<?echo $trans_goto?>&t=<?echo time()?>&set_trans_srclang='+this.value">
			<option value=""><?echo label8('Choose source encoding language')?></option>
			<?
				foreach (array_keys($CHARSET_TAB) AS $l)
				{
					if (!strlen($l)) continue;

					$sel=($trans_srclang==$l)?'selected':'';
					echo "<option value=\"$l\" $sel>".label8($l)."</option>";
				}
			?>
			</select>
	</td>
	<td class=k_formtitle>
		&nbsp;
	</td>
	<td width="49%" class=k_formtitle>
		
			<?echo label8('Translation');?>
	</td>
</tr>

<tr>
	<td width="49%" class=k_form valign="top">
		<div id="_wt_o_plain" style="width:100%;height:260px;overflow:auto"><?echo lang2utf8(nl2br(stripslashes($wt_o_plain)),$trans_srclang);?></div>
		<div id="_wt_o_html" style="width:100%;height:260px;overflow:auto;display:none"><?echo lang2utf8(stripslashes($new_wt_o_html),$trans_srclang);?></div>
	</td>
	<td width="2%" class=k_form align="center">
	<img src="img/i_next_n.gif" style="cursor:pointer;display:<?if ($wt_verification) echo 'none'?>" 
		onMouseOver="this.src='img/i_next_a.gif'" 
		onMouseOut="this.src='img/i_next_n.gif'"
		onClick="document.getElementById('wt_t_plain').value=document.getElementById('_wt_o_plain').innerText;document.getElementById('wt_t_plain').focus()">
		
	</td>
	<td width="49%" class=k_form valign="top">
		<form method="post" name="transform" action="index.php" 
		 target="trans" style="margin:0px;display:<?if ($wt_verification) echo 'none' ?>">
			<input type="hidden" name="action" value="TransLate">
			<input type="hidden" name="settransmode[ch_autosave]" value="" id="autosave">
			<input type="hidden" name="settransmode[ch_saveall]" value="" id="saveall">
			<textarea style="width:100%;height:260px; border: 0px; background-color:#d0d0d0"  
			 onBlur="if (document.getElementById('ch_autosave').checked) saveTrans()" class=k_form
			 onKeyDown="return textareaKey(this)" onClick="textareaTouched(this)" onSelect="textareaTouched(this)"
			 id="wt_t_plain" name="wt_t_plain[<? echo $wt_sid ?>]"><?echo lang2utf8($wt_t_plain)?></textarea>
			<input type="image" src="img/spacer.gif" width="10" height="10" align="right">
		</form>

		<div id="_wt_t_plain" style="width:100%;height:260px;overflow:auto;display:<?if (!$wt_verification) echo 'none' ?>">
			<?echo lang2utf8(nl2br(stripslashes($wt_t_plain)));?>
		</div>
		<div id="_wt_t_html" style="width:100%;height:260px;overflow:auto;display:none">
			<?echo lang2utf8(stripslashes($wt_t_html));?>
		</div>

		<?
			if ($wt_translation>1) echo label8('Translation').': '.date('d-m-Y, H:i',$wt_translation)." [$wt_translator]";
			if ($wt_verification>1) echo '<br>'.label8('Verification').': '.date('d-m-Y, H:i',$wt_verification)." [$wt_verificator]";
		?>
	</td>
</tr>
<tr>
	<td>
		<table width="100%">
		<tr>
		<td width='30%' class=k_form><a href="<?echo $prev_link?>" onClick="return aMayGo(this)">
		<img src="img/i_back_n.gif" border=0 align="absmiddle"
			alt="<?echo label8('Previous module')?>"
			onMouseOver="this.src='img/i_back_a.gif'" 
			onMouseOut="this.src='img/i_back_n.gif'"></a>
		<?echo $trans_offset+1?> / <?echo $trans_count?>
		</td>
		<td align="center" width="40%">
		<img src="img/i_editmode_n.gif" em="img/i_editmode" pm="img/i_previewmode" am="img/i_editmode"
			style="cursor:pointer"
			alt="<?echo label8('Toggle preview (html) and editmode')?>"
			onMouseOver="this.src=this.am+'_a.gif'" 
			onMouseOut="this.src=this.am+'_n.gif'"
			onClick="togglePlainHtml(this,'o')"
		>
		</td>
		<td align="right" width='30%'><a href="<?echo $next_link?>" onClick="return aMayGo(this)">
		<img src="img/i_forw_n.gif" border=0
			alt="<?echo label8('Next module')?>"
			onMouseOver="this.src='img/i_forw_a.gif'" 
			onMouseOut="this.src='img/i_forw_n.gif'">
		</a></td>
		</tr>
		</table>
	</td>
	<td>&nbsp;</td>

	<td class="k_form">
		<div style="float:right" align="right">
		<img src="img/i_save_n.gif" style="cursor:pointer;display:<?if ($wt_verification) echo 'none'?>" 
		onMouseOver="this.src='img/i_save_a.gif'" 
		onMouseOut="this.src='img/i_save_n.gif'"
		alt="<?echo label8('Save')?>"
		onClick="saveTrans(); goHref('index.php?trans_goto=<?echo $trans_goto?>')">

		<img src="img/i_proof_n.gif" style="cursor:pointer;display:<?if (!$wt_translation || $wt_verification) echo 'none'?>" 
		onMouseOver="this.src='img/i_proof_a.gif'" 
		onMouseOut="this.src='img/i_proof_n.gif'"
		alt="<?echo label8('Verify translated text')?>"
		onClick="proofTrans()">

		<img src="img/i_editmode_n.gif" em="img/i_editmode" pm="img/i_previewmode" am="img/i_editmode"
			style="cursor:pointer;display:<?if (!$wt_verification) echo 'none'?>"
			alt="<?echo label8('Toggle preview (html) and editmode')?>"
			onMouseOver="this.src=this.am+'_a.gif'" 
			onMouseOut="this.src=this.am+'_n.gif'"
			onClick="togglePlainHtml(this,'t')"
		>

		<img src="img/i_noproof_n.gif" style="cursor:pointer;display:<?if (!$wt_verification) echo 'none'?>" 
		onMouseOver="this.src='img/i_noproof_a.gif'" 
		onMouseOut="this.src='img/i_noproof_n.gif'"
		alt="<?echo label8('Unverify translated text')?>"
		onClick="proofTrans()">

		<br>

		<input type="button" class="k_button" 
			onClick="trans.location.href='index.php?trans_goto=<? echo $trans_goto?>&t=<?echo time()?>&action=TransProofAll'"
			value="<? echo label('Verify all simple modules') ?>">


		</div>
		
		<input type="checkbox" id="ch_autosave" class="k_chk" <?if ($ch_autosave) echo 'checked'?>> <?echo label8('Autosave')?><br>
		<input type="checkbox" id="ch_saveall" class="k_chk" <?if ($ch_saveall) echo 'checked'?>> <?echo label8('Save all similar')?>
		<? 
			if (strlen($wt_similar)) echo '('.label8('module count').': '.count(explode(',',$wt_similar)).')';
		?>
	</td>

</tr>

<tr>
	<td class=k_form valign=top>
	<?
		$module_may_be_verified=true;
		echo label8('Modules related').':<br>';

		$query="SELECT wt_sid AS wt_child,wt_translation AS wt_child_translation,wt_verification AS wt_child_verification 
				FROM webtrans WHERE wt_parent=$wt_sid ORDER BY wt_sid";
		$res=$adodb->Execute($query);
		$trans_count=$res->RecordCount();
		echo '<ol style="margin:1px 30px">';
		for ($i=0;$i<$trans_count;$i++)
		{
			parse_str(ado_ExplodeName($res,$i));
			$status='';
			if ($wt_child_translation) $status='['.label8('Translated').']';
			if ($wt_child_verification) $status='['.label8('Verified').']';
			else $module_may_be_verified=false;
			

			echo '<li>'.implode(' <span class=k_a>&raquo;</span> ',trans_decription($wt_child))." $status</li>";

		}
		echo '</ol>';
		
	?>
	</td>
	<td>&nbsp;</td>
	<td class=k_form valign=top>
	<?
		
		echo label8('Total items');
		$sql="SELECT count(*) AS ti FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang'";
		parse_str(ado_query2url($sql));
		echo ": $ti<br>";
		echo label8('Items translated');
		$sql="SELECT count(*) AS it FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' AND wt_translation>0";
		parse_str(ado_query2url($sql));
		echo ": $it<br>";
		echo label8('Items verified');
		$sql="SELECT count(*) AS iv FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' AND wt_verification>0";
		parse_str(ado_query2url($sql));
		echo ": $iv<br>";
	?>	
	</td>

</tr>


<tr>
	<td class=k_form valign=top>
		<table width='100%'><tr>

		<td valign='top' class=k_form>

		<input type="checkbox" class="k_chk" <?if ($trans_ch_nt) echo 'checked'?>
		 onClick="location.href='index.php?trans_goto=<?//echo $trans_goto?>&t=<?echo time()?>&set_trans_ch_nt='+this.checked">
		 <?echo label8('Show only not translated')?><br>
		<input type="checkbox" class="k_chk" <?if ($trans_ch_nv) echo 'checked'?>
		 onClick="location.href='index.php?trans_goto=<?//echo $trans_goto?>&t=<?echo time()?>&set_trans_ch_nv='+this.checked">
		 <?echo label8('Show only not verified')?><br>
		<input type="checkbox" class="k_chk" <?if ($trans_ch_ep) echo 'checked'?>
		 onClick="location.href='index.php?trans_goto=<?//echo $trans_goto?>&t=<?echo time()?>&set_trans_ch_ep='+this.checked">
		 <?echo label8('Don\'t show empty plain')?><br>

		<?
			$szukaj=label8('Search');
			$value=$szukaj;
			if (strlen($trans_search)) $value=$trans_search;

			$context=label8('Context');
			$context_value=$context;
			if (strlen($trans_context)) $context_value=$trans_context;
		?>
	
		<input class="k_input" type="text" value="<?echo $value?>" style="width:200px; margin-top:10px; margin-left:4px;"
		 onFocus="if (value=='<?echo $szukaj?>') value=''; else this.lastvalue=value;" lastvalue=""
		 onBlur="if (this.lastvalue!=value) location.href='index.php?trans_goto=<? //echo $trans_goto?>&t=<?echo time()?>&set_trans_search='+this.value"><br>

		<select class=k_select  style="width:200px; margin-top:10px; margin-left:4px;"
			onchange="location.href='index.php?trans_goto=<?echo $trans_goto?>&t=<?echo time()?>&set_trans_order='+this.value">
		<option value="wt_o_plain" <?if ($trans_order=='wt_o_plain') echo 'selected'?>><?echo label8('Order by oryginal text')?>
		<option value="wt_table" <?if ($trans_order=='wt_table') echo 'selected'?>><?echo label8('Order by section')?>
		<option value="wt_path" <?if ($trans_order=='wt_path') echo 'selected'?>><?echo label8('Order by page path')?>
		</select>
		<br>

		<input class="k_input" type="text" value="<?echo $context_value?>" style="width:100px; margin-top:10px; margin-left:4px;"
		 onFocus="if (value=='<?echo $context?>') value=''; else this.lastvalue=value;" lastvalue=""
		 onBlur="if (this.lastvalue!=value) location.href='index.php?trans_goto=<? //echo $trans_goto?>&t=<?echo time()?>&set_trans_context='+this.value"><br>


		</td>
		<td valign='top' class=k_form>
		<?
			$sql="SELECT wt_table,count(*) AS ile FROM webtrans WHERE wt_server=$SERVER_ID AND wt_lang='$lang' GROUP BY wt_table ORDER BY wt_table";
			$res=$adodb->Execute($sql);
			$c=$res->RecordCount();
			for ($i=0;$i<$c;$i++)
			{
				parse_str(ado_ExplodeName($res,$i));
				$t=time();
				$onclick="location.href='index.php?trans_goto=$trans_goto&t=$t&set_trans_ch_ex[$wt_table]='+!this.checked";
				$checked=$trans_ch_ex[$wt_table]?'':'checked';
				echo "<input type=\"checkbox\" class=\"k_chk\" onclick=\"$onclick\" $checked> ".label8('Show')." $wt_table ($ile)<br>";
			}
		?>
		</td>

		</tr></table>
	</td>
	<td>
	<img src="img/i_nextnext_n.gif" style="cursor:pointer;display:<?if ($wt_verification) echo 'none'?>" 
		onMouseOver="this.src='img/i_nextnext_a.gif'" 
		onMouseOut="this.src='img/i_nextnext_n.gif'"
		alt="<?echo label8('Copy content modules satisfying limit options')?>"
		onClick="copyGroup()">


	</td>
	<td class=k_form valign=top>
		<form method="post" action="index.php" ENCTYPE="multipart/form-data">
		<input type="hidden" name="action" value="TransImport">
		<a href="index.php?action=TransExport">
		<img src="img/i_export_n.gif" border=0 align="absMiddle"
			alt="<?echo label8('Export translation to MS Word')?>"
			onMouseOver="this.src='img/i_export_a.gif'" 
			onMouseOut="this.src='img/i_export_n.gif'"></a>

		<input type="file" name="transfile" class="k_button" size=35>
		<input type="submit" class="k_button" value="<?echo label8('Import')?>" 
			title="<?echo label8('Import translations from MS Word HTML file')?>">
		</form>
		<form method="post" action="index.php">
		<input type="hidden" name="action" value="TransReplace">
		<input type="hidden" name="trans_goto" value="<?echo $trans_goto?>">
		<fieldset style="width:99%; margin-left:2px;">
		<legend><?echo label8('Copy & replace phrases in satisfying limit options translated modules')?></legend>
		
		<table>
			<tr>
				<td class=k_form><?echo label8('Find what')?>:</td>
				<td><input name="trans_find" value="<?echo $trans_find?>" class="k_input" size=30></td>

				<td rowspan=2><input type="submit" value="<?echo label8('Replace')?>" class=k_button style="margin-left:20px"></td>
			</tr>
			<tr>
				<td class=k_form><?echo label8('Replace with')?>:</td>
				<td><input name="trans_replace" value="<?echo $trans_replace?>" class="k_input" size=30></td>
			</tr>
		</table>
	
		</fieldset>
		</form>	
	</td>

</tr>

</table>

<?
$parts=array();

if (strlen(strpos($wt_o_html,'<')) && $wt_translation && !$wt_verification)
{
	$html=stripslashes($wt_o_html);
	
	$totalpos=0;
	while (strlen($pos=strpos( substr($html,$totalpos), '<' )))
	{
		$plain=rtrim(substr($html,$totalpos,$pos));
		
		$endpos=strpos( substr($html,$totalpos+$pos), '>' );

		if (strlen(trim(trans_unhtml($plain))) && trans_requires_trans($plain))
		{
			$parts[]=array($totalpos,$totalpos+$pos,trans_unhtml($plain));
			
		}
		$totalpos+=$pos+$endpos+1;
	}
	$plain=substr($html,$totalpos);
	if ( strlen(trim($plain)) && trans_requires_trans($plain)) $parts[]=array($totalpos,$totalpos+strlen($plain),trans_unhtml($plain));

}

$i=0;
$parts_js='';
foreach($parts AS $part )
{
	/*
	echo $part[0].' - '.$part[1].' ('.($part[1]-$part[0]).')...('.strlen($part[2]).')';
	echo ': "'.lang2utf8($part[2],$trans_srclang);
	echo "\" , <b>\"".lang2utf8(substr($html,$part[0],$part[1]-$part[0]),$trans_srclang);
	echo "\"</b><br>";
	*/

	$parts_js.="parts[$i]='".str_replace("'","\\'",lang2utf8($part[2],$trans_srclang))."'\n";
	$parts_js.="xy[$i]='".$part[0].':'.$part[1]."'\n";
	$i++;
}

?>

<iframe name="trans" id="transid" style="display:none; width:100%; height:250px"></iframe>
<form name="transMatrix" method="post" target="trans" style="display:none" action="index.php">
	<input type="hidden" name="action" value="TransProof">
	<input type="hidden" name="trans_goto" value="<?echo $trans_goto?>">
	<input type="hidden" name="trans_next" value="<?echo $trans_next?>">
	
</form>

<form name="transCopyForm" method="post" style="display:none" action="index.php">
	<input type="hidden" name="action" value="TransCopy">
	<input type="hidden" name="trans_goto" value="<?echo $trans_goto?>">
</form>


<script language="javascript">
	var parts;
	var xy;
	var lastPos=0;
	var partIndex;
	var oText=document.getElementById('_wt_o_plain').innerHTML;
	var textareaKeyReturnTrue=true;
		
	function saveTrans()
	{
		<? if ($wt_verification) echo 'return;';?>

		chas=document.getElementById('ch_autosave');
		if (chas!=null) document.transform.autosave.value=chas.checked?1:0;
		chsa=document.getElementById('ch_saveall');
		if (chsa!=null)	document.transform.saveall.value=chsa.checked?1:0;
		document.transform.submit();
	}


	function proofPart()
	{
		i=partIndex;

		pos=oText.substr(lastPos).indexOf(parts[i]);

		//alert(oText.substr(lastPos)+'\n'+pos+'\n'+parts[i]);
		nText=oText.substr(0,lastPos+pos)+'<span style="background-color:black;color:white">'+parts[i]+'</span>'+oText.substr(lastPos+pos+parts[i].length);
		document.getElementById('_wt_o_plain').innerHTML=nText;

		lastPos+=pos+parts[i].length;
		//alert(pos+' - '+xy[i]+' - '+parts[i]);

	}

	function proofTrans()
	{

		parts = new Array;
		xy = new Array;
		lastPos=0;
		

		<? 
			if (!$wt_translation) echo "return;\n";
			
			echo $parts_js;
		?>
	
		if (<? echo (!strlen(strpos($wt_o_html,'<')) || $wt_verification)?'true':'false' ?>)
		{
			trans.location.href='index.php?t=<? echo time()?>&trans_goto=<?echo $trans_goto?>&action=TransProof';
			return;
		}
		
		if (<? echo $module_may_be_verified?'false':'true' ?>)
		{
			alert('<?echo label8('Verify related modules first')?>');
			return;
		}

		

		if (parts.length>0)
		{
			lastPos=0;
			partIndex=0;
			textareaKeyReturnTrue=false;
			document.getElementById('wt_t_plain').focus();
			proofPart();
			
			if (parts.length==1) 
			{
				document.getElementById('wt_t_plain').select();
				textareaKey(null);
			}
		}
		else
		{
			document.transMatrix.submit();
		}

	}


	var link='';

	function aGo()
	{
		ifr=document.getElementById('transid');
		if (ifr.readyState=='complete')
		{
			document.location=link;
			return;
		}
		setTimeout(aGo,100);
	}

	function goHref(href)
	{
		link=href+'&t=<?echo $adodb->now?>';
		aGo();
	}

	function aMayGo(a)
	{
		goHref(a.href);
		return false;
	}

	function togglePlainHtml(img,ot)
	{
		divHtml=document.getElementById('_wt_'+ot+'_html');
		divPlain=document.getElementById('_wt_'+ot+'_plain');

		if (divHtml.style.display=='none')
		{
			divHtml.style.display='';
			divPlain.style.display='none';
			img.am=img.pm;
		}
		else
		{
			divHtml.style.display='none';
			divPlain.style.display='';
			img.am=img.em;
		}
			
		img.src=img.am+'_a.gif'; 
	}

	var selectedObj;


	function getSel()
	{
		var txt = '';
		var foundIn = '';
		if (window.getSelection)
		{
			txt = window.getSelection();
			foundIn = 'window.getSelection()';
		}
		else if (document.getSelection)
		{
			txt = document.getSelection();
			foundIn = 'document.getSelection()';
		}
		else if (document.selection)
		{
			txt = document.selection.createRange().text;
			selectedObj=document.selection.createRange();
			foundIn = 'document.selection.createRange()';
		}
		else return;
	
		

		return txt;
		document.forms[0].selectedtext.value = 'Found in: ' + foundIn + '\n' + txt;
	}


	var textareaNewText=true;


	function textareaTouched(obj)
	{
		textareaNewText=true;
	}


	function textareaKey(obj)
	{
		if (textareaKeyReturnTrue) return true;

		ekc=event.keyCode;
		if ( (ekc>=16 && ekc<=18) || (ekc>=33 && ekc<=40) || (ekc>=112) ) 
		{
			textareaTouched(obj);
			return true;
		}

		//alert(ekc);

		if (ekc!=27)
		{
			tText=getSel();
			if (tText.length==0) 
			{
				if (!confirm('<?echo label8('Accept empty text')?> ?')) return false;
			}
			else
				if (!textareaNewText) if (!confirm('<?echo label8('Accept the same text')?> ?')) return false;

			
			var oTA=document.createElement("TEXTAREA");
			oTA.name='transmatrix['+xy[partIndex]+']';
			oTA.innerHTML=tText;
			document.transMatrix.appendChild(oTA);


			textareaNewText=false;
		}

		//alert(parts[partIndex]+' = '+tText);
		
		partIndex++;

		if (parts.length==partIndex)
		{
			document.transMatrix.submit();
		}
		else
		{
			proofPart();
		}

		return false;

	}


	function copyGroup()
	{
		if (!confirm('<?echo label8('Are you sure')?> ?')) return;
		document.transCopyForm.submit()
		
	}

	function proofIfProofMode()
	{
		if (event.keyCode!=13) return true;

		<? if (!$trans_ch_nv || $wt_verification || !$wt_translation) echo "return true;\n"; ?>

		proofTrans();
		return false;
	}

</script>






</body>
</html>