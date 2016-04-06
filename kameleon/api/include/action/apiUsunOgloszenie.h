<?
 if ($SERVICE!="ogloszenia")
 {
  $api_action="";
	return;
 }

 $api_action="";
 
 $query="DELETE FROM ogloszenia WHERE servername='$KEY' AND id=$api_id ";
	
 //echo nl2br($query);return;
 $adodb->Execute($query);

?>