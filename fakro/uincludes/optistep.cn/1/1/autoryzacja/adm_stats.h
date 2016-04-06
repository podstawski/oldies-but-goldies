<?
	include("$INCLUDE_PATH/autoryzacja/img.h");

	global $orderby, $corderby, $cdirect;

	$direct = "ASC";

	if ($corderby == $orderby)
	{
		if ($cdirect == "ASC")
			$direct = "DESC";
		else
			$direct = "ASC";
	}

	if (strlen($corderby) && !strlen($orderby))
		$orderby = $corderby;
	
	if (!strlen($orderby))
		$orderby = "su_login";

	echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
		document.cookie = 'corderby=$orderby;path=/';
		document.cookie = 'cdirect=$direct;path=/';
	</SCRIPT>";

	$sql = "SELECT * FROM system_user WHERE	
			su_server = $SERVER_ID AND su_parent IS NULL
			ORDER BY upper($orderby) $direct";

	$res = 	pg_exec($db,$sql);

	echo "
	<TABLE border=\"1\" cellspacing=\"0\" cellpading=\"0\" class=\"tabletable\" width=\"100%\">
	<col>
	<col>
	<col width=\"10%\">
	<TR class=\"tabletr\">
		<Th class=\"tabletd\">Nr</Th>
		<Th class=\"tabletd\"><A HREF=\"$self${next_char}orderby=su_nazwisko\">Nazwa biura</A></Th>
		<Th class=\"tabletd\"><A HREF=\"$self${next_char}orderby=su_login\">Nr agencyjny</A></Th>
		<Th class=\"tabletd\" title=\"Liczba pracowników\">LP</Th>
		<Th class=\"tabletd\" title=\"Licznik wej¶æ\">LW</Th>
		<Th class=\"tabletd\" title=\"£±czny czas pobytu\">£C</Th>
	</TR>";

	for ($i=0; $i < pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$nr = $i+1;
		$buttons = "<A HREF=\"$next${next_char}suid=$su_id\"><img alt=\"edytuj\" src=\"$AUTHIMG/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$su_id')\" alt=\"usuñ\" src=\"$AUTHIMG/i_delete_n.gif\" border=0 style=\"cursor:hand\">";
		if (!strlen($su_miejscowosc))$su_miejscowosc = "&nbsp;";
		else $rodzaj = "&nbsp;";
		if (!strlen($su_login)) $su_login = "&nbsp;";

		$disable = "";

		if (!strlen($su_aktywny))
		{
			$disable = "disabled";
//			$buttons = "&nbsp;";
		}

		$sql = "SELECT su_id FROM system_user WHERE su_parent = $su_id";
		$r = pg_exec($db,$sql);
		$ile = pg_numrows($r);
		$cala_suma = 0;
		$ilosc_wejsc = 0;
		for ($k=0; $k < $ile; $k++)
		{
			parse_str(pg_explodename($r,$k));

			$sql = "SELECT COUNT(sl_tout) AS tot, SUM(sl_tout - sl_tin) AS suma FROM system_log 
					WHERE sl_server = $SERVER_ID AND sl_user_id = $su_id";
			parse_str(query2url($sql));
			$ilosc_wejsc+=$tot;
			$cala_suma+=$suma;
		}
		
		if ($cala_suma)
			$cala_suma = date("H:i:s",$cala_suma);
		else
			$cala_suma = "00:00:00";

		echo "
			<TR class=\"tabletr\" $disable>
				<TD class=\"tabletd\" $disable>$nr</TD>
				<TD class=\"tabletd\" $disable>$su_nazwisko</TD>
				<TD class=\"tabletd\" $disable>$su_login</TD>
				<TD class=\"tabletd\" $disable>$ile</TD>
				<TD class=\"tabletd\" $disable>$ilosc_wejsc</TD>
				<TD class=\"tabletd\" $disable>".$cala_suma."</TD>
			</TR>";
	}
	echo "</table>";
?>
