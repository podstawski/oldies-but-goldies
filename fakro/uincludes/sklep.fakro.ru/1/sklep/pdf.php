<?
	$_more="";

	$costxt=explode(":",$costxt);
	$id=$costxt[1];
	$costxt=$costxt[0];

	if (strlen($id)) $_more="&list[id]=$LIST[$id]";

	$alts=sysmsg("Save PDF file","system");
	$altp=sysmsg("Print PDF","system");


	echo "<a href=\"$self${next_char}action=PDF&list[pdf]=$costxt&list[prn]=1$_more\" target=\"pdf\"><img
			src=\"$SKLEP_IMAGES/sb/pdfprint.gif\" alt=\"$altp\" border=0></a>
			<a href=\"$self${next_char}action=PDF&list[pdf]=$costxt&list[prn]=0$_more\"><img
			src=\"$SKLEP_IMAGES/sb/pdfsave.gif\" alt=\"$alts\" border=0></a>
			";

?>
