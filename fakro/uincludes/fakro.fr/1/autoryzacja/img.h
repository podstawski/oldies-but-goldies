<?
	if (!file_exists("$UIMAGES/autoryzacja") && $KAMELEON_MODE)
	{
		mkdir("$UIMAGES/autoryzacja",0755);
		foreach (array("i_delete_n.gif","i_editmode_n.gif","i_file_n.gif") AS $img)
		{
			copy("img/$img","$UIMAGES/autoryzacja/$img");
		}
	}
	$AUTHIMG="$UIMAGES/autoryzacja";
?>