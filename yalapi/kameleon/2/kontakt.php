<?

$next=$next;
$msg_bad="";
$msg_ok=false;

if ($_POST["formsend"]==1)
{
  if (strlen($_POST["fr_imie"])==0) $msg_bad.="Podaj swoje imiê<br />";
  if (strlen($_POST["fr_nazwisko"])==0) $msg_bad.="Podaj swoje nazwisko<br />";
  if (strlen($_POST["fr_telefon"])==0) $msg_bad.="Podaj swój telefon<br />";
  if (strlen($_POST["fr_email"])==0) $msg_bad.="Podaj swój adres e-mail<br />";
  if ($_POST["fr_regulamin"]!=1) $msg_bad.="Musisz zaakceptowaæ regulamin<br />";
  
  if (strlen($msg_bad)==0)
  {
    $msg="Imiê: ".$_POST["fr_imie"]."\r\n";
    $msg.="Nazwisko: ".$_POST["fr_nazwisko"]."\r\n";
    $msg.="Telefon: ".$_POST["fr_telefon"]."\r\n";
    $msg.="E-mail: ".$_POST["fr_email"]."\r\n";
    $msg.="Regulamin: zaakceptowany\r\n";
    $msg.="Newsletter: ".$_POST["fr_newsletter"]."\r\n";
    mail("justyna@l-system.pl","Wiadomoœæ z Yala.pl",$msg,"From: mail@yala.pl");
    $msg_ok=true;
  }
}

?>