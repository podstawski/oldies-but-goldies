<?
	global $WEBTD,$adodb;


	if (strlen($WEBTD->xml))
	{
		if (substr($WEBTD->xml,0,7)=='base64:') $NAVI=@unserialize(base64_decode(substr($WEBTD->xml,7)));
		else $NAVI=@unserialize($WEBTD->xml);
	}

	if (!strlen($NAVI['remark'][sendmail])) $NAVI['remark'][sendmail]=ini_get ("sendmail_path");
	if (!strlen($NAVI['recommend'][sendmail])) $NAVI['recommend'][sendmail]=ini_get ("sendmail_path");


	if ($this_editmode) if ($page==$WEBTD->page_id || $WEBTD->page_id<0)
	{
		global $NAVIFORM;

		//echo $WEBTD->sid; echo '<pre>'; print_r($NAVIFORM); echo '</pre>';
		
		if ($NAVIFORM['sid']+0==$WEBTD->sid+0)
		{
			$NAVI=$NAVIFORM;
			$xml=base64_encode(serialize($NAVI));
			$sql="UPDATE webtd SET xml='base64:$xml' WHERE sid=".$WEBTD->sid;
			//$adodb->debug=1;
			$adodb->execute($sql);
			//$adodb->debug=0;
		}

?>
		<fieldset style="width:99%; margin-left:2px;">
		<legend style="cursor:pointer" 
			onclick="document.getElementById('page_navigator_opt_form').style.display=(document.getElementById('page_navigator_opt_form').style.display=='none')?'':'none'">
			<?=label('Page navigator')?>
		</legend>
		<form method="POST" action="index.php?page=<?echo $page?>" id="page_navigator_opt_form" style="display:none">
		<input type="hidden" value="<?echo $WEBTD->sid?>" name="NAVIFORM[sid]">
		<table width="100%">
			<tr>
				<td width="10%"><input type="checkbox" value="1" class="k_cbx" name="NAVIFORM[back][img]" <? if ($NAVI['back'][img]) echo 'checked';?> title="<? echo label('Show image ').$IMAGES.'/navi/'.$lang.'/back.gif'?>"></td>
				<td><?echo label('Back')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['back'][imgalt]?>" name="NAVIFORM[back][imgalt]" class="k_input" style="width:100%" title="<? echo label('Alt text to image')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['back'][txt]?>" name="NAVIFORM[back][txt]" class="k_input" style="width:100%" title="<? echo label('Show text')?>"></td>
			</tr>

			<tr>
				<td width="10%"><input type="checkbox" value="1" class="k_cbx" name="NAVIFORM[up][img]" <? if ($NAVI['up'][img]) echo 'checked';?> title="<? echo label('Show image ').$IMAGES.'/navi/'.$lang.'/up.gif'?>"></td>
				<td><?echo label('Go top')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['up'][imgalt]?>" name="NAVIFORM[up][imgalt]" class="k_input" style="width:100%" title="<? echo label('Alt text to image')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['up'][txt]?>" name="NAVIFORM[up][txt]" class="k_input" style="width:100%" title="<? echo label('Show text')?>"></td>
			</tr>

			<tr>
				<td width="10%"><input type="checkbox" value="1" class="k_cbx" name="NAVIFORM[print][img]" <? if ($NAVI['print'][img]) echo 'checked';?> title="<? echo label('Show image ').$IMAGES.'/navi/'.$lang.'/print.gif'?>"></td>
				<td><?echo label('Print')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][imgalt]?>" name="NAVIFORM[print][imgalt]" class="k_input" style="width:100%" title="<? echo label('Alt text to image')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][txt]?>" name="NAVIFORM[print][txt]" class="k_input" style="width:100%" title="<? echo label('Show text')?>"></td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Url prefix')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][needle]?>" name="NAVIFORM[print][needle]" class="k_input" style="width:100%" title="<? echo label('Remote constant prefix')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][target]?>" name="NAVIFORM[print][target]" class="k_input" style="width:100%" title="<? echo label('Target print prefix')?>"></td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Position')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][x]?>" name="NAVIFORM[print][x]" class="k_input" style="width:100%" title="<? echo label('Left-top X coordinate')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][y]?>" name="NAVIFORM[print][y]" class="k_input" style="width:100%" title="<? echo label('Left-top Y coordinate')?>"></td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Dimensions')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][w]?>" name="NAVIFORM[print][w]" class="k_input" style="width:100%" title="<? echo label('Width')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['print'][h]?>" name="NAVIFORM[print][h]" class="k_input" style="width:100%" title="<? echo label('Height')?>"></td>
			</tr>


			<tr>
				<td width="10%"><input type="checkbox" value="1" class="k_cbx" name="NAVIFORM[favorite][img]" <? if ($NAVI['favorite'][img]) echo 'checked';?> title="<? echo label('Show image ').$IMAGES.'/navi/'.$lang.'/favorite.gif'?>"></td>
				<td><?echo label('Add to favourites')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['favorite'][imgalt]?>" name="NAVIFORM[favorite][imgalt]" class="k_input" style="width:100%" title="<? echo label('Alt text to image')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['favorite'][txt]?>" name="NAVIFORM[favorite][txt]" class="k_input" style="width:100%" title="<? echo label('Show text')?>"></td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Prefix')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['favorite'][prefix]?>" name="NAVIFORM[favorite][prefix]" class="k_input" style="width:100%" title="<? echo label('Favorite prefix')?>"></td>
				<td width="30%">&nbsp;</td>
			</tr>
	
			<tr>
				<td width="10%"><input type="checkbox" value="1" class="k_cbx" name="NAVIFORM[remark][img]" <? if ($NAVI['remark'][img]) echo 'checked';?> title="<? echo label('Show image ').$IMAGES.'/navi/'.$lang.'/remark.gif'?>"></td>
				<td><?echo label('Remark')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['remark'][imgalt]?>" name="NAVIFORM[remark][imgalt]" class="k_input" style="width:100%" title="<? echo label('Alt text to image')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['remark'][txt]?>" name="NAVIFORM[remark][txt]" class="k_input" style="width:100%" title="<? echo label('Show text')?>"></td>
			</tr>			

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Receipient\'s email')?></td>
				<td width="60%" colspan=2><input type="text" value="<?echo $NAVI['remark'][email]?>" name="NAVIFORM[remark][email]" class="k_input" style="width:100%" ></td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Sendmail path')?></td>
				<td width="60%" colspan=2><input type="text" value="<?echo $NAVI['remark'][sendmail]?>" name="NAVIFORM[remark][sendmail]" class="k_input" style="width:100%" ></td>
			</tr>

			<tr>
				<td width="10%"><input type="checkbox" value="1" class="k_cbx" name="NAVIFORM[recommend][img]" <? if ($NAVI['recommend'][img]) echo 'checked';?> title="<? echo label('Show image ').$IMAGES.'/navi/'.$lang.'/recommend.gif'?>"></td>
				<td><?echo label('Recommend page')?></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['recommend'][imgalt]?>" name="NAVIFORM[recommend][imgalt]" class="k_input" style="width:100%" title="<? echo label('Alt text to image')?>"></td>
				<td width="30%"><input type="text" value="<?echo $NAVI['recommend'][txt]?>" name="NAVIFORM[recommend][txt]" class="k_input" style="width:100%" title="<? echo label('Show text')?>"></td>
			</tr>	

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Sendmail path')?></td>
				<td width="60%" colspan=2><input type="text" value="<?echo $NAVI['recommend'][sendmail]?>" name="NAVIFORM[recommend][sendmail]" class="k_input" style="width:100%" ></td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Header')?></td>
				<td width="60%" colspan=2><input type="text" value="<?echo str_replace('"','&quot;',$NAVI['recommend'][header])?>" name="NAVIFORM[recommend][header]" class="k_input" style="width:100%" ></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;&nbsp;<?echo label('Footer')?></td>
				<td width="60%" colspan=2><input type="text" value="<?echo str_replace('"','&quot;',$NAVI['recommend'][footer])?>" name="NAVIFORM[recommend][footer]" class="k_input" style="width:100%" ></td>
			</tr>
			<tr>
				<td colspan=4 align="right"><input type="submit" class="k_button" value="<?echo label('Save')?>"></td>	
			</tr>
		</table>
		</form>
		</fieldset>
		<br />&nbsp;
<?
	}

	global $CHARSET;

	if ($KAMELEON_MODE)
	{
		include('remote/kameleon_api_navigator.php');
	}
	else
	{
		$str=$WEBTD->xml;
		if (substr($WEBTD->xml,0,7)=='base64:') 
		{
			$str=substr($str,7);
			echo '<'."?php \$NAVI=unserialize(base64_decode('$str'));?".'>';

		}
		else echo '<'."?php \$NAVI=unserialize('$str');?".'>';

		echo read_file('remote/kameleon_api_navigator.php');
	}

	$remark = "<div id=\"remark_".$WEBTD->sid."\" class=\"remark\" 
				style=\"position: absolute; display: none;\">";
	$remark.= "<h1><img src=\"".$IMAGES."/navi/".$lang."/close.gif\" alt=\"".label('Close')."\" class=\"close\" style=\"cursor:pointer\" onclick=\"kameleon_remark()\">".$NAVI['remark'][imgalt]."</h1>";
	$remark.= "<h2><b>".label('Page title').":</b> ".$WEBPAGE->title."</h2>";

	$remark.= "<div id=\"remarkurl_".$WEBTD->sid."\"></div>";
	
	$remark.= "<form method=\"post\" action=\"$self\" name=\"kameleon_remark_form\" 
				onSubmit=\"return kameleon_remark_form_submit(this,'".label('Email format wrong')."')\">";
	$remark.= "<input type=\"hidden\" name=\"REMARK[title]\" value=\"".$WEBPAGE->title."\">";
	$remark.= "<input type=\"hidden\" name=\"REMARK[charset]\" value=\"".$CHARSET."\">";
	$remark.= "<input type=\"hidden\" name=\"REMARK[subject]\" value=\"".label('Remark').' - '.$WEBPAGE->title."\">";
	$remark.= "<div class=\"row\"><b>".label('Remark').":</b><textarea class=\"\" name=\"REMARK[text]\"></textarea></div>";
	$remark.= "<div class=\"row\"><b>".label('Your e-mail').":</b><input type=\"text\" name=\"REMARK[mailfrom]\" 
				id=\"k_remark_email\" value=\"\"></div>";
	$remark.= "<div class=\"row\"><input type=\"submit\" class=\"button\" value=\"".label("Send")."\"></div>";
	$remark.= "</form>";
	
	$remark.= "</div>\n";

	$recommend = "<div id=\"recommend_".$WEBTD->sid."\" class=\"recommend\" 
			style=\"position: absolute; display: none;\">";
	$recommend.= "<h1><img src=\"".$IMAGES."/navi/".$lang."/close.gif\" alt=\"".label('Close')."\" class=\"close\" style=\"cursor:pointer\" onclick=\"kameleon_recommend()\">".$NAVI['recommend'][imgalt]."</h1>";
	$recommend.= "<h2><b>".label('Page title').":</b> ".$WEBPAGE->title."</h2>";
	$recommend.= "<div id=\"remarkurl_".$WEBTD->sid."\"></div>";

	$recommend.= "<form method=\"post\" action=\"$self\" name=\"kameleon_recommend_form\"
				onSubmit=\"return kameleon_recommend_form_submit(this,'".label('Email format wrong')."')\">";
	$recommend.= "<input type=\"hidden\" name=\"RECOMMEND[title]\" value=\"".$WEBPAGE->title."\">";
	$recommend.= "<input type=\"hidden\" name=\"RECOMMEND[charset]\" value=\"".$CHARSET."\">";
	$recommend.= "<input type=\"hidden\" name=\"RECOMMEND[subject]\" value=\"".label('Recommendation').' - '.$WEBPAGE->title."\">";
	$recommend.= "<div class=\"row\"><b>".label('Your recipient\'s e-mail').":</b><input type=\"text\" name=\"RECOMMEND[mailto]\" 
					id=\"k_recommend_mailto\" value=\"\"></div>";
	$recommend.= "<div class=\"row\"><b>".label('Remarks').":</b><textarea class=\"\" name=\"RECOMMEND[text]\"></textarea></div>";
	$recommend.= "<div class=\"row\"><b>".label('Your e-mail').":</b><input type=\"text\" name=\"RECOMMEND[mailfrom]\" 
					id=\"k_recommend_mailfrom\" value=\"\"></div>";
	$recommend.= "<div class=\"row\"><b>".label('Your name').":</b><input type=\"text\" name=\"RECOMMEND[name]\" value=\"\"></div>";
	$recommend.= "<div class=\"row\"><input type=\"submit\" class=\"button\" value=\"".label("Send")."\"></div>";
	$recommend.= "</form>";
	
	$recommend.= "</div>\n";

?>

<div class="navi" style="position: relative;">
<?
	if (strlen($NAVI['remark'][img]) || strlen($NAVI['remark'][txt])) echo $remark;
	if (strlen($NAVI['recommend'][img]) || strlen($NAVI['recommend'][txt])) echo $recommend;


	//print_r($NAVI);

	$hrefs=array(
			'back'=>'javascript:history.back()',
			'up'=>'#top',
			'print'=>'javascript:kameleon_print()',
			'favorite'=>'javascript:kameleon_favorite()',
			'remark'=>'javascript:kameleon_remark()',
			'recommend'=>'javascript:kameleon_recommend()');

	reset($NAVI);
	
	global $SZABLON_PATH;
	while (list($k,$v)=each($NAVI))
	{
		if ($k=='sid') continue;
		if (strlen($v[img])) 
		{
			$naviimg_src = $IMAGES."/navi/".$lang."/".$k.".gif";
			$naviimg_arr = @getImageSize($SZABLON_PATH."/images/navi/".$lang."/".$k.".gif");
			$rozmiary='';
			if (strlen($naviimg_arr[3])) $rozmiary=' '.$naviimg_arr[3]; 
			
			echo "<a href=\"".$hrefs[$k]."\"><img src=\"".$naviimg_src."\" ".$naviimg_arr[3]." border=\"0\" align=\"absMiddle\" alt=\"".$v[imgalt]."\"$rozmiary></a>";
		}	
		if (strlen($v[txt]))
			echo " <a href=\"".$hrefs[$k]."\">".$v[txt]."</a>";
	}




?>
</div>