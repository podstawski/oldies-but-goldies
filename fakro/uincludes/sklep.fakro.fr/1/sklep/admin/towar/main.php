<?
	$LIST[id]=$CIACHO[admin_to_id];

	$to_id = $LIST[id];
	if (!strlen($to_id)) return;

	$sql = "SELECT * FROM towar WHERE to_id=$to_id ";

	parse_str(ado_query2url($sql));

	$indeks="<b>$to_indeks</b><br>$to_nazwa";

	$foto=explode(":",$to_foto_m);
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

?>

<table cellspacing=0 width="100%">
<tr>
	<td valign="top"><?echo $indeks." ($to_id)"?></td>
	<td align="right" width=80 title="picture">
		<img src="<?echo $img?>" border=0 height="40"></td>
</tr>
</table>
