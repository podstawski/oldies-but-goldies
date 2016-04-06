<?
	$LIST[id]=$CIACHO[admin_ka_id];

	if (!function_exists("getFullPath"))
	{
		function getFullPath($id)
		{		
			$sql = "SELECT ka_nazwa, ka_parent FROM kategorie WHERE ka_id = $id";
			parse_str(ado_query2url($sql));
			if ($ka_parent)
				return "$ka_nazwa;".getFullPath($ka_parent);
			else
				return "$ka_nazwa";
		}
	}

	$ka_id = $LIST[id];
	if (!strlen($ka_id)) return;

	$sql = "SELECT * FROM kategorie WHERE ka_id=$ka_id ";

	parse_str(ado_query2url($sql));
	
	$opis = "ka_opis_m_".$lang;

	$indeks="<b>$ka_nazwa</b><br>".$$opis;

	$foto=explode(":",$ka_foto_m);
	if (count($foto)>1)
	{
		$img=$$foto[0];
		$img.="/".$foto[1];
	}
	else
	{
		$img="$UIMAGES/".$foto[0];
	}

	if (!file_exists($img) || is_dir($img)) $img="$SKLEP_IMAGES/spacer.gif";
	
	$path = getFullPath($ka_id);
	$path = explode(";",$path);
	$path = array_reverse($path);
	$path = implode("->",$path); 		

?>

<table cellspacing=0 width="100%">
<tr>
	<td valign="top"><?echo $path." ($ka_id)" ?></td>
	<td align="rigth" width=80 title="Foto małe">
		<iframe style="width:80;height:40;" marginheight=0 marginwidth=0
			scrolling="no" frameborder=0 
			src="<?echo $img?>"></iframe>
	</td>
</tr>
</table>
