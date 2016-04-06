<?
	$LIST[id]=$CIACHO[admin_pr_id];

	$pr_id = $LIST[id];
	if (!strlen($pr_id)) return;

	$sql = "SELECT ";

	$sql = "SELECT * FROM producent WHERE pr_id=$pr_id ";

	parse_str(ado_query2url($sql));
	
	$indeks="<b>$pr_nazwa</b><br><a href=\"http://$pr_www\" target=\"_new\">$pr_www</a><br>".$$opis;

	$foto=explode(":",$pr_logo_m);
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
	<td valign="top"><?echo $indeks." ($pr_id)" ?></td>
	<td align="rigth" width=80 title="Logo maГe">
		<iframe style="width:80;height:40;" marginheight=0 marginwidth=0
			scrolling="no" frameborder=0 
			src="<?echo $img?>"></iframe>
	</td>
</tr>
</table>
