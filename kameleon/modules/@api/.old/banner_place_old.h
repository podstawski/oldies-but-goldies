<?
	global $CMS_API_HOST;
	$API_VARS=array("sid","CMS_API_HOST");
	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.h");
		return;
	}

	//echo "<script src=\"adv.php?place=$link\"></script>";


	$place = $costxt;
	$server = $SERVER_ID;

	if ($id)
	{
		$sql="UPDATE api2_baner SET ab_click=ab_click+1 WHERE ab_id=$id;
				SELECT ab_html,ab_href FROM api2_baner WHERE ab_id=$id";
		parse_str(ado_query2url($sql));
		
		Header("Location: $ab_href");
		exit();
	}


	if (strlen($place))
	{

		$sql = "SELECT * FROM api2_baner WHERE 
				ab_place = '$place' AND
				ab_d_start <= CURRENT_DATE AND
				(ab_d_end >= CURRENT_DATE OR ab_d_end IS NULL) AND
				(ab_limit >= ab_count OR ab_limit = 0 OR ab_limit IS NULL) AND
				ab_server = $server
				ORDER BY ab_lastviewed, ab_lastvtime, ab_id LIMIT 1";

		parse_str(ado_query2url($sql));

				

		if (strlen($ab_html))
		{
			$sql = "SELECT plain,bgcolor,costxt FROM webtd WHERE sid = $ab_html
					AND	server = $server"; 
			parse_str(ado_query2url($sql));

			$plain_line = explode("\n",$plain);

			$close_button_up = "";
			$close_button_down = "";
			
			if (strlen(trim($costxt)))
			{
				$popup = explode(":",$costxt);
				$s_pos = ";position:absolute";
				$s_top = ";top:$popup[0]";
				$s_left = ";left:$popup[1]";
				$s_height = ";height:$popup[2]"."px";
				$s_width = ";width:$popup[3]"."px";
				
				if (strlen($popup[7]))
				{
					switch ($popup[7])
					{
						case 1: $div_align_up = "align=left";break;
						case 2: $div_align_up = "align=center";break;
						case 3: $div_align_up = "align=right";break;
						case 4: $div_align_down = "align=left";break;
						case 5: $div_align_down = "align=center";break;
						case 6: $div_align_down = "align=right";break;
					}
				}

				if (strlen($popup[6]))
				{
					if ($popup[7] < 4)
					{
						$close_button_up = "<img src=\"$UIMAGES/$popup[6]\" alt=\"$popup[5]\" onClick=\"closeBanner()\">";
					}
					else
					{
						$close_button_down = "<img src=\"$UIMAGES/$popup[6]\" alt=\"$popup[5]\" onClick=\"closeBanner()\">";
					}
				} else
				{
					if ($popup[7] < 4)
					{
						$close_button_up = "<TABLE><TR><TD onClick=\"closeBanner()\">$popup[5]</TD></TR></TABLE>";
					}
					else
					{
						$close_button_down = "<TABLE><TR><TD onClick=\"closeBanner()\">$popup[5]</TD></TR></TABLE>";
					}
				}

			}
			if (strlen($bgcolor)) $s_bgcolor=";background-color: #$bgcolor";

			$target=strlen($ab_target)?"target=\"$ab_target\"":"";

			echo "document.writeln('<form name=\"kam_adv_form_$ab_id\" action=\"$CMS_API_HOST/$SCRIPT_NAME\" $target><input type=\"hidden\" name=\"id\" value=\"$ab_id\"></form>');\n";
			echo "document.writeln('<div name=\"banner_div\" id=\"banner_div\" style=\"cursor:hand; z-Index:50 $s_pos $s_top $s_left $s_width $s_height $s_bgcolor\" onClick=\"document.kam_adv_form_$ab_id.submit()\">');\n";
			echo "document.writeln('<table width=100%><tr><td id=up_div style=\"cursor:hand $s_width\" $div_align_up>$close_button_up</td></tr></table>');\n";

			for ($i=0; $i < count($plain_line); $i++)
			{
				//addslashes(
				$content = stripslashes(trim($plain_line[$i]));
				echo "document.writeln('$content ');\n";
			}
			echo "document.writeln('<script>');\n";
			echo "document.writeln('function closeBanner()');\n";
			echo "document.writeln('{');\n";
			echo "document.writeln(\"document.all['banner_div'].style.display = 'none'\");\n";
			echo "document.writeln('}');\n";
			echo "document.writeln('</script>');\n";
			echo "document.writeln('<table width=100%><tr><td id=down_div style=\"cursor:hand $s_width;\" $div_align_down>$close_button_down</td></tr></table>');\n";
			echo "document.writeln('</div> ');\n";

/*
			if (strlen($popup[4]))
			{
				echo "<script>\n
					window_width = document.body.clientWidth;\n
					window_height = document.body.clientHeight;\n
					baner = document.banner_div;\n
				";

				switch ($popup[4])
				{
					case 1: echo "
							baner.style.top = window_width - baner.scrollHeight;\n
							";

				}
				echo "</script>\n";
			}
*/

//		$adodb->debug = true;
			$sql = "UPDATE api2_baner SET
					ab_lastviewed = CURRENT_TIMESTAMP,
					ab_lastvtime = CURRENT_TIME,
					ab_count = ab_count + 1
					WHERE ab_id = $ab_id";

			$adodb->execute($sql);
		}
		$adodb->debug = false;

	}
	$adodb->debug = 0;


	
?>
