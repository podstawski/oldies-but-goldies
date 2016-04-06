<?
################
global $lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID;
global $lang,$id;

#$tab = unserialize(stripslashes($costxt));
################
/*
if(is_array($tab)) {
	$fakro = $tab["fakro"];
	}
*/
?>

<table width="580" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td align="center">
<?
$dir = opendir('../foto/'.$costxt);
while($file = readdir($dir)) {
	if((!ereg("[.]",$file))) {
		$themelist .= "$file ";
		}
	}
closedir($dir);

//-reszta
$themelist = explode(" ", $themelist);
sort($themelist);

	for($i = 1; $i < sizeof($themelist); $i++) {
		echo ' <a href="'.$self.'?id=' .$themelist[$i]. '">[ ' .$themelist[$i]. ' ]</a> ';
		}
?>
	</td>
</tr>
</table>

<?
if(!isset($id)) $id = 1;
$dir_foto = '../foto/'.$costxt.'/'.$id;
$dir_foto_thumbnails = "thumbnails";
$dir_foto_images = "images";

# otwiera katalog główny
$dir = opendir($dir_foto);

$num = 5;
$n_count = 0;

# otwiera katalog ze zdjeciami
$dir = opendir("$dir_foto/$dir_foto_thumbnails");


$foto_array = array();
while($file_name = readdir($dir)) {
	if(($file_name != ".") && ($file_name != "..") && ($file_name != "$dir_foto_thumbnails")) {
		$foto_array[$file_name] = $file_name;
	}
}
closedir($dir);
sort($foto_array);

echo '<table border="0" cellspacing="4" cellpadding="4">';

foreach ($foto_array as $key => $val) {
		$active_file = "$dir_foto/$dir_foto_thumbnails/$val";
		$big_file = "$dir_foto/$dir_foto_images/$val";
		
		$big_file_hi = "$id/$dir_foto_images/$val";
		
		if($n_count == 0) {
			echo "\n<tr>\n";
			$n_count++;
			}
		if(($n_count > 0) && ($n_count <= $num)) {
			echo "<td align=\"center\" valign=\"bottom\">";
			echo '<a href="javascript:popup_img(\''.$big_file.'\')"><img class="ramka" border="0" align="left" src="'.$active_file.'" alt=""/></a>';
			echo "</td>\n";
			$n_count++;
			}
		if($n_count > $num) {
			echo "</tr>";
			$n_count = 0;
			}
}
echo "</table>\n";
?>
