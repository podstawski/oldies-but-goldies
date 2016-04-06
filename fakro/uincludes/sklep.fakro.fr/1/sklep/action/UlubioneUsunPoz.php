<?
	$usun = $FORM[article_id];

	if (strlen($usun))
	{
		$sql = "DELETE FROM ulubione WHERE ul_id = $usun AND ul_su_id=$AUTH[id]";
		$adodb->execute($sql);
	}

?>
