<?
	if (!strlen($cos)) $cos = 335;

	$sql = "SELECT * FROM kategorie WHERE ka_parent = $cos";
	$res = $adodb->execute($sql);

	echo "
	<div class=\"galery\">
		<table cellspacing=\"0\" cellpadding=\"3\" border=\"0\">
	    <tbody>
		    <tr>
		
		";


	for($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		list(,$foto) = explode(":",$ka_foto_d);

		eval('$opis=$ka_opis_m_'.$lang.';');

		if ($i && !($i%3))
			echo "</tr><tr>";
		echo "
         <td class=\"\"><img style=\"cursor:pointer;\" onclick=\"setColor('$ka_id')\" height=\"90\" alt=\"$opis\" src=\"$UIMAGES/$foto\" width=\"137\" border=\"0\"/><br/>
         <center>$opis</center></td>		
		";
	}

	echo "
			</tr>
			<tr>
			</tr>
		</tbody>
	</table>
	</div>
	";

?>