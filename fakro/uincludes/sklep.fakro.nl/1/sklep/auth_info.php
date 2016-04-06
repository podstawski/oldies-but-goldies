<?
	if ($AUTH[id] <= 0) 
	{
		echo "<a href=\"$next\"><img src=\"$UIMAGES/system/login.gif\" border=0 align=absMiddle>".sysmsg("login","system")."</a>";
		return;
	}
	echo "<a href=\"$next?action=logout\"><img src=\"$UIMAGES/system/logout.gif\" border=0 align=absMiddle>".sysmsg("logout","system")."</a>";

	$link=$AUTH[imiona]." ".$AUTH[nazwisko];
	if (strlen($AUTH[nazwa])) $link.=" / ".$AUTH[nazwa];
	echo " | <a href=\"$more\">$link</a> ";
	
?>
