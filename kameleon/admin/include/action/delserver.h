<?
	$action="";

	$server+=0;

   	$query = "SELECT nazwa FROM servers WHERE id=$server";
	parse_str(ado_query2url($query));
	
	$query="DELETE FROM servers WHERE id=$server; 
		 DELETE FROM rights WHERE server=$server;
		 DELETE FROM webpage WHERE server=$server;
		 DELETE FROM weblink WHERE server=$server;
		 DELETE FROM webtd WHERE server=$server;";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		if (!is_link("../uimages")) system ("rm -rf ../uimages/$server");

   		$query = "
     			DELETE FROM kontakt WHERE servername='$nazwa';
     			DELETE FROM ksiega WHERE servername='$nazwa';
     			DELETE FROM ksiega_ustawienia WHERE servername='$nazwa';
     			DELETE FROM ogloszenia WHERE servername='$nazwa';
     			DELETE FROM search_desc WHERE servername='$nazwa';
     			DELETE FROM search_index WHERE servername='$nazwa';
     			DELETE FROM search_slownik WHERE servername='$nazwa';
     			DELETE FROM search_ustawienia WHERE servername='$nazwa';
			DELETE FROM webaktual WHERE servername='$nazwa';
     			DELETE FROM polecam WHERE servername='$nazwa';
     			DELETE FROM forum WHERE servername='$nazwa';
     			DELETE FROM forum_ustawienia WHERE servername='$nazwa';
     			DELETE FROM counter WHERE servername='$nazwa';
			";
		$adodb->Execute($query);
	}
?>
