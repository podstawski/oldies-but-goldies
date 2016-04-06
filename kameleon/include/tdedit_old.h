<?
  include("include/helpbegin.h");

  if (!$editmode) $WEBPAGE->title=label("WebKameleon Editor");



  if (!strlen($page)) $page=$page_id;

  include("include/userclass.h");
  include("include/usercolors.h");

  $td_width+=0;
  if (!$td_width) $td_width="100%";
  else
	$td_width+=34; //bo trzeba dodac padding

  if (strlen($u_color) && $u_color[0]!="#")  $u_color="#$u_color";

  $aligns[]=array("",label("Choose"));
  $aligns[]=array("left",label("Align left"));
  $aligns[]=array("right",label("Align right"));
  $aligns[]=array("center",label("Center"));
  $aligns[]=array("justify",label("Justify"));

  $valigns[]=array("",label("Choose"));
  $valigns[]=array("top",label("Top"));
  $valigns[]=array("middle",label("Middle"));
  $valigns[]=array("bottom",label("Bottom"));

  $filename = "$UIMAGES/$DEFAULT_TEXTFILE_CSS";
  if (!file_exists($filename)) $filename = "$IMAGES/textstyle.css";
  $fd = fopen( $filename, "r" );
  $stylecontent = @fread( $fd, filesize( $filename ) );
  fclose( $fd ); 

  //if ($BASIC_RIGHTS) $UIMAGES.="/$USERNAME";
  
  $label_link=label("Issue page number");

  $my_host="http";
  if ($HTTPS) $my_host.="s";
  $my_host.="://$HTTP_HOST";
  if (dirname($SCRIPT_NAME)!="." && dirname($SCRIPT_NAME)!="/") $my_host.=dirname($SCRIPT_NAME);

?>
<SCRIPT>
	UIMAGES='<? echo "$my_host/$UIMAGES"?>'; 
	TDEDIT_WIDTH='<?echo $td_width?>'; 
	TEXTSTYLE='<?echo $filename;?>';
	UFILES='<? echo "$my_host/$UFILES"?>';
	label_prompt_cols='<? echo label("Issue number of columns");?>';
	label_prompt_rows='<? echo label("Issue number of rows");?>';
	label_prompt_width='<? echo label("Issue table width (%)");?>';
	label_prompt_border='<? echo label("Issue table border width");?>';
</SCRIPT>

<?	include ("include/tree_js.h"); ?>


<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
<? if ($editmode) {?>
<tr>
 <td>
  <? if ($CONST_MODE!="express") include("include/navigation.h");?>
 </td>
</tr>
<?}?>
<tr>
 <td>    
 <table bgcolor="silver" valign=top width="100%" border="3" cellspacing="0" cellpadding="0">
<?
	if (strtolower($pri)=="max")
	{
		$query="SELECT max(pri) AS _pri FROM webtd
			WHERE  server=$SERVER_ID AND ver=$ver 
			AND lang='$lang' AND page_id=$page_id";
		parse_str(ado_query2url($query));		
		$pri=$_pri;
	}
?>



 <form method=post action=index.<?echo $KAMELEON_EXT?>#<?echo $hash?> name="edytujtd" id="edytujtd_id" onSubmit="ZapiszZmiany()">
  <input type=hidden name=page_id value="<?echo $page_id?>">
  <input type=hidden name=page value="<?echo $page?>">
  <input type=hidden name=pri value="<?echo $pri?>">
  <input type=hidden name=action value="">
  
<?

	if ($editmode)
	{
		
		$query="SELECT * FROM webtd
			WHERE  server=$SERVER_ID AND ver=$ver 
			AND lang='$lang' AND page_id=$page_id AND pri=$pri";

		parse_str(ado_query2url($query));

		echo "<input type=\"hidden\" name=\"tdsid\" value=\"$sid\">";
	}

	$strpslashes_needed = ini_get('magic_quotes_gpc') ;

	if ($strpslashes_needed) $title=stripslashes($title);
	if ($strpslashes_needed) $plain=stripslashes($plain);
	if ($strpslashes_needed) $costxt=stripslashes($costxt);
	if (ereg("<[^>]+=\\\\\"[^>]*>",$plain)) $plain=stripslashes($plain);


	if ($html[0]=="@")
	{
		$module=$html;
		$html="";

		$obj=html_txt2html_obj($module);

		$bn=basename($module);
		$dn=dirname($module);
		$m_name=substr($dn,1);

		if (is_Object($obj))
		{
			$MODULE_NAME=kameleon_global($obj->label);
			$MODULE_PATH="modules/$dn";
			if (file_exists("modules/$dn/.$bn")) $MODULE_ADV="modules/$dn/.$bn";
			if (is_array( $obj->var ))
			{
				reset ($obj->var);
				while ( list( $v_key, $v_val ) = each($obj->var) )
				{
					$cmd="\$$v_key = \"$v_val\" ; ";
					eval($cmd);
				}
			}
		}
	}

?>
<tr>
	<td align="left" width="100%" background="zakl01n_back.gif"></td>
</tr>
<? if ($C_SHOW_TD_TITLE && $editmode) 
	{ 
		$input_title_id=$helpmode?"id=\"help_title_input\"":"";
		if ($CONST_MODE!="express")
		{
?>
<tr>
	  <td class=k_td><?echo label("Title")?>:<br><input style='width:<?echo $td_width?>;' class='k_input' type=text name=title value="<?echo ereg_replace("\"","&quot;",$title)?>" <?echo $input_title_id?>></td>
</tr>
<?	
		}	
	}
?>
 <tr>
  <td>

<?
	include_js("tdedit_old");
?>

<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
 <tr>
  <td align="left" width="100%" background="zakl01n_back.gif">
    <!-- ZAKLADKI -->
     <table border="0" cellspacing="0" cellpadding="0">
         <tr>
			<? if ($CONST_MODE!="express") {?>

				<td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img ></td>
				<td background=img/zakl01n_middle.gif nowrap>
					<? 
						if ($editmode) $zapisz_zmiany="ZapiszZmiany()";
						else $zapisz_zmiany="ZapiszZmianyZamknij()";

						$save_href_id=$helpmode?"id=\"help_td_save\"":"";
					?>
					<a href="javascript:<?echo $zapisz_zmiany?>" <?echo $save_href_id?> class=k_zakl01>&nbsp;<?echo label("Save and exit")?>&nbsp;</a>
				</td>
				<td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img ></td>

			
				<td><img src=img/zakl01a_left.gif width=2 height=20 border=0 name=zakLeftWysiwyg></td>
				<td id=zakMiddleWysiwyg background=img/zakl01a_middle.gif nowrap><a href="javascript:showWysiwyg()" class=k_zakl01>&nbsp;<?echo label("WYSIWYG")?>&nbsp;</a></td>
				<td><img src=img/zakl01a_right.gif width=2 height=20 border=0 name=zakRightWysiwyg></td>

				<td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img  name=zakLeftHtml></td>
				<td id=zakMiddleHtml background=img/zakl01n_middle.gif nowrap><a href="javascript:showHtml()" class=k_zakl01>&nbsp;<?echo label("HTML")?>&nbsp;</a></td>
				<td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img  name=zakRightHtml></td>
			<?}?>
			<? if ($editmode && $CONST_MODE!="express") {?>
				<td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img   name=zakLeftAdv></td>
				<td id=zakMiddleAdv background=img/zakl01n_middle.gif nowrap>
					<a href="javascript:showAdvanced()" class=k_zakl01>&nbsp;<?echo label("Options")?>&nbsp;</a>
				</td>
				<td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img   name=zakRightAdv></td>
			<?}?>

			<? if (strlen($MODULE_ADV) && $CONST_MODE!="express") {?>
	            <td><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img  name=zakLeftModAdv></td>
		        <td id=zakMiddleModAdv background=img/zakl01n_middle.gif nowrap><a href="javascript:showModAdv(0)" class=k_zakl01>&nbsp;<?echo strlen($MODULE_NAME)?$MODULE_NAME:label("Module options")?>&nbsp;</a></td>
			    <td><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img  name=zakRightModAdv></td>
			<?} else {?>
		        <td style="visibility:hidden;display:none" id=zakMiddleModAdv><img src=img/zakl01n_left.gif width=2 height=20 border=0 class=k_img  name=zakLeftModAdv><img src=img/zakl01n_right.gif width=2 height=20 border=0 class=k_img  name=zakRightModAdv></td>
			<?} ?>

			<? if ($CONST_MODE!="express") {?>

            <td width="100%" align="right" class="k_td">
            <?
                if (strlen($menu_id)>0) echo "<b>".label('Menu').": <a href=menus.$KAMELEON_EXT?menu=$menu_id>$menu_id</a></b>&nbsp;&nbsp;"; 
                if (strlen($html)>0) echo label('Plik').": $html&nbsp;&nbsp;"; 
            ?>
            </td>
			<?	}?>
		
		</tr>
        </table>
       <!-- ZAKLADKI END-->
    </td>
 </tr>
</table>
<table id=toolbar border=0 cellpadding=0 cellspacing=0>
<tr>
 <td>
  <!-- <img src=img/i_save_n.gif onClick="ZapiszZmiany()" style="cursor:hand;" border=0 alt='<?echo label("Save changes")?>' width=23 height=22>
  <img src=img/i_separator.gif border=0 width=4 height=22> -->
  <img class=k_imgbutton src=img/i_cut_n.gif onClick="styl('cut');" style="cursor:hand;" border=0 alt='<?echo label("Cut")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_copy_n.gif onClick="styl('copy')" style="cursor:hand;" border=0 alt='<?echo label("Copy")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_paste_n.gif onClick="styl('paste')" style="cursor:hand;" border=0 alt='<?echo label("Paste")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_bold_n.gif onClick="styl('bold');" style="cursor:hand;" border=0 alt='<?echo label("Bold")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_italic_n.gif onClick="styl('italic')" style="cursor:hand;" border=0 alt='<?echo label("Italic")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_underline_n.gif onClick="styl('underline')" style="cursor:hand;" border=0 alt='<?echo label("Underline")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_left_n.gif onClick="styl('justifyleft')" style="cursor:hand;" border=0 alt='<?echo label("Align left")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_center_n.gif onClick="styl('justifycenter')" style="cursor:hand;" border=0 alt='<?echo label("Center")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_right_n.gif onClick="styl('justifyright')" style="cursor:hand;" border=0 alt='<?echo label("Align right")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_dentin_n.gif onClick="styl('indent')" style="cursor:hand;" border=0 alt='<?echo label("Increase indent")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_dentout_n.gif onClick="styl('outdent')" style="cursor:hand;" border=0 alt='<?echo label("Decrease indent")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_ul_n.gif onClick="styl('insertunorderedlist')" style="cursor:hand;" border=0 alt='<?echo label("Bulleted list")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_ol_n.gif onClick="styl('insertorderedlist')" style="cursor:hand;" border=0 alt='<?echo label("Numbered list")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_outeranchor_n.gif onClick="kameleon_outer_link('<?echo $label_link?>')" style="cursor:hand;" border=0 alt='<?echo label("Create or edit outside hyperlink")?>' width=23 height=22 id="help_olink_icon">
  <?if ($editmode) {?>
  <img class=k_imgbutton src=img/i_inneranchor_n.gif onClick="kameleon_inner_link('<?echo $label_link?>')" style="cursor:hand;" border=0 alt='<?echo label("Create or edit inside hyperlink")?>' width=23 height=22 id="help_ilink_icon">
  <?}?>
  <?if ($editmode || strlen($SetServer) ) 
  {
	   $i_image_id=$helpmode?"id=\"help_image_icon\"":"";
		
	?>
  <img class=k_imgbutton <?echo $i_image_id?> src=img/i_image_n.gif onClick="obrazekGaleria('<?echo "ufiles.$KAMELEON_EXT?page=$page&galeria=2"?>')" style="cursor:hand;" border=0 alt='<?echo label("Insert or edit image")?>' width=23 height=22>
  <?} else {?>
  <img class=k_imgbutton src=img/i_image_n.gif onClick="obrazekBezGalerii()" style="cursor:hand;" border=0 alt='<?echo label("Insert or edit image")?>' width=23 height=22>
  <?}?>
  <img class=k_imgbutton src=img/i_file_n.gif onClick="plikGaleria('<?echo "ufiles.$KAMELEON_EXT?page=$page&galeria=1"?>')" style="cursor:hand;" border=0 alt='<?echo label("Attach file")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_table_n.gif onClick="insertTable()" style="cursor:hand;" border=0 alt='<?echo label("Insert simple table")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_bookmark_n.gif onClick="styl('createbookmark',prompt('Podaj nazwê ?','name')),true" style="cursor:hand;" border=0 alt='<?echo label("Bookmark")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_hr_n.gif onClick="styl('inserthorizontalrule')" style="cursor:hand;" border=0 alt='<?echo label("Horizontal line")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <img class=k_imgbutton src=img/i_resize_n.gif onClick="td_resize(this)" style="cursor:hand;" border=0 alt='<?echo label("Switch edytor width")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_nostyle_n.gif onClick="styl('RemoveFormat')" style="cursor:hand;" border=0 alt='<?echo label("Remove format")?>' width=23 height=22>
  <img class=k_imgbutton src=img/i_separator.gif border=0 width=4 height=22>
  <a href="<?echo $HELP_LINK?>"><img src=img/i_help_n.gif alt="<? echo label("Help")?>" border=0 width=23 height=22></a>


 </td>
</tr>
<tr>
 <td valign=top>
        <select class=k_select onChange="styl('fontSize', this[this.selectedIndex].value);this.selectedIndex=0"> 
        <option class="k_option" selected><?echo label("Size")?></option>
        <option class="k_option" value="1">1</option>
        <option class="k_option" value="2">2</option>
        <option class="k_option" value="3">3</option>
        <option class="k_option" value="4">4</option>
        <option class="k_option" value="5">5</option>
        <option class="k_option" value="6">6</option>
        <option class="k_option" value="7">7</option>
        </select>
        <select class=k_select onChange="styl('fontname', this[this.selectedIndex].value);this.selectedIndex=0"> 
        <option class="k_option" selected><?echo label("Font")?></option>
        <option class="k_option" value="Arial">Arial</option>
        <option class="k_option" value="Courier">Courier</option>
        <option class="k_option" value="Courier New">Courier New</option>
        <option class="k_option" value="Thaoma">Thaoma</option>
        <option class="k_option" value="Times New Roman">Times New Roman</option>
        <option class="k_option" value="Verdana">Verdana</option>
        </select>		
<?if ($CONST_MODE!="express") {?>
        <select class=k_select onChange="insertClass(this[this.selectedIndex].value);this.selectedIndex=0"> 
<?
        for ($c=0;$c<count($USERCLASS);$c++)
        {
         $row=$USERCLASS[$c];
         $kam_klasy=$row[0];
         if ($c==0)
         	echo "<option value=''>".label("Choose style")."</option>\n";
         else
			echo "<option value='$kam_klasy'>$kam_klasy</option>\n";
        }
?>
        </select>
<? } //if ($CONST_MODE!="express")?>

		<select class="k_select" onChange="styl('forecolor', this[this.selectedIndex].value);this.selectedIndex=0"> 
        <option class="k_option" selected style="background-color:white;" value=""><?echo label("Color")?></option>
<? 


 $len=count($USERCOLORS);
 $mod=1;
 for ($i=0;$i<$len;$i++)
 {
  $color=$USERCOLORS[$i];
  $option.="<option class=\"k_option\" style='background-color:$color' value='$color'>&nbsp;&nbsp;</option>\n";
 }
 echo $option;
?>
        </select>			  
        <img src=img/i_colors_n.gif onClick="openColors('#FFFFFF','<?echo $KAMELEON_EXT?>')" style="cursor:hand;" onmouseover="this.src='img/i_colors_a.gif'" onmouseout="this.src='img/i_colors_n.gif'" border=0 alt='<?echo label("Text color")?>'  width=23 height=22 align=absmiddle>
 </td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 ID="roolerbar">
<? if ($CONST_MODE!="express") {?>
<tr>
 <td background="img/podzialka.gif">
    <img src="img/spacer.gif" width=1 height=19 border=0>
 </td>
</tr>
<? }
	if ($CONST_MODE=="express") {?>
<tr>
<td class=k_td><?echo label("Title")?>:<br><input style='width:<?echo $td_width?>;border-style:none;' type=text name=title value="<?echo ereg_replace("\"","&quot;",$title)?>" <?echo $input_title_id?>></td>
</tr>
<?	}?>
</table>

<table bgcolor="silver" width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
 <td>
  <table ID="wysiwyghtml" style="display:inline;visibility:visible" border=0 cellspacing=0 cellpadding=0 width='100%' bordercolor=<?echo $SERVER->editbordercolor?>>
    <tr>
    	<td  height=400>
			<textarea style="display:none;visibility:hidden;width:100%;height:100%;border-style:none;" wrap=virtual name=plain><?echo $plain?></textarea>
			<iframe style="display:inline;visibility:visible;width:<?echo $td_width?>;height:100%;z-index:50" scrolling="yes" id="edytor" frameborder=0></iframe>
			<script language="javascript">
				frames.edytor.document.designMode = 'on';
				window.setTimeout("init()",200);
			</script>
    	</td>
    </tr>
  </table>
<?
	include('include/tdedit_opt.h');
?>  


 </form>
 </table>
 </td>
</tr>

</table>


<?
if ( is_Array($C_MODULES) ) { ?>

<script>
old_selection=document.edytujtd.module.selectedIndex;
function module_selected()
{
	if (document.edytujtd.module.selectedIndex!=0) document.edytujtd.html.value="";
	if (document.edytujtd.module.value=="-") 
		document.edytujtd.module.selectedIndex=old_selection;	
	else
		old_selection=document.edytujtd.module.selectedIndex;
}
</script>

<? } 


  include("include/helpend.h");
?>


</body>
</html>

<?

	if ($C_HIDE_WYSIWYG)
		echo "<script>
				document.zakLeftWysiwyg.style.display='none';
			    document.all['zakMiddleWysiwyg'].style.display='none';
				document.zakRightWysiwyg.style.display='none';
				</script>";

	if ($C_HIDE_HTML)
		echo "<script>
        document.zakLeftHtml.style.display='none';
        document.all['zakMiddleHtml'].style.display='none';
        document.zakRightHtml.style.display='none';
				</script>";

	if (strlen($MODULE_ADV)) echo "<script> showModAdv(1); </script>";
?>