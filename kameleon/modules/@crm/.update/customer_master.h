<?
	if (module_update($MODULES->crm->files->customer_master))
	{

		$title=toText($CUSTOMER[c_name]);

		$q="UPDATE webpage SET title='$title' 
			 WHERE lang='$lang' AND
			 id=$page_id AND ver=$ver AND server=$SERVER_ID ;
			UPDATE weblink SET alt='$title' 
			 WHERE lang='$lang' AND
			 page_target=$page_id AND ver=$ver AND server=$SERVER_ID ;";

		if ($adodb->Execute($q)) logquery($q);
	}
	

?>