<?
	global $QUESTIONNAIRE;
	$xml="";
	
	if (strlen($QUESTIONNAIRE[aq_name]))
	{
		$showsrc = '0';
		if ($QUESTIONNAIRE[show_scr]) $showsrc = '1';
		if ($QUESTIONNAIRE[show_plus_scr]) $showsrc = '2';
		$costxt = $QUESTIONNAIRE[aq_name].';'.$showsrc;
		$costxt.= ';'.$QUESTIONNAIRE[how_long].';'.$QUESTIONNAIRE[show_graph_button];
		$costxt.= ';'.$QUESTIONNAIRE[sum_name].';'.$QUESTIONNAIRE[precision];
	}
	

?>
