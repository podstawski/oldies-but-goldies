<?
if (!$page || $page==997 || strstr($tree,':997:')) return;


$ret = "<div class=\"koszyk\">";
$ret.= "<a href=\"$next\"><img src=\"".$UIMAGES."/sb/koszyk_head.gif\" border=0></a>";
$ret.= "<p>";
ob_start();
include("$SKLEP_INCLUDE_PATH/koszyk.php");
$ile=ob_get_contents();
ob_end_clean();



if (unhtml($ile)>0)
	$ret.= "<a href=\"$next\">".sysmsg("article_items_in_cart","cart").": <b>$ile</b></a>";
else
	$ret.= sysmsg("no_article_in_cart","cart");;

$ret.= "</p>";
$ret.= "</div>";
echo $ret;

$KOSZYK_NEXT = $next;
?>