<?
	include("$SKLEP_INCLUDE_PATH/stat/zam_daty.php");
	
	$query="SELECT * FROM sklep ORDER BY sk_id";
	$res=$projdb->execute($query);

	for ($s=0;$s<$res->RecordCount();$s++)
	{
		parse_str(ado_Explodename($res,$s));

		$rs=count($statusy);
		if ($res->RecordCount()>1) echo "<b>$sk_nazwa:</b><hr size=1>";

		echo "<table width=100%>";

		echo "<tr><td rowspan=$rs width=50% valign=top>".sysmsg("Today","admin")."<br>
				".humandate($dzis0)."</td>";

		foreach ($statusy AS $st)
		{
			$status=sysmsg("status_$st","status");
			echo "<td width=25%>$status</td>";
			$pole=$stat_daty[$st];
			$query="SELECT count(*) AS ile FROM zamowienia WHERE $pole>=$dzis0 AND za_sk_id=$sk_id
					AND za_status=$st";
			parse_str(ado_query2url($query));
			echo "<td width=35% align=right>$ile</td>";
			echo "<tr>";
		}

		echo "<td colspan=3><hr size=1></td><tr>";
		echo "<td rowspan=$rs width=50% valign=top>".sysmsg("Current week","admin")."<br>
				".humandate($tydzien0)." - ".humandate($NOW)."</td>";

		foreach ($statusy AS $st)
		{
			$status=sysmsg("status_$st","status");
			echo "<td width=25%>$status</td>";
			$pole=$stat_daty[$st];
			$query="SELECT count(*) AS ile FROM zamowienia WHERE $pole>=$tydzien0 AND za_sk_id=$sk_id
					AND za_status=$st";
			parse_str(ado_query2url($query));
			echo "<td width=25% align=right>$ile</td>";
			echo "<tr>";
		}



		echo "</table>";

	}

?>
