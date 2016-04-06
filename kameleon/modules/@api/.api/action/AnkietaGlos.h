<?
	global $answer, $how_long, $SERVER_ID, $REMOTE_ADDR, $INPUTS, $VOTE;

	if (!strlen($how_long)) $how_long = 10;



	if (strlen($answer))
	{
		$sql = "SELECT aq_name FROM api2_questionnaire
				WHERE aq_id = $answer AND 
				aq_server = $SERVER_ID";

		parse_str(ado_query2url($sql));

		$sql = "UPDATE api2_questionnaire SET
				aq_hits = aq_hits + 1 WHERE
				aq_id = $answer AND 
				aq_server = $SERVER_ID";

		if (!$VOTE[$aq_name])
			$adodb->execute($sql);


		$in_text = $INPUTS["input_$answer"];

		if (strlen($in_text))
		{
			$sql = "INSERT INTO api2_questionnaire_answers
					(aqa_aq_id,aqa_answer,aqa_remote)
					VALUES($answer,'$in_text','$REMOTE_ADDR')";
//			$adodb->debug = 1;
			if (!$VOTE[$aq_name])	$adodb->execute($sql);
		}


		$JScript.="var exp_date = new Date();
			sec = exp_date.getSeconds(); 
			exp_date.setSeconds(sec+ $how_long);
			document.cookie='VOTE[$aq_name]=1;expires='+exp_date.toGMTString();
			";

	} 
	$adodb->debug = 0;
	unset($show_score);

?>
