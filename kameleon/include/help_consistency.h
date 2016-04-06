<table cellspacing=0>
<td valign="top" colspan=2 class="k_a" style="text-decoration:underline"><?echo label("Basic commands")?></td>


<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="ConsistencyHelp(document.all['m_0_<?echo $lang?>'],'<? echo label("Right mouse button click and then click the icon")?>',40,18)">
		<? echo label("New page creation")?>
	</td>
</tr>


<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="ConsistencyHelp(document.all['m_0_<?echo $lang?>'],'<? echo label("Right mouse button click and then click the icon")?>',70,18)">
		<? echo label("Page title change")?>
	</td>
</tr>


<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="ConsistencyHelp(document.all['m_0_<?echo $lang?>'],'<? echo label("Right mouse button click and then click the icon")?>',220,18)">
		<? echo label("Page erasing")?>
	</td>
</tr>



</table>


<script>
	function ConsistencyHelp(obj,label,x,y)
	{
		
		KameleonHelp(obj,label,x,y);
		expmenu(0,'<?echo $lang?>');
		pos=getHelpAbsolutePos(obj);
		document.all['oMenu'].style.left=pos.x+30;
		document.all['oMenu'].style.top=pos.y+5;
	}
</script>