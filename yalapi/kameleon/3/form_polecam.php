<?
$showform = true; 
$script = "
<script type=\"text/javascript\">
function checkform()
{
blad=true;
  if (document.getElementById('f_dane').value == '') 
  	{
		document.getElementById('e_dane').style.display=\"block\";
		blad=false;
	}
  if (document.getElementById('f_mail').value == '') 
  	{
		document.getElementById('e_mail').style.display=\"block\";
		blad=false;
	}
  if (document.getElementById('f_mail2').value == '') 
  	{
		document.getElementById('e_mail2').style.display=\"block\";
		blad=false;
	}
  if (blad==false)
  	return false;
}
</script>
";
if ($_POST["kn"])
{
$dane = $_POST["kn"]["dane"];
$mail = $_POST["kn"]["mail"];
$mail2 = $_POST["kn"]["mail2"];
$kom=$_POST["kn"]["kom"];

}
else
{
$dane=$mail=$mail2=$kom="";
}

if ($_POST["kn"])
{
	$showform=false;
	$header = 	"From: ".$mail." \nContent-Type:".
			' text/plain;charset="UTF-8"'.
			"\nContent-Transfer-Encoding: 8bit";
	$mail_message ="Polecana strona www: http://www.yala.pl\n";
    $mail_message.="Imię i nazwisko: ".$_POST["kn"]["dane"]."\n";
    $mail_message.="Adres e-mail: ".$_POST["kn"]["mail"]."\n";
    $mail_message.="Komentarz: ".$_POST["kn"]["kom"]."\n";
    
    mail($mail2,"Polecam stronę Yala", $mail_message, $header."\n\r");
}
?>