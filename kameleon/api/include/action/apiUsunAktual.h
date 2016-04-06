<?
 if ($SERVICE!="news")
 {
  $api_action="";
	return;
 }

 $api_action="";
 
 $query="DELETE FROM webaktual WHERE servername='$KEY' AND pri=$api_pri ";
	
 //echo nl2br($query);return;
 $adodb->Execute($query);

?>