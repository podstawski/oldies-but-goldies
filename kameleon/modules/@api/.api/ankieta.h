<?
	$MAX_WIDTH = 100;
	if (strlen($size)) $MAX_WIDTH = $size;

	$SHOW_ALL_ANSWERS = 1;

	$params = explode(";",$costxt);
	
//	print_r($VOTE);

	if (count($params) < 2) return;
//	if ($ankieta_id != $sid)
	$show_score = $params[1];
	
	if (!strlen($show_score)) $show_score = $params[1];
	
		
	$q_name = eregi_replace(" ","_",$params[0]);
	if (!strlen($how_long)) $how_long = $params[2];
	if (!strlen($how_long)) $how_long = 10;
	$graph_button = $params[3];
	$sumq = $params[4];
	$precision=$params[5];
	if (!strlen($precision)) $precision=2;

	if ($show_score)
	{
		if (!$SHOW_ALL_ANSWERS) $add = "AND aq_input = 0";
		if (strlen($sumq)) $add2 = "OR aq_name = '$sumq'";
		$sql = "SELECT SUM(aq_hits) AS total_hits FROM api2_questionnaire
				WHERE (aq_name = '$params[0]' $add2) $add AND
				aq_server = $SERVER_ID";
//		$adodb->debug = 1;
		parse_str(ado_query2url($sql));
		if ($total_hits == 0 && $show_score!=2)
		{
			echo "<br><br>Brak odpowiedzi";
			return;
		}
//		$adodb->debug = 0;
	}
	
// ---------------------
	if (strlen($sumq))
	{
		$query = "SELECT * FROM api2_questionnaire
				  WHERE aq_name = '$sumq' 
				  AND aq_server = $SERVER_ID
				  ORDER BY aq_pri";

		$res2 = $adodb->execute($query);

		$sum_hits=array();
		if (is_object($res2))
			for ($k=0; $k < $res2->RecordCount(); $k++)
			{
				parse_str(ado_explodename($res2,$k));
				$sum_hits[] = $aq_hits;
			}
	}
// ---------------------
	$sql = "SELECT * FROM api2_questionnaire
			WHERE aq_name = '$params[0]' AND aq_server = $SERVER_ID
			ORDER BY aq_pri";

	$res = $adodb->execute($sql);
	
	if (strlen($next)) $where = $next;
		else $where = $self;

	$ankieta = "<FORM METHOD=POST ACTION=\"$where\" name=\"ankietaf_$sid\">
			<INPUT TYPE=\"hidden\" name=\"show_score\" value=\"1\">
			<INPUT TYPE=\"hidden\" name=\"ACTION_VAR\" value=\"answer,how_long,INPUTS,VOTE\">
			<INPUT TYPE=\"hidden\" name=\"how_long\" value=\"$how_long\">
			<INPUT TYPE=\"hidden\" name=\"ankieta_id\" value=\"$sid\">
			<INPUT TYPE=\"hidden\" name=\"isselected\" value=\"0\">
			<INPUT TYPE=\"hidden\" name=\"action\" value=\"AnkietaGlos\">
			<TABLE border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"api2_quest_table\">";
//	isSelected_$sid()
	for($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		if (!$show_score || $show_score == 2)
		{
			$input = "";
			$radio = "<INPUT TYPE=\"radio\" onClick=\"isSelected_$sid()\" value=\"$aq_id\" NAME=\"answer\" id=\"r_$aq_id\" class=\"api2_quest_radio\">";
			if ($aq_input) $input = "<INPUT TYPE=\"text\" value=\"\" id=\"input_$aq_id\" NAME=\"INPUTS[input_$aq_id]\" onBlur=\"setSelected_$sid('r_$aq_id','input_$aq_id')\" class=\"api2_quest_input\" style=\"width:100px\">";
			$ankieta.="
				<TR class=\"api2_quest_tr\">
					<TD class=\"api2_quest_td_radio\" valign=\"top\">$radio</TD>
					<TD class=\"api2_quest_td_answer\">$aq_answer $input</TD>
					<td class=\"api2_quest_td_input\"></td>
				</TR>
				";
		}
	}
	if ($show_score == 2)
	{
		$ankieta.="<tr class=\"api2_quest_tr\"><td class=\"api2_quest_td\" colspan=\"3\">Wyniki</td></tr>";
	}

	if ($show_score > 0)
	{
		$max = 1;
		for($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			if (strlen($sumq))
				$aq_hits+=$sum_hits[$i];
			if ($max < $aq_hits) $max = $aq_hits;
		}
/*
		echo "MW = $MAX_WIDTH<br>";
		echo "MAX = $max<br>";
		echo "inc = ".floor($MAX_WIDTH / $max)."<br>";
*/
		for($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));

			if (strlen($sumq))
				$aq_hits+=$sum_hits[$i];

			if ($total_hits > 0)
				$procent = ($aq_hits * 100) / $total_hits; 
			else $procent = 0;
			$procent = round($procent,$precision);
			
			if ($max > 0)
				$inc = $MAX_WIDTH / $max; 
			else $inc = 0;
			
			$ile = ($aq_hits * $MAX_WIDTH) / $max;
			$inc = ceil($inc);

			$pixels = floor($ile);
			
			$last = dechex(rand(100,180));$first = dechex(rand(181,255));$middle = dechex(rand(17,255));
	//		$color = "style=\"background:'#FF0000'\" ";

			if (!$aq_input || $SHOW_ALL_ANSWERS)
			$ankieta.="
				<TR class=\"api2_quest_ans_tr\">
					<TD class=\"api2_quest_ans_td_answer\">$aq_answer</TD>
					<TD class=\"api2_quest_ans_td_percent\" align=\"right\" nowrap>&nbsp;$procent%</TD>
					<td class=\"api2_quest_ans_td_bar\"> <img src=\"$IMAGES/spacer.gif\" class=\"api2_quest_bar\" $color height=\"10\" width=\"$pixels\"><span class=\"api2_quest_ans_td_hits\">&nbsp;$aq_hits</span></td>
				</TR>
			";
		}
	}

	if ($show_score == 1 || $show_score == 2)
	{
		$ankieta.="
			<TR class=\"api2_quest_ans_tr\">
				<TD colspan=\"3\" class=\"api2_quest_ans_votes\">".label("Number of answers")." : $total_hits</form></td>
			</tr>";
	} 
	if ($show_score == 0 || $show_score == 2)
	{
		$QUEST_BUTTON = "quest_button.gif";

		$ans_button = label("Send vote");
		if (!$graph_button)
		$ankieta.="
			<TR class=\"api2_quest_tr\">
				<TD class=\"api2_quest_td_send\" colspan=\"2\"><INPUT TYPE=\"button\" value=\"$ans_button\" onClick=\"sendVote_$sid()\" class=\"api2_quest_button\"></td>
			</tr>";
		else
		$ankieta.="
			<TR class=\"api2_quest_tr\">
				<TD class=\"api2_quest_td_send\" colspan=\"2\"><img src=\"$UIMAGES/$QUEST_BUTTON\" onClick=\"sendVote_$sid()\" style=\"cursor:hand\"></td>
			</tr>";


	}
	$ankieta.="</TABLE></form>";

	echo $ankieta;

//	$show_score = pop();
	unset($show_score);
	unset($params);
	unset($costxt);
	unset($answer);
	unset($ankieta_id);
?>
