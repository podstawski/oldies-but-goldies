<?
	$p=fopen('/tmp/shop.fakrousa.com','w');
	fwrite ($p,"$SKLEP_INCLUDE_PATH/templates.html/".$foundfilename."_$za_status.main");	
	
	if (file_exists("$SKLEP_INCLUDE_PATH/templates.html/${foundfilename}_$za_status.main"))
		$foundfilename="${foundfilename}_$za_status";

	fwrite ($p,"\n $foundfilename");
	fclose($p);
?>