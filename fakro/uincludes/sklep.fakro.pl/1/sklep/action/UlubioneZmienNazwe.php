<?
	$nazwa = $FORM[nazwa];
	$nowa_nazwa = $FORM[ul_nazwa];

	if (!strlen($nazwa) || $AUTH[id] <= 0 ) return;

	if (strlen($nowa_nazwa) && ($nowa_nazwa != $nazwa))
	{
		$sql = "SELECT COUNT(*) AS jest FROM ulubione 
				WHERE ul_nazwa = '$nowa_nazwa' 
				AND ul_su_id = ".$AUTH[id];

		parse_str(ado_query2url($sql));
		if (!$jest)
		{
			$sql = "UPDATE ulubione SET ul_nazwa = '$nowa_nazwa' 
					WHERE ul_su_id = ".$AUTH[id]." AND ul_nazwa = '$nazwa'";
			$adodb->execute($sql);
			$nazwa = $nowa_nazwa;
			$FORM[nazwa] = $nowa_nazwa;
		}
		else
		{
			echo "
			<script>
				alert('".sysmsg("Name is already in use","system")."');
			</script>
			";
		}
	}

?>
