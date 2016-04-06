<?
$url_print = str_replace($file_name,"print/$file_name",$REQUEST_URI);
$href_print="popup_url('".$url_print."',780, 400,'yes')";

$url_favorite = "http://".$HTTP_HOST.$REQUEST_URI;
$href_favorite = "window.external.AddFavorite('".$url_favorite."','sklep FAKRO: '+JSTITLE)";

?>

<script language="JavaScript1.2" type="text/javascript">

function favorite()
{
	<?echo $href_favorite;?>

}

function drukuj()
{
	<?echo $href_print;?>

}
</script>