<?
 if ($SERVICE!="news")
 {
  $api_action="";
	return;
 }

 if (!validateDate($api_data))
 {
  $api_action="";
  $err=1;
  echo "<script>alert('".label("Invalid date format [dd-mm-yyy]")."')</script>";
 }
 if ($err) return;
 $api_data=FormatujDateSQL($api_data);

 $api_headline=validateText($api_headline);
 $api_akt=validateText($api_akt);
 $api_more=validateText($api_more);
 $api_img=validateText($api_img);

 if (!validateEmail($apiKontaktEmail))
 {
  $api_action="";
  $err=1;
  echo "<script>alert('".label("This email is not valid!")."')</script>";
 }
 if ($err) return;

$api_action="";
$api_mies+=0;
$api_rok+=0;
$query="UPDATE webaktual
 SET 	mies=$api_mies,rok=$api_rok,headline='$api_headline',akt='$api_akt',more='$api_more',img='$api_img',nd_akt='$api_data'  
 WHERE servername='$KEY' AND pri=$api_pri ";
	
//echo nl2br($query);return;


$adodb->Execute($query);

?>