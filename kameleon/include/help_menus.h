<?

	if (!$menu) return;

	$query="SELECT count(*) AS menu_items
		FROM weblink WHERE server=$SERVER_ID and menu_id=$menu
		AND ver=$ver AND lang='$lang'";

	parse_str(ado_query2url($query));

	
?>

<table cellspacing=0>
<tr><td valign="top" colspan=2 class="k_a" style="text-decoration:underline"><?echo label("Basic commands")?></td></tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_menu_new_item'],'<? echo label("Click to add new item")?>',-7,12)">
		<? echo label("New item inserting")?>
	</td>
</tr>

<? if ($menu_items) {?>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_menu_edit_item'],'<? echo label("Click to edit item")?>',-7,12)">
		<? echo label("Item change")?>
	</td>
</tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_menu_pri_item'],'<? echo label("Click to move item up or down")?>',13,17)">
		<? echo label("Item sequence change")?>
	</td>
</tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_menu_target_item'],'<? echo label("Click to change the target page inside this website")?>',-5,10)">
		<? echo label("Target change")?>
	</td>
</tr>



<? }?>
</table>

