<table cellspacing=0>

<tr><td valign="top" colspan=2 class="k_a" style="text-decoration:underline"><?echo label("Basic commands")?></td></tr>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(ClosestTD(),'<? echo label("Click and wait for the editor to open")?>',35,14)">
		<? echo label("Text and graphics update")?>
	</td>
</tr>


<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_new_page'],'<? echo label("Click here to create a new page")?>',15,14)">
		<? echo label("New page creation")?>
	</td>
</tr>






<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_page_property_icon'],'<? echo label("Click and submit new page title")?>',14,10)">
		<? echo label("Page title change")?>
	</td>
</tr>






<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(ClosestTD(),'<? echo label("Click the icon and get rid of the whole block of text")?>',-12,10)">
		<? echo label("Module delete")?>
	</td>
</tr>

<?if ($CONST_MODE!="express") { ?>

<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_page_hfswitch_icon'],'<? echo label("Click to change header or footer mode")?>',14,10)">
		<?
			if ($hf_editmode)
				echo label("Content change");
			else
				echo label("Header/footer change");

		?>
	</td>
</tr>
<?}?>
<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_page_preview_icon'],'<? echo label("Click to see published page preview")?>',14,10)">
		<? echo label("Page preview")?>

	</td>
</tr>

<? if ($FTP_RIGHTS) { ?>
<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_navi_ftp'],'<? echo label("Click to publish website to the Internet")?>',-5,10)">
		<? echo label("Website publishing")?>
		
	</td>
</tr>
<? } ?>
<tr>
	<td valign="top"><img src="<?echo $icon?>" align="absMiddle"></td>
	<td class="k_a" style="cursor:hand"
		onclick="KameleonHelp(document.all['help_explorer_bm'],'<? echo label("Click to open website explorer")?>',-5,10)">
		<? echo label("Website structure")?>
	</td>
</tr>

</table>

