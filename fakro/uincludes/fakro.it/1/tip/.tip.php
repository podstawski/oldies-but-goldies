<style type="text/css">
div.tip {
	display: none;
	}
</style>
<?
global $SERVER_ID,$lang,$ver,$adodb,$DEFAULT_PATH_PAGES,$WEBTD;

$menu_id = $WEBTD->menu_id;
$menuArray = kameleon_menus($menu_id);

$_count_menu = count($menuArray);

for($i=0; $i<$_count_menu; $i++) { 
	$layouttip = $menuArray[$i];
	
	$href = kameleon_href('','',$layouttip->page_target);
	echo '<div id="'.($i+1).'" class="tip"><a href="'.$href.'"><img src="'.$UIMAGES.'/'.$layouttip->img.'" alt="'.$layouttip->alt.'" border="0"></a></div>';
	}
?>

<script type="text/javascript">
function losowanie(ile) {
	var wartosc = Math.floor(Math.random() * ile) + 1;
	document.getElementById(wartosc).style.display = 'inline';
	}
losowanie(<?=$_count_menu?>)
</script>