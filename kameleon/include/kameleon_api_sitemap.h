<?	include ("include/tree_js.h"); ?>
<?

	$imgs=array("tree_menu_e.gif",
				"tree_menu.gif",
				"tree_strona.gif",
				"tree_plus_e.gif",
				"tree_plus.gif",
				"tree_linia.gif",
				"tree_serwis.gif",
				"tree_folder.gif",
				"tree_minus.gif",
				"tree_minus_e.gif");

	if ($KAMELEON_MODE)
	{		
		if (!file_exists("$UIMAGES/api")) mkdir ("$UIMAGES/api",0755);
		for ($i=0;$i<count($imgs);$i++)
		{
			$plik=$imgs[$i];
			if (!file_exists("$UIMAGES/api/$plik") ) 
				copy ("img/$plik", "$UIMAGES/api/$plik");

		}
	}

	global $TreeFollowLink,$TreeDontShowPageNumber,$IMG;

	$TreeFollowLink=1;
	$TreeDontShowPageNumber=1;
	$IMG="$UIMAGES/api";

	
	include ("include/tree_fun.h");
	drzewo(-1,$page,$lang);
?>
