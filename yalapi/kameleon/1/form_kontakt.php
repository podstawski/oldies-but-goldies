<?
$script = "
<script type=\"text/javascript\">
function checkform()
{
  if ((document.getElementById('f_name').value == '') || (document.getElementById('f_firma').value == '') || (document.getElementById('f_email').value == '')  || (document.getElementById('f_telefon').value == '')) {alert('Proszê wype³niæ wymagane pola.');return false;}
}
document.getElementById('f_dotyczy').value='".$_REQUEST['produkt']."';

</script>
";
?>
