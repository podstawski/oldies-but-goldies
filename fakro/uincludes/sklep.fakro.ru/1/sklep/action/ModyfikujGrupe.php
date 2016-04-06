<?
	$LIST[id]=$CIACHO[admin_to_id];
	$action_id = $LIST[id];
	$akcja = $FORM[akcja];
	$grupa = $FORM[grupa];
	$nowy_indeks = $FORM[new_indx];
	$stary_id = $FORM[indx];

	if (!strlen($akcja) || !strlen($grupa)) return;

	if ($akcja == 'in')
	{
		if (!strlen($nowy_indeks)) return;
		$sql = "SELECT to_id FROM towar WHERE to_indeks = '$nowy_indeks' OR to_ean = '$nowy_indeks'";
		parse_str(ado_query2url($sql));
		if (!strlen($to_id))
		{
			// error
			return;
		}
		$sql = "SELECT COUNT(*) AS jest FROM grupy_towarow
				WHERE $to_id IN (gt_to_id1,gt_to_id2)
				AND ".$LIST[id]." IN (gt_to_id1,gt_to_id2)
				AND gt_grupa = '$grupa'";
		parse_str(ado_query2url($sql));
		if ($jest || $to_id == $LIST[id])
		{
			// error
			$to_id = "";
			return;
		}

		$sql = "INSERT INTO grupy_towarow (gt_to_id1,gt_to_id2,gt_grupa)
				VALUES (".$LIST[id].",$to_id,'$grupa')";

		$projdb->execute($sql);
	}	

	if ($akcja == 'out')
	{
		if (!strlen($stary_id)) return;

		$sql = "DELETE FROM grupy_towarow WHERE $stary_id = gt_id";
		$projdb->execute($sql);
	}

	$to_id = "";

?>
