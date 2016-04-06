<?

	global $showq, $addq, $editq, $killq, $changeq, $oldname, 
			$saveq, $content, $is_input, $move, $qid, $qpri;

	if ($move == 'up' && strlen($qid) && strlen($showq) && strlen($qpri))
	{
//		$adodb->debug=true;
		$sql = "SELECT MAX(aq_pri) AS next_q FROM api2_questionnaire
				WHERE aq_pri < $qpri AND aq_server = $SERVER_ID
				AND aq_name = '$showq'";
		
		parse_str(ado_query2url($sql));

		if (!strlen($next_q))
		{
			$sql = "SELECT MAX(aq_pri) AS next_q FROM api2_questionnaire
					WHERE aq_server = $SERVER_ID
					AND aq_name = '$showq'";
			
			parse_str(ado_query2url($sql));
		}

		$sql = "UPDATE api2_questionnaire SET
				aq_pri = $qpri WHERE aq_pri = $next_q
				AND aq_server = $SERVER_ID
				AND aq_name = '$showq'";

		$adodb->execute($sql);

		$sql = "UPDATE api2_questionnaire SET
				aq_pri = $next_q WHERE aq_id = $qid
				AND aq_server = $SERVER_ID
				AND aq_name = '$showq'";

		$adodb->execute($sql);
		
//		$adodb->debug=false;
	}
	elseif ($move == 'down' && strlen($qid) && strlen($showq) && strlen($qpri))
	{
		$sql = "SELECT MIN(aq_pri) AS next_q FROM api2_questionnaire
				WHERE aq_pri > $qpri AND aq_server = $SERVER_ID
				AND aq_name = '$showq'";
		
		parse_str(ado_query2url($sql));

		if (!strlen($next_q))
		{
			$sql = "SELECT MIN(aq_pri) AS next_q FROM api2_questionnaire
					WHERE aq_server = $SERVER_ID
					AND aq_name = '$showq'";
			
			parse_str(ado_query2url($sql));
		}

		$sql = "UPDATE api2_questionnaire SET
				aq_pri = $qpri WHERE aq_pri = $next_q
				AND aq_server = $SERVER_ID
				AND aq_name = '$showq'";

		$adodb->execute($sql);

		$sql = "UPDATE api2_questionnaire SET
				aq_pri = $next_q WHERE aq_id = $qid
				AND aq_server = $SERVER_ID
				AND aq_name = '$showq'";

		$adodb->execute($sql);
	}

	if (strlen($costxt))
		$params = explode(";",$costxt);

	if (!strlen($showq)) $showq = $params[0];
	if ($params[1] == 1) $src_check = "checked";
	elseif ($params[1] == 2) $srcplus_check = "checked";
	
	$how_long = $params[2];
	if (!strlen($how_long)) $how_long = 10;
	if ($params[3] == 1) $button_check = "checked";
	$sumq = $params[4];
	$precision=$params[5];
	if (!strlen($precision)) $precision=2;

	if (strlen($saveq))
	{
		$sql = "SELECT aq_name AS showq FROM api2_questionnaire
				WHERE aq_id = $saveq
				AND aq_server = $SERVER_ID";

		parse_str(ado_query2url($sql));

		if (!strlen($is_input)) $is_input = 0;
		$sql = "UPDATE api2_questionnaire SET
				aq_answer = '$content',
				aq_input = $is_input
				WHERE aq_id = $saveq
				AND aq_server = $SERVER_ID";

		$adodb->execute($sql);
	}

	if (strlen($changeq))
	{
		$sql="SELECT COUNT(*) AS ile FROM api2_questionnaire
				WHERE aq_name='$changeq' AND aq_server = $SERVER_ID";
		
		parse_str(ado_query2url($sql));


		if ($ile == 0)
		{
			$sql="UPDATE api2_questionnaire SET 
					aq_name='$changeq' WHERE 
					aq_name='$oldname' AND aq_server = $SERVER_ID";
			
			$adodb->execute($sql);
			$showq = $changeq;
		} 
		else
		{
			echo "
			<script>
				alert('".label("That name is alredy in use")."');
			</script>";
			$showq = $oldname;
		}
	}
	if (strlen($killq))
	{
		$sql="DELETE FROM api2_questionnaire
				WHERE aq_id = $killq AND aq_server = $SERVER_ID";

		$adodb->execute($sql);
	}

	if (strlen($editq))
	{
		$sql="SELECT * FROM api2_questionnaire
				WHERE aq_server = $SERVER_ID 
				AND aq_lang = '$lang'
				AND aq_id = $editq";

		parse_str(ado_query2url($sql));
		
		$ch[$aq_input] = "checked";

		$edit_content = "
	  <table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
		<TR>
			<td colspan=2 class=k_formtitle>".label("Edit question").":</td>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Question content")."</TD>
			<TD><TEXTAREA NAME=\"QUESTIONNAIRE[answer]\" id=\"aq_answer\" ROWS=\"3\" COLS=\"25\" class=\"k_input\">$aq_answer</TEXTAREA></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Custom question")."</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"QUESTIONNAIRE[aq_input]\" id=\"aq_input0\" $ch[0] value=\"0\">".label("No")." &nbsp;&nbsp;
				<INPUT TYPE=\"radio\" NAME=\"QUESTIONNAIRE[aq_input]\" id=\"aq_input1\" $ch[1] value=\"1\">".label("Yes")."</TD>
		</TR>
		<TR class=k_form>
			<td colspan=2 align=\"right\"><img src=\"img/i_save_n.gif\" style=\"cursor:hand\" onClick=\"saveQuestion('$editq')\"></td>
		</TR>
		</TABLE>
		";

	}

	if (strlen($addq))
	{
		$sql = "SELECT MAX(aq_pri) AS next_pri FROM api2_questionnaire
				WHERE aq_name = '$addq'
				AND aq_server = $SERVER_ID
				AND aq_lang = '$lang'";
		
		parse_str(ado_query2url($sql));
		
		$next_pri++;

		$sql = "INSERT INTO api2_questionnaire
				(aq_name, aq_pri, aq_answer, aq_server, aq_lang)
				VALUES('$addq',$next_pri,'Odpowiedz nr $next_pri',$SERVER_ID,'$lang')";

		$adodb->execute($sql);
		$showq = $addq;
	}

	if ($showq == -1)
	{
		
		$sql = "SELECT COUNT(*) AS ilosc FROM api2_questionnaire
				WHERE aq_server = $SERVER_ID";
		
		parse_str(ado_query2url($sql));

		$sql = "INSERT INTO api2_questionnaire
				(aq_name, aq_pri, aq_answer, aq_server, aq_lang)
				VALUES('Ankieta nr $ilosc',1,'Odpowiedz nr 1',$SERVER_ID,'$lang')";

		if ($adodb->execute($sql)) $showq = "Ankieta nr $ilosc";
	}
	

	if (strlen($showq) && $showq != -1 && $showq != '0')
	{
		$nowa_pozycja = "<A HREF=\"javascript:newQuestion('$showq')\"><img src=\"img/i_new_n.gif\" alt=\"nowa pozycja\" border=\"0\"></A>";
		$zmien_nazwa = "<A HREF=\"javascript:changeName('$showq')\">Zmien nazwe</A>";
		$sql="SELECT * FROM api2_questionnaire
				WHERE aq_server = $SERVER_ID 
				AND aq_lang = '$lang'
				AND aq_name = '$showq'
				ORDER BY aq_pri";

		$aq_content = "
		  <table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<TR>
				<td colspan=2 class=k_formtitle>".label("Questionaire").":</td>
			</TR>
			<TR class=k_form>
				<TD><INPUT TYPE=\"text\" NAME=\"newq\" value=\"$showq\" onBlur=\"changeName('$showq')\" class=\"k_input\"></TD>
				<TD>$nowa_pozycja</TD>
			</TR>
			";

		$res=$adodb->execute($sql);

		$numrows = $res->RecordCount();
		
		for($i=0; $i < $numrows ; $i++)
		{
			parse_str(ado_explodename($res,$i));

			$edytuj = "<A HREF=\"javascript:editQuestion('$aq_id')\"><img src=\"img/i_property_n.gif\" alt=\"edytuj\" border=\"0\"></A>";
			$usun = "<A HREF=\"javascript:deleteQuestion('$aq_id','$showq')\"><img src=\"img/i_delete_n.gif\" alt=\"usun\" border=\"0\"></A>";
			$mup = "<A HREF=\"javascript:moveQuestion('up','$aq_pri','$aq_id','$showq')\"><img src=\"img/i_up_n.gif\" alt=\"przesuñ w górê\" border=\"0\"></A>";
			$mdown = "<A HREF=\"javascript:moveQuestion('down','$aq_pri','$aq_id','$showq')\"><img src=\"img/i_down_n.gif\" alt=\"przesuñ w dó³\" border=\"0\"></A>";

			$aq_content.="
			<TR class=\"k_form\">
				<TD>$aq_answer</TD>
				<TD>$edytuj $usun $mup $mdown</TD>
			</TR>
			";

		}
		
		$aq_content.="</table>";

		$sql = "SELECT aq_answer, aqa_answer FROM api2_questionnaire_answers, api2_questionnaire
				WHERE aqa_aq_id = aq_id 
				AND aq_input = '1' 
				AND aq_name = '$showq'
				AND aq_server = $SERVER_ID 
				ORDER BY aq_answer";

//		$adodb->debug = 1;
		$result = $adodb->execute($sql);
		$numr = $result->RecordCount();
		$other = "<table width=\"100%\" border=1 align=center bgcolor=\"#E0E0E0\" cellpadding=1 cellspacing=1>
		<TR class=k_form>
			<td bgcolor='#808080' style=\"color:white\"><B>".label("Custom answers").":</B></td>
		</TR>
		";
//		$da_nr = 0;
		for($ind=0; $ind < $numr ; $ind++)
		{
			parse_str(ado_explodename($result,$ind));

			if ($last_name != $aq_answer)
			{
				$last_name = $aq_answer;
				$da_nr = 0;
				$other.= "
				<TR class=k_form>
					<TD bgcolor='#C0C0C0'>".label("Submited answers").": <B>$aq_answer</B></TD>
				</TR>
				";
			}
			$da_nr++;
			$other.= "
					<TR class=k_form>
						<TD>($da_nr) - $aqa_answer</TD>
					</TR>
					";

		}
		$custom_ans_count = $numr;


// lista do sumowania odpowiedzi z ankiet

	$sql = "SELECT aq_name FROM api2_questionnaire 
			WHERE aq_server = $SERVER_ID 
			GROUP BY aq_name";
	
	$res=$adodb->execute($sql);

	$numrows = $res->RecordCount();

	$sum_select = "<SELECT NAME=\"QUESTIONNAIRE[sum_name]\" class=\"k_input\">\n";
	$sum_select.= "<option value=\"\"> ".label("Select")." </option>\n";	
	for($i=0; $i < $numrows ; $i++)
	{
		parse_str(ado_explodename($res,$i));
		$selected = "";
		if ($aq_name == $sumq) $selected = "selected";
		$sum_select.= "<option value=\"$aq_name\" $selected> $aq_name </option>\n";
	}
	
	$sum_select.="</SELECT>\n";

//


		$aq_content.="
	  <table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
		<TR class=k_form>
			<td colspan=2 class=k_formtitle>".label("Options").":</td>
		</TR>
		<TR class=k_form>
			<TD colspan=2 ><a href=\"javascript:popup_ans_list()\">".label("Show custom answers")." ($custom_ans_count)</a></TD>
		</TR>
		<TR class=k_form>
			<TD><INPUT TYPE=\"checkbox\" onclick=\"document.all['src2'].checked = false\" id=\"src1\" value=\"1\" NAME=\"QUESTIONNAIRE[show_scr]\" $src_check>".label("Show only scores")."</TD>
		</TR>
		<TR class=k_form>
			<TD><INPUT TYPE=\"checkbox\" onclick=\"document.all['src1'].checked = false\" id=\"src2\" value=\"1\" NAME=\"QUESTIONNAIRE[show_plus_scr]\" $srcplus_check>".label("Show with scores")."</TD>
		</TR>
		<TR class=k_form>
			<TD><INPUT TYPE=\"checkbox\" value=\"1\" NAME=\"QUESTIONNAIRE[show_graph_button]\" $button_check>".label("Show graphics button")."</TD>
		</TR>
		<TR class=k_form>
			<TD><INPUT TYPE=\"text\" style=\"width:60px\" value=\"$how_long\" NAME=\"QUESTIONNAIRE[how_long]\" class=\"k_input\"> ".label("Seconds between two votes (for the same user)")."</TD>
		</TR>
		 <TR class=k_form>
			<TD><INPUT TYPE=\"text\" style=\"width:60px\" value=\"$precision\" NAME=\"QUESTIONNAIRE[precision]\" class=\"k_input\"> ".label("Percentage display precision")."</TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 >".label("Sum answers with questionary :")." $sum_select</TD>
		</TR>

		<TR class=k_form>
			<td align=\"right\"><img src=\"img/i_save_n.gif\" style=\"cursor:hand\" onClick=\"ZapiszZmiany()\"></td>
		</TR>
		</TABLE>";
	}
	

	$sql = "SELECT aq_name FROM api2_questionnaire 
			WHERE aq_server = $SERVER_ID 
			AND aq_lang = '$lang' GROUP BY aq_name";
	
	$res=$adodb->execute($sql);

	$numrows = $res->RecordCount();

	$aq_select = "<SELECT NAME=\"QUESTIONNAIRE[aq_name]\" onChange=\"showQuestionnaire(this)\" class=\"k_input\">\n";
	$aq_select.= "<option value=\"0\"> ".label("Select")." </option>\n";	
	$aq_select.= "<option value=\"-1\"> ".label("New questionaire")." </option>\n";	
	for($i=0; $i < $numrows ; $i++)
	{
		parse_str(ado_explodename($res,$i));
		$selected = "";
		if ($aq_name == $showq) $selected = "selected";
		$aq_select.= "<option value=\"$aq_name\" $selected> $aq_name </option>\n";
	}
	
	$aq_select.="</SELECT>\n";


	if (!strlen($edit_content)) 
		echo "
		  <table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<TR class=k_form>
				<TD>".label("Questionaire")." : $aq_select</TD>
			</TR>
			</TABLE>";

	if (!strlen($edit_content)) echo $aq_content;
	echo $edit_content;
	
?>

<script>

	function popup_ans_list() 
	{
		msg=open("","Odpowiedzi","scrollbars=yes,toolbar=no,directories=no,width=380,height=500,menubar=no");
				msg.document.write("<HTML><HEAD><TITLE>Ankieta</TITLE></HEAD>");
				msg.document.write("<BODY bgcolor=\"#C0C0C0\">");
				<?
					$other = addslashes(stripslashes($other));
					$o = explode("\n",$other);
					for($i=0; $i < count($o); $i++)
					{
						$o[$i] = addslashes(trim(stripslashes($o[$i])));
						echo "msg.document.write(\"$o[$i]\");\n";
					}
				?>
				msg.document.write("</table><CENTER><br><br><A style=\"color:black\" HREF=\"javascript:window.close()\"><B>Zamknij to okno</B></A></CENTER>");
				msg.document.write("</BODY></HTML>");
	}

	function showQuestionnaire(obj)
	{
		document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&showq='+obj.value;
	}

	function moveQuestion(where,qpri,qid,showq)
	{
		document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&move='+where+'&qid='+qid+'&qpri='+qpri+'&showq='+showq;
	}

	function newQuestion(qid)
	{
		document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&addq='+qid;
	}

	function editQuestion(qid)
	{
		document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&editq='+qid;
	}

	function changeName(qid)
	{
		new_name = document.all["newq"].value;
		if (new_name != qid)
			document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&changeq='+new_name+'&oldname='+qid;
	}

	function deleteQuestion(qid,qname)
	{
		if( confirm('<? echo "Na pewno chcesz usun¹æ t¹ odpowiedŸ ?" ?>') )
			document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&killq='+qid+'&showq='+qname;
	}

	function saveQuestion(qid)
	{
		content = document.all["aq_answer"].value;
		
		if (document.all["aq_input0"].checked) is_input = 0;
		if (document.all["aq_input1"].checked) is_input = 1;
				
		document.location.href = 'tdedit.php?page_id=<?echo $page?>&page=<?echo $page?>&pri=<?echo $pri?>&saveq='+qid+'&content='+content+'&is_input='+is_input;
	}


</script>
