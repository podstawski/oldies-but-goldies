<?
 if ($SERVICE!="kontakt")
 {
  $api_action="";
  return;
 }

 $api_action="";

 $query="DELETE FROM kontakt WHERE servername='$KEY' AND id=$api_id ";
 //echo nl2br($query);return;
 $adodb->Execute($query);

?>