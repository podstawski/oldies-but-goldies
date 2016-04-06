<?
require_once('recaptchalib.php');
$captcha = recaptcha_get_html("6LciZcMSAAAAAFnHE48Ss6VonszQ6_UVZhsPmxbs");

$showform = true; 
$error=false;
$script = "
<script type=\"text/javascript\">
function checkform()
{
  if ((document.getElementById('f_name').value == '') || (document.getElementById('f_email').value == '')  || (document.getElementById('f_telefon').value == '') || (document.getElementById('f_zgoda').checked == '')) {alert('Proszę wypełnić wymagane pola.');return false;}
}
</script>
";

if ($_POST["kn"])
{
$comment = $_POST["kn"]["comment"];
$name = $_POST["kn"]["name"];
$firma = $_POST["kn"]["firma"];
$email=$_POST["kn"]["email"];
$telefon = $_POST["kn"]["telefon"];
$zgoda = $_POST["kn"]["zgoda"];
}
else
{
$comment=$name=$firma=$email=$telefon=$zgoda="";
}

if ($_POST["kn"])
{
  $privatekey = "6LciZcMSAAAAAIHV1GEHQQ1slp1xqXvPqCymT9_m";
  $resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
  
  if (!$resp->is_valid) {
    $error="Podaj poprawny kod weryfikujacy";
  } else {
    $showform=false;
	$header = 	"From: ".$email." \nContent-Type:".
			' text/plain;charset="UTF-8"'.
			"\nContent-Transfer-Encoding: 8bit";

    $mail_message ="Imię i nazwisko: ".$_POST["kn"]["name"]."\n";
    $mail_message.="Nazwa firmy: ".$_POST["kn"]["firma"]."\n";
    $mail_message.="Nr telefonu: ".$_POST["kn"]["telefon"]."\n";
    $mail_message.="Adres e-mail: ".$_POST["kn"]["email"]."\n\n";
	$mail_message.="Zgoda: ".$_POST["kn"]["zgoda"]."\n\n";
	if (strlen($costxt))
		mail($costxt,"Yala - Formularz kontaktowy", $mail_message, $header."\n\r");
	else
	    mail("agnieszka@l-system.pl","Formularz kontaktowy www.yala.pl", $mail_message, $header."\n\r");
  }
}
?>
