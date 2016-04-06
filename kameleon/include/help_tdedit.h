<table cellspacing=0>
<tr><td valign="top" colspan=2 class="k_a" style="text-decoration:underline"><?echo label("Basic commands")?></td></tr>

<? if ($C_SHOW_TD_TITLE) { ?>
<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_title_input'],'<? echo label("Mark the text and insert new title")?>',390,10)">
		<? echo label("Section title change")?>
	</td>
</tr>
<?}?>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="Wait4Bookmark(document.all['wysiwyghtml'],'<? echo label("Click at the field and start typing")?>',20,30,'wysiwyghtml',this)">
		<? echo label("Content entering")?>

	</td>
</tr>


<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="Wait4Bookmark(document.all['help_image_icon'],'<? echo label("Click to open graphic files library")?>',14,10,'wysiwyghtml',this)">
		<? echo label("Inserting graphics")?>

	</td>
</tr>



<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand" 
		onclick="Wait4Bookmark(document.all['help_ilink_icon'],'<? echo label("Mark a piece of text and click to choose target page")?>',-3,10,'wysiwyghtml',this)">
		<? echo label("Link to another page inside website")?>
</td>
</tr>
<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand" 
		onclick="Wait4Bookmark(document.all['help_olink_icon'],'<? echo label("Mark a piece of text and click to insert target URL address")?>',-3,10,'wysiwyghtml',this)">
		<? echo label("Link to another website")?>
</td>



<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_td_save'],'<? echo label("Click to save changes and close the editor")?>',-5,10)">
		
		<? echo label("Save changes")?>
	</td>
</tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_navi_page'],'<? echo label("Click to close the editor and save no changes")?>',-5,10)">
		<? echo label("Exit without saving changes")?>
	</td>
</tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand" 
		onclick="Wait4Bookmark(document.all['menu_id'],'<? echo label("Click and choose menu to insert")?>',-1,5,'advanced',this)">
		<? echo label("Inserting menu")?>

</td>
</tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand" 
		onclick="Wait4Bookmark(document.all['type'],'<? echo label("Click to modify section layout")?>',-1,5,'advanced',this)">
	<? echo label("Change layout")?>

	</td>
</tr>



<? if($C_SHOW_TD_LEVEL) {?>
<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand" 
		onclick="Wait4Bookmark(document.all['level'],'<? echo label("Click to modify section location")?>',-1,5,'advanced',this)">
	<? echo label("Change location")?>

	</td>
</tr>
<?}?>

</tr>


</table>


<script>
	var _time_action_permited=1;
	var _obj_referer;
	var _bookmark;

	function Wait4Bookmark(obj,txt,x,y,bookmark_name,referer)
	{
		if (_time_action_permited && bookmark_name.length)
		{
			_bookmark=bookmark_name;
			_obj_referer=referer;
			if (bookmark_name=='advanced') bookmark_id='zakMiddleAdv';
			if (bookmark_name=='wysiwyghtml') bookmark_id='zakMiddleWysiwyg';
		}

		if (document.all[_bookmark].style.display == 'none')
		{
			if (_time_action_permited)
			{
				_time_action_permited=0;		
				KameleonHelp(document.all[bookmark_id],'<? echo label("first click this bookmark ...")?>',-3,12);
			}
			setTimeout(Wait4Bookmark,500);
			return;
		}

		if (_time_action_permited) KameleonHelp(obj,txt,x,y);
		else
		{
			_time_action_permited=1;
			_obj_referer.click();
		}
	}
</script>
