<?
global $WEBTD, $form, $list;
global $SERVER_ID,$lang,$ver,$adodb,$DEFAULT_PATH_PAGES,$WEBTD;

echo '
<fieldset style="width:99%; margin-left:2px;">
<legend>Aktualizacja firm - Domy PL (FAKRO)</legend>
<div align="center">
<br>
<form method=post action="'.$self.'">
<INPUT TYPE="hidden" NAME="form[pole]" value="aktualizacja">
<INPUT TYPE="submit" value="--- Aktualizacja firm ---" class="k_button">
</form></div>
<br>';

if($form[pole] == "aktualizacja") {
	$fakrodb->debug=1;
	$menu_id = $WEBTD->menu_id;
	$menuArray = kameleon_menus($menu_id);
	
	$_count_menu = count($menuArray);
	
	$d_i = 0; $d_u = 0;
	for($i=0; $i<$_count_menu; $i++) { 
		$layouttip = $menuArray[$i];
		
		$sql = "SELECT firma_menu FROM dom_firma WHERE firma_menu='$layouttip->sid'";
		parse_str(query2url($sql));
		
		if($firma_menu) {
			$sql = "UPDATE dom_firma SET firma_nazwa = '".$layouttip->alt."', firma_link = '".$layouttip->href."', firma_logo = '".$layouttip->img."' WHERE firma_menu = '".$layouttip->sid."'";
			pg_exec($db,$sql);
			$d_u++;
			}else{
			$sql = "INSERT INTO dom_firma (firma_menu,firma_nazwa,firma_link,firma_logo) VALUES ('".$layouttip->sid."','".$layouttip->alt."','".$layouttip->href."','".$layouttip->img."')";
			pg_exec($db,$sql);
			$d_i++;
			}
		}
	
	echo 'Dane firm:<br>dodano - <strong>'.$d_i.'</strong>  zaktualizowano - <strong>'.$d_u.'</strong> ';
	}

echo "</fieldset>";
echo "<div align=\"right\">sid: ".$WEBTD->sid."</div>";
?>