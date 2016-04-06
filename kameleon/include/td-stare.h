<?
global 	$CONST_MODE, $CONST_PARSER_INTEGRATED , $editmode, $hf_editmode,$helpmode,$main_buttons,$main_copy;
global $insert_td;
global $C_SWF_STYLE, $CONST_SWF_JS, $C_EDITOR_FORM;
global $kameleon_editor_form_counetr;

// Na potrzeby JS menu na belkach
global $APIS, $adodb;
global $kameleon;
global $SERVER,$SERVER_NAME;
global $DEFAULT_PATH_KAMELEON_UINCLUDES;
global $DEFAULT_PATH_KAMELEON_UINCLUDES_SVN;


push ($this_editmode);

if ($editmode && $page_id>=0 && !$hf_editmode ) $this_editmode=1;
if ($editmode && $page_id<0 && !$hf_editmode ) $this_editmode=0;
if ($editmode && $page_id>=0 && $hf_editmode ) $this_editmode=0;
if ($editmode && $page_id<0 && $hf_editmode ) $this_editmode=1;

if ($CONST_MODE=="express" && $editmode) $this_editmode=1;
if (!$C_SWF_STYLE) $WEBTD->swfstyle=0;
if ($C_SWF_STYLE) include_once('include/swf.h');

$bg_color='silver';

if (!$WEBPAGE->hidden) if (strstr($WEBPAGE->unproof_sids,':'.$WEBTD->sid.':') || $WEBTD->hidden==100) $bg_color=$SERVER->editbordercolor;
//if (strstr($WEBPAGE->unproof_sids,':'.$WEBTD->sid.':') && !$WEBPAGE->noproof ) $bg_color='gray';

if ($CONST_PARSER_INTEGRATED || file_exists("$SZABLON_PATH/td.h")) 
{
	if ($this_editmode )
	{
		$up="";
		$alt_next=label("Next page");
		$hash="kameleon_td${page_parts_index}_$WEBTD->pri";
		$hashencoded=urlencode($hash);

		$alt_up=label("Move up");
		$img_up_n="img/i_up_min_n.gif";
		$img_up_a="img/i_up_min_a.gif";
		$alt_down=label("Move down");
		$img_down_n="img/i_down_min_n.gif";
		$img_down_a="img/i_down_min_a.gif";
		
		if ($page_id<0 && is_Array($TD_POZIOMY_HF))
		{
			$alt_up=label("Move left");
			$img_up_n="img/i_left_min_n.gif";
			$img_up_a="img/i_left_min_a.gif";
			$alt_down=label("Move right");
			$img_down_n="img/i_right_min_n.gif";
			$img_down_a="img/i_right_min_a.gif";
		}

		$tab_name="tab".ereg_replace("-","_",$WEBTD->page_id)."_$WEBTD->pri";
		$domenuId = 'td_' . $WEBTD->sid;
		
		$info_text = "";
		if (strlen($WEBTD->type))			$info_text .= label("Type").": ".$WEBTD->type;
		if (strlen($WEBTD->level))			$info_text .= "\n".label("Level").": ".$WEBTD->level;
		if (strlen($WEBTD->menu_id))		$info_text .= "\n".label("Menu").": ".$WEBTD->menu_id;
		if (strlen($WEBTD->html))			$info_text .= "\n".label("Include file").": ".$WEBTD->html;
		if (strlen($WEBTD->api))			$info_text .= "\n".label("Include api").": ".$WEBTD->api;
		if (strlen($WEBTD->staticinclude) && 
			($WEBTD->staticinclude == 1))	$info_text .= "\n".label("Files included during publication");
		if (strlen($WEBTD->bgimg) && !$WEBTD->swfstyle)			$info_text .= "\n".label("Background image").": ".$WEBTD->bgimg;
		if (strlen($WEBTD->bgimg) && $WEBTD->swfstyle)			$info_text .= "\n".label("Macromedia SWF file").": ".$WEBTD->bgimg;
		if (strlen($WEBTD->bgcolor))		$info_text .= "\n".label("Background color").": ".$WEBTD->bgcolor;
		if (strlen($WEBTD->align))			$info_text .= "\n".label("Horizontal align").": ".$WEBTD->align;
		if (strlen($WEBTD->valign && !$WEBTD->swfstyle))			$info_text .= "\n".label("Vertical align").": ".$WEBTD->valign;
		if (strlen($WEBTD->width ))			$info_text .= "\n".label("Width").": ".$WEBTD->width;
		if (strlen($WEBTD->size && $WEBTD->swfstyle))			$info_text .= "\n".label("Height").": ".$WEBTD->size;
		if (strlen($WEBTD->class))			$info_text .= "\n".label("Class name").": ".$WEBTD->class;
		if (strlen($WEBTD->img) && !$WEBTD->swfstyle)			$info_text .= "\n".label("Title image").": ".$WEBTD->img;
		if (strlen($WEBTD->img) && $WEBTD->swfstyle)			$info_text .= "\n".label("Image parameter").": ".$WEBTD->img;
		if (strlen($WEBTD->more))			$info_text .= "\n".label("More").": ".$WEBTD->more;
		if (strlen($WEBTD->next))			$info_text .= "\n".label("Next page").": ".$WEBTD->next;
		if (strlen($WEBTD->size && !$WEBTD->swfstyle))			$info_text .= "\n".label("Size").": ".$WEBTD->size;
		
		if (strlen($WEBTD->cos))			$info_text .= "\n".label("Number parameter").": ".$WEBTD->cos;
		if (strlen($WEBTD->costxt))			$info_text .= "\n".label("Text parameter").": ".$WEBTD->costxt;
		
		if ($WEBTD->valid=='f')	
		{
			$info_text .= "\n".label("Module date activity").":";
			if (strlen($WEBTD->nd_valid_from))	$info_text .= "\n     ".label("valid from").": ". FormatujDate($WEBTD->nd_valid_from, 'd-m-Y H:i');
			if (strlen($WEBTD->nd_valid_to))		$info_text .= "\n     ".label("valid to").": ". FormatujDate($WEBTD->nd_valid_to, 'd-m-Y H:i');
		}
		

		$infoab="";$infoae="";
		if ($WEBTD->swfstyle)
		{
			$infoab='<a href="'.urldecode(kameleon_mode_swf_link($WEBTD)).'" target="swf-debug">';
			$infoae='</a>';
		}

		$info="$infoab<img class=k_imgbutton src=\"img/i_infomin_n.gif\" onmouseover=\"this.src='img/i_infomin_a.gif'\" onmouseout=\"this.src='img/i_infomin_n.gif'\" border=0 width=11 height=17 hspace=0 vspace=0 alt='$info_text'>$infoae";	
		$up="<a class=k_a href=$SCRIPT_NAME?table=td&page=$page&pole=page_id&wart=$WEBTD->page_id&pri=$WEBTD->pri&dir=up&action=Move><img class=k_imgbutton src=$img_up_n onmouseover=\"this.src='$img_up_a'\" onmouseout=\"this.src='$img_up_n'\" alt='$alt_up' border=0 width=11 height=17 hspace=0 vspace=0></a>";
		$down="<a class=k_a href=$SCRIPT_NAME?table=td&page=$page&pole=page_id&wart=$WEBTD->page_id&pri=$WEBTD->pri&dir=down&action=Move><img class=k_imgbutton src=$img_down_n onmouseover=\"this.src='$img_down_a'\" onmouseout=\"this.src='$img_down_n'\" alt='$alt_down' border=0 width=11 height=17 hspace=0 vspace=0></a>";
		$delete="<a class=k_a href=javascript:zmiana('$WEBTD->page_id:$WEBTD->pri','UsunTD')><img class=k_imgbutton src=img/i_delete_min_n.gif onmouseover=\"this.src='img/i_delete_min_a.gif'\" onmouseout=\"this.src='img/i_delete_min_n.gif'\" alt='".label("Delete")." $WEBTD->title' border=0 width=17 height=17 hspace=0 vspace=0 align=right></a>";
		$edit="<a class=k_a href=\"javascript:tdedit($WEBTD->page_id,$WEBTD->pri,'$hashencoded','$tab_name')\"><img class=k_imgbutton src=img/i_property_min_n.gif onmouseover=\"this.src='img/i_property_min_a.gif'\" onmouseout=\"this.src='img/i_property_min_n.gif'\" alt='".label("Edit")."' border=0 width=17 height=17 hspace=0 vspace=0></a>";
		$copy="<img class=k_imgbutton style='cursor:hand' onclick=\"skopiuj('$WEBTD->page_id:$WEBTD->pri','td')\" src=img/i_copy_min_n.gif onmouseover=\"this.src='img/i_copy_min_a.gif'\" onmouseout=\"this.src='img/i_copy_min_n.gif'\" alt='".label("Copy")." $WEBTD->title' border=0 width=17 height=17 hspace=0 vspace=0>";
		$new="<a href=$SCRIPT_NAME?page=$page&page_id=$page_id&_level=$WEBTD->level&_type=$WEBTD->type&action=DodajTD#$hash><img class=\"k_imgbutton\" src=\"img/i_newmodule_n.gif\" onmouseover=\"this.src='img/i_newmodule_a.gif'\" onmouseout=\"this.src='img/i_newmodule_n.gif'\" width=17 height=17  border=0 alt='$insert_td'></a>";
    $dragicon="<img class=\"kameleon_dragicon\" src=\"img/nkam/drag_icon.gif\" border=\"0\" alt=\"".label("Drag & Drop")."\">";
		//if ($page_id<0) $copy="";

		if ($WEBTD->hidden)
		{
			$lab=label("Module invisible");
			if ($WEBTD->hidden==100) $lab=label("Module ready to delete");
			$visibility="<img class=k_imgbutton border=0 align=absMiddle src=img/i_invisible_min_n.gif onmouseover=\"this.src='img/i_invisible_min_a.gif'\" onmouseout=\"this.src='img/i_invisible_min_n.gif'\" alt='".$lab."'>";
		}
		else	
		{
			if ($WEBTD->valid=='f')
				$visibility="<img class=k_imgbutton border=0 align=absMiddle src=img/i_visible_disabled_min_n.gif onmouseover=\"this.src='img/i_visible_min_a.gif'\" onmouseout=\"this.src='img/i_visible_disabled_min_n.gif'\" alt='".label("Module visible, but expired")."'>";
			else
				$visibility="<img class=k_imgbutton border=0 align=absMiddle src=img/i_visible_min_n.gif onmouseover=\"this.src='img/i_visible_min_a.gif'\" onmouseout=\"this.src='img/i_visible_min_n.gif'\" alt='".label("Module visible")."'>";
		}

		$visibility="<a class=k_a href='$SCRIPT_NAME?page=$page&page_id=$page_id&pri=$WEBTD->pri&action=visibility'>$visibility</a>";
       

		$svn_cmd="";
		eval("\$KAMELEON_UINCLUDES=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
		if ( substr($WEBTD->html,0,1)!='@' && strlen($WEBTD->html) && strlen($SERVER->svn) && (@is_writable($KAMELEON_UINCLUDES.'/'.$WEBTD->html) || @is_writable($KAMELEON_UINCLUDES) && !file_exists($KAMELEON_UINCLUDES.'/'.$WEBTD->html)) )
		{

		
			eval("\$KAMELEON_UINCLUDES_SVN=\"$DEFAULT_PATH_KAMELEON_UINCLUDES_SVN\";");

			$html=$WEBTD->html;
			if ( !file_exists("$KAMELEON_UINCLUDES_SVN/$html") )
			{
				$svn_cmd="<img class=k_imgbutton style='cursor:hand' onclick=\"svn_start('$html')\" src=img/i_svn_start_min_n.gif onmouseover=\"this.src='img/i_svn_start_min_a.gif'\" onmouseout=\"this.src='img/i_svn_start_min_n.gif'\" alt='".label("Start editing include file").": $html' border=0 width=17 height=17 hspace=0 vspace=0>";

			}
			else
			{
				$svn_cmd="<img class=k_imgbutton style='cursor:hand' onclick=\"svn_end('inc')\" src=img/i_svn_end_min_n.gif onmouseover=\"this.src='img/i_svn_end_min_a.gif'\" onmouseout=\"this.src='img/i_svn_end_min_n.gif'\" alt='".label("File being edited, click to confirm changes")."' border=0 width=17 height=17 hspace=0 vspace=0>";

			}

			$svn_cmd="<td bgcolor=\"$bg_color\" width=19>$svn_cmd</td>";

		}

		if ($WEBTD->menu_id) 
			$menu_id="<a class=k_a href=menus.$KAMELEON_EXT?menu=$WEBTD->menu_id&setreferpage=$page><img class=k_imgbutton src=img/i_menu_min_n.gif onmouseover=\"this.src='img/i_menu_min_a.gif'\" onmouseout=\"this.src='img/i_menu_min_n.gif'\" alt='(".label("Menu").": $WEBTD->menu_id)' border=0 width=17 height=17 hspace=0 vspace=0></a>";
		else
			$menu_id="";
            
		if ($WEBTD->next) 
			$next="<a class=k_a href=$SCRIPT_NAME?page=$WEBTD->next&referer=$page><img class=k_imgbutton src=img/i_next_min_n.gif onmouseover=\"this.src='img/i_next_min_a.gif'\" onmouseout=\"this.src='img/i_next_min_n.gif'\" alt='$alt_next' border=0 width=11 height=17 hspace=0 vspace=0></a>";		
		else
			$next="";
        
        if ($WEBTD->more) 
			$more="<a class=k_a href=$SCRIPT_NAME?page=$WEBTD->more&referer=$page><img class=k_imgbutton src=img/i_more_min_n.gif onmouseover=\"this.src='img/i_more_min_a.gif'\" onmouseout=\"this.src='img/i_more_min_n.gif'\" alt='".label('More')."' border=0 width=11 height=17 hspace=0 vspace=0></a>";		
		else
			$more="";    

		$onContextMenu=" onContextMenu=\"return showMenu('TD', '".$WEBTD->page_id."', '".$WEBTD->sid."', '".$WEBTD->server."', '".$WEBTD->type."', '".$WEBTD->level."', '".$WEBTD->api."', '".$WEBTD->pri."', '".$WEBPAGE->id."', '".$hashencoded."', '".$tab_name."', '".$WEBTD->hidden."', '".$WEBTD->valid."', '".$SCRIPT_NAME."', '".$WEBTD->menu_id."', event )\"";


		$buttons="$dragicon$up$down$edit$copy$next$menu_id$more";
		if ($CONST_MODE=="express")
			$buttons="$edit$next$menu_id$more$copy";
			
		$visibility="<td bgcolor=\"$bg_color\" width=19>$visibility</td>";
		$delete="<td bgcolor=\"$bg_color\" width=19>$delete</td>";
		$info="<td bgcolor=\"$bg_color\" width=11>$info</td>";
		$dragicon="<td width=\"20\">".$dragicon."</td>";
		$classdrag=" class=\"kameleon_dragbox\" id=\"kameleon_dragbox_".$WEBTD->sid."\" ";

		if ( $WEBTD->accesslevel > $kameleon->current_server->accesslevel)
		{
			$link=label("No rights to edit this module");
			$buttons="$link";
			$visibility="";
			$delete="";
			$info="";
			$onContextMenu="";
		}


		if ($WEBTD->page_id != $page_id)
		{
			$link=label("Link from page");
			$buttons="<a href=$SCRIPT_NAME?page=$WEBTD->page_id class='k_a' title='pri:$WEBTD->pri,level:$WEBTD->level,ver:$WEBTD->ver'>$link: $WEBTD->page_id</a>";
			$visibility="";
			$delete="";
			$info="";
			$onContextMenu="";
			$classdrag="";
		}
	
		if ($WEBTD->ver != $ver)
		{
			$buttons="<span title='pri:$WEBTD->pri,level:$WEBTD->level,page:$WEBTD->page_id'>".label("No module in this version")." ($WEBTD->ver)</span>";
			$visibility="";
			$delete="";
			$onContextMenu="";
			$classdrag="";

		}
		if ($helpmode && $WEBTD->page_id==$page_id && $WEBTD->ver==$ver) 
			echo "<tdhelp name=\"h_$tab_name\">";


		if ($CONST_MODE=="express")
		{
			$info="";
			$down="";
			$up="";
			$onContextMenu="";
			echo "
			<div title=\"".$WEBTD->sid.",".$WEBTD->pri."\" \"".$classdrag."\">
      <a name=$hash></a>
					<table id='$tab_name'
						onmouseover=\"wk_show('id_td_$tab_name');  this.style.border='2px dotted $SERVER->editbordercolor';event.cancelBubble = true;\" 
						onMouseOut=\"wk_hide('id_td_$tab_name'); this.style.border='0px dotted $SERVER->editbordercolor';event.cancelBubble = true;\"
						width=100% border=0 cellspacing=0 cellpadding=0>";
			echo "<tr>
				<td nowrap>
				<div  id='id_td_$tab_name' style='display:none;visibility:hidden;position:absolute;'>
					<table border='1' cellspacing='0' cellpadding='0' width=100%>
						<tr>
						 <td nowrap bgcolor=\"$bg_color\" class=k_td> $new $buttons $main_buttons </td>
						 $info $visibility $delete
						</tr>
					</table>
				</div>
				</td>
			  </tr>";

		}
		else 
		{

			
			echo "
      <div title=\"".$WEBTD->sid.",".$WEBTD->pri."\" \"".$classdrag."\">
      <a name=\"$hash\"></a>
					<table id=\"$tab_name\"
					onMouseOver=\"kameleon_mouse_event(this,'borderColor','$SERVER->editbordercolor',1)\" 
					onMouseOut=\"kameleon_mouse_event(this,'borderColor','#$WEBPAGE->bgcolor',0)\"
					width=\"100%\" border=1 cellspacing=0 cellpadding=0 bordercolor=\"#$WEBPAGE->bgcolor\">";
			//$adodb->puke($WEBTD);
			//$adodb->puke($WEBPAGE);
			echo "<tr>
					<script language=\"Javascript\">
						identyfiersArray['".$WEBTD->sid."']='".$WEBTD->uniqueid."';
					</script>
					<td bgcolor=white>
					<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">
						<tr>
						 <td bgcolor=\"$bg_color\" class=\"k_td\" id=\"".$domenuId."\" $onContextMenu>$buttons</td>
						 $svn_cmd $info $visibility $delete 
						</tr>
					</table>
					</td>
				  </tr>";
		}
		$align="";$valign="";$class="";$width="";
		if (strlen($WEBTD->align)) $align="align=\"$WEBTD->align\"";
		if (strlen($WEBTD->valign)) $valign="valign=\"$WEBTD->valign\"";
		if (strlen($WEBTD->width)) $width="width=\"$WEBTD->width\"";
		echo "<tr><td $align $valign $width id=\"cid_td_$tab_name\">
					<div id=\"div_td_$tab_name\">\n";
	}

	//***** Teraz zamiana UIMAGES w czasie publikacji

	if (ereg("<[^>]+=\\\\\"[^>]*>",$WEBTD->plain)) $WEBTD->plain = stripslashes( $WEBTD->plain );

	if (eregi('<img[^>]* src="[\.\/]+img/include.gif"[^>]*>',$WEBTD->plain))
	{
		$WEBTD->plain=kameleon_include_plain($WEBTD->plain);
	}

	if (!$KAMELEON_MODE)
	{
		$WEBTD->plain=ereg_replace($EREG_REPLACE_KAMELEON_UIMAGES,$UIMAGES,$WEBTD->plain);
		$WEBTD->plain=ereg_replace($KAMELEON_UFILES,$UFILES,$WEBTD->plain);
	}


	if ($C_EDITOR_FORM)
	{
		$n=$WEBTD->next;
		if (!$n) $n=$page;

		if (strstr(strtolower($WEBTD->plain),'<form')) $kameleon_editor_form_counetr++;
		$n=kameleon_href('','',$n);
		if (!ereg("<form[^>]+action=[^>]+>",$WEBTD->plain)) $WEBTD->plain=eregi_replace("<(form[^>]+)>","<\\1 action=\"$n\">",$WEBTD->plain);
		if (!ereg("<form[^>]+id=[^>]+>",$WEBTD->plain)) $WEBTD->plain=eregi_replace("<(form[^>]+)>","<\\1 id=\"kameleon_form_sid_".$WEBTD->sid."\">",$WEBTD->plain);
		if (!ereg("<form[^>]+name=[^>]+>",$WEBTD->plain)) $WEBTD->plain=eregi_replace("<(form[^>]+)>","<\\1 name=\"kameleon_form_".$kameleon_editor_form_counetr."\">",$WEBTD->plain);
	}




	//******* Znajdowanie linków wewnêtrznych
	
	$kameleon_inside_link="kameleon:inside_link(";
	
	$plain=strtolower($WEBTD->plain);

	while( strstr($plain,$kameleon_inside_link) )
	{

		$pos=strpos($plain,$kameleon_inside_link);

		$end=strpos(substr($plain,$pos),")");
		$page_target=substr($plain,$pos+strlen($kameleon_inside_link),$end-strlen($kameleon_inside_link));

		$pt=explode(',',$page_target);

		$href=kameleon_href("",$pt[1],$pt[0]);

		$WEBTD->plain=substr($WEBTD->plain,0,$pos)."$href".substr($WEBTD->plain,$pos+$end+1);		
		
		$plain=strtolower($WEBTD->plain);
	}


	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_from)
		echo "\n<?php if (".$WEBTD->nd_valid_from."<time()) { ?>\n";

	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_to)
		echo "\n<?php if (time()<".$WEBTD->nd_valid_to.") { ?>\n";


	if (!$WEBTD->hidden && !$this_editmode && ($WEBTD->ob & 1) )
	{
		if ($KAMELEON_MODE || $WEBTD->staticinclude) include('remote/ob_start.h');
		else echo read_file('remote/ob_start.h');
	}

	if (!$WEBTD->hidden || $this_editmode) 
	{
		if ($WEBTD->swfstyle)
		{
			global $ORYGINAL_WEBTD;
			$ORYGINAL_WEBTD=$WEBTD;

			if (strlen($WEBTD->html)) $WEBTD->staticinclude=1;
	
			$plain=kameleon_td2swf_obj($WEBTD);

			$WEBTD->plain=$plain;
			$WEBTD->menu_id='';
			$WEBTD->html='';
			$WEBTD->api='';	
			$WEBTD->title='';	
			$WEBTD->more='';	
			$WEBTD->next='';	
			$WEBTD->img='';	
			$WEBTD->bgimg='';
		}
	
		if ($CONST_PARSER_INTEGRATED) 
		{
			include("include/parser_td.h");
		}
		else
		{
			include("$SZABLON_PATH/td.h");
		}
		
	}
	$ORYGINAL_WEBTD=null;

	if (!$WEBTD->hidden && !$this_editmode && ($WEBTD->ob & 2) )
	{
		if (($WEBTD->ob & 1) && ($WEBTD->ob & 2) && strlen($WEBTD->api) && !strstr($WEBTD->api,'kameleon') ) 
		{
			if ($KAMELEON_MODE || $WEBTD->staticinclude) ob_end_clean();
			else echo '<? ob_end_clean(); ?>';
			
		}
		else
		{
			if ($KAMELEON_MODE || $WEBTD->staticinclude) include('remote/ob_end.h');
			else echo ereg_replace("[\r\n\t]+"," ",read_file('remote/ob_end.h'));
		}
	}

	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_from)
		echo "\n<?php } ?>\n";
	if (!$this_editmode && !$WEBTD->hidden && $WEBTD->nd_valid_to)
		echo "\n<?php } ?>\n";
	
	if ($this_editmode )
	{
		echo "\n</div></td></tr></table></div>\n";	
	}
}
$this_editmode=pop();

?>
