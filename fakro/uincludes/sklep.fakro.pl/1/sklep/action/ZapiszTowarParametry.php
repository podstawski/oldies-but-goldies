<?
	if (!strlen($FORM[id])) return;

	$float_array = array("tp_a","tp_b","tp_c","tp_d","tp_l","tp_r1","tp_r2","tp_o","tp_m_m","tp_m_m2","tp_m_szt","tp_m_jm");

	reset($FORM);
	while (list($key,$val) = each($FORM))
		if (in_array($key,$float_array))
		{
			$FORM[$key] = toFloat($val);	
		}

	for ($i=0; $i < count($float_array); $i++)
	{
		$key = $float_array[$i];
		$val = $FORM[$key];
		$FORM[$key] = toFloat($val);	
	}

	$sql="SELECT count(*) AS c FROM towar_parametry WHERE tp_to_id =".$FORM[id];
	parse_str(ado_query2url($sql));

	$sql="";
	if (!$c) $sql="INSERT INTO towar_parametry (tp_to_id) VALUES ($FORM[id]);\n";


	$sql.="UPDATE towar_parametry SET
			tp_a = ".$FORM[tp_a].",
			tp_b = ".$FORM[tp_b].",
			tp_c = ".$FORM[tp_c].",
			tp_d = ".$FORM[tp_d].",
			tp_l = ".$FORM[tp_l].",
			tp_r1 = ".$FORM[tp_r1].",
			tp_r2 = ".$FORM[tp_r2].",
			tp_o = ".$FORM[tp_o].",
			tp_m_m = ".$FORM[tp_m_m].",
			tp_m_m2 = ".$FORM[tp_m_m2].",
			tp_m_szt = ".$FORM[tp_m_szt].",
			tp_m_jm = ".$FORM[tp_m_jm].",
			tp_gatunek = '".addslashes(stripslashes($FORM[tp_gatunek]))."',
			tp_stan = '".addslashes(stripslashes($FORM[tp_stan]))."'
			WHERE tp_to_id = ".$FORM[id]."";

	$adodb->execute($sql);
	$action_id=$FORM[id];
?>
