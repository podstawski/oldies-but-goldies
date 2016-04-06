<?

	$place = $costxt;
	$server = $SERVER_ID;

	$now=time();

///
	if ($KAMELEON_MODE)
	{

		$sql = "SELECT count(*) AS wyswietlane FROM api2_baner WHERE 
				ab_place = '$place' AND
				ab_d_start <= $now AND
				(ab_d_end >= $now OR ab_d_end IS NULL) AND
				(ab_limit > ab_count OR ab_limit = 0 OR ab_limit IS NULL) AND
				ab_server = $server";
		parse_str(ado_query2url($sql));

		if (!$wyswietlane)
		{
			$sql = "UPDATE webtd SET hidden=1
					WHERE server = $server 
					AND sid = $sid";
			//$adodb->execute($sql);
			return;
		} 
		else
		{
			$sql = "UPDATE webtd SET hidden=0
					WHERE server = $server 
					AND sid = $sid";
			//$adodb->execute($sql);
		}


		if ($modpos=strpos($SCRIPT_NAME,"modules/@api"))
		{
			$ORIGIN_HOST.=substr($SCRIPT_NAME,0,$modpos-1);

		}
	}
///

	if ($bid)
	{
		$sql="UPDATE api2_baner SET ab_click=ab_click+1 WHERE ab_id=$bid;
				SELECT ab_html,ab_href FROM api2_baner WHERE ab_id=$bid";
		parse_str(ado_query2url($sql));
		

		$href=$ab_href;
		$_href=explode('?',$ab_href);

		
		$href_plus_zero=$_href[0]+0;
		if (strlen($href) && "$_href[0]"=="$href_plus_zero")
		{
			$WEBPAGE->file_name=".baner.php";
			$href=kameleon_href("",$_href[1],$_href[0]);
			
			$href="http://$ORIGIN_HOST/$href";
		}
		$header="Location: $href";

		//echo"$header ... $SCRIPT_NAME ($page)";
		Header($header);
		exit();
	}


	if (strlen($place))
	{

		$sql = "SELECT * FROM api2_baner WHERE 
				ab_place = '$place' AND
				ab_d_start <= $now AND
				(ab_d_end >= $now OR ab_d_end IS NULL) AND
				(ab_limit > ab_count OR ab_limit = 0 OR ab_limit IS NULL) AND
				ab_server = $server
				ORDER BY ab_lastviewed, ab_lastvtime, ab_id LIMIT 1";

		parse_str(ado_query2url($sql));

				

		if (strlen($ab_html))
		{
			$sql = "SELECT plain,bgcolor,costxt FROM webtd WHERE sid = $ab_html
					AND	server = $server"; 
			parse_str(ado_query2url($sql));
			

			$plain=eregi_replace($EREG_REPLACE_KAMELEON_UIMAGES,$UIMAGES,$plain);
		

			$close_button_up = "";
			$close_button_down = "";
			$s_pos = ";position:inline";

			if (strlen(trim($costxt)))
			{
				$popup = explode(":",$costxt);
				$s_pos = ";position:absolute";
				$s_top = ";top:$popup[0]";
				$s_left = ";left:$popup[1]";
				$s_height = ";height:$popup[2]"."px";
				$s_width = ";width:$popup[3]"."px";
				$from_top = $popup[8];

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
						$close_button_up = "<img src=\"$UIMAGES/$popup[6]\" alt=\"$popup[5]\" onClick=\"closeBanner_$sid()\">";
					}
					else
					{
						$close_button_down = "<img src=\"$UIMAGES/$popup[6]\" alt=\"$popup[5]\" onClick=\"closeBanner_$sid()\">";
					}
				} else
				{
					if ($popup[7] < 4)
					{
						$close_button_up = "<TABLE><TR><TD onClick=\"closeBanner_$sid()\">$popup[5]</TD></TR></TABLE>";
					}
					else
					{
						$close_button_down = "<TABLE><TR><TD onClick=\"closeBanner_$sid()\">$popup[5]</TD></TR></TABLE>";
					}
				}

				if (!$KAMELEON_MODE)
				{
					$close_button_up = eregi_replace($EREG_REPLACE_KAMELEON_UIMAGES,$UIMAGES,$close_button_up);
					$close_button_down = eregi_replace($EREG_REPLACE_KAMELEON_UIMAGES,$UIMAGES,$close_button_down);
				}



			}
			if (strlen($bgcolor)) $s_bgcolor=";background-color: #$bgcolor";

			$target=strlen($ab_target)?"target=\"$ab_target\"":"";



			if (!strlen($QS))
			{
				$url=explode("?",$REQUEST_URI);
				$QS="&".$url[1];
			}


			$HOST=$CMS_API_HOST.ereg_replace(".*(/modules/.*)","\\1",$SCRIPT_NAME);
			$HOST=ereg_replace("[/]+","/",$HOST);
			$HOST=ereg_replace("(http[s]*):/([^/])","\\1://\\2",$HOST);

			$dodatkowe="bid=$ab_id&QS=$QS";

			$swf_link=urlencode("$HOST?$dodatkowe");

			//echo "<br>$HOST";



			if (!strlen($from_top)) $from_top = '0';

			$style = "$s_pos $s_top $s_left $s_width $s_height $s_bgcolor";
//			<table width=100%><tr><td id=up_div style=\"cursor:hand $s_width\" $div_align_up>$close_button_up</td></tr></table>
//			<table width=100%><tr><td id=down_div style=\"cursor:hand $s_width;\" $div_align_down>$close_button_down</td></tr></table>
			echo "<form name=\"kam_adv_form_$ab_id\" action=\"$HOST?$dodatkowe\" $target method=\"POST\"></form>";

			if (count($popup))
			{
				echo "<div name=\"banner_div_$sid\" id=\"banner_div_$sid\" style=\"$style\" $div_align_up $div_align_down>";
				echo "<div name=\"banner_up_$sid\" id=\"banner_up_$sid\" style=\"cursor:pointer; position:relative; width:1px; z-Index:100; top:${from_top}px\">$close_button_up</div>";
			}
			echo "<div name=\"div_close_$sid\" id=\"div_close_$sid\" style=\"cursor:pointer; position:relative; z-Index:100;\"  onClick=\"document.kam_adv_form_$ab_id.submit()\">";

			$plain=eregi_replace("\.swf",".swf?clickTag=$swf_link",$plain);

			if (ini_get('magic_quotes_gpc'))
				$plain=stripslashes(trim($plain));
 
			echo $plain;
	/*		for ($i=0; $i < count($plain_line); $i++)
			{
				
				$content = addslashes(stripslashes(trim($plain_line[$i])));
				echo "$content ";
			}
	*/
			$JScript.="
				function closeBanner_$sid()
				{
					document.getElementById('banner_div_$sid').style.display = 'none';
				}
			";
			echo "</div>";
			if (count($popup))
			{
				echo "<div name=\"banner_down_$sid\" id=\"banner_up_$sid\" style=\"cursor:pointer; position:relative; width:1px; z-Index:100; top:${from_top}px\">$close_button_down</div>";
			}
			echo "</div> ";

			if (!strlen($popup[3])) $popup[3] = 0;
			if (!strlen($popup[2])) $popup[2] = 0;

//			echo nl2br(urldecode(htmlspecialchars($plain)));

			if (strlen($popup[4]))
			{
				$JScript.="
					window_width = document.body.clientWidth;
					window_height = document.body.clientHeight - 20;
					var baner = document.getElementById('banner_div_$sid');
					";
				
				switch ($popup[4])
				{
					case 1: $JScript.="
							baner.style.top = Math.ceil(window_height / 2) - Math.ceil($popup[2] / 2);
							baner.style.left = Math.ceil(window_width / 2) - Math.ceil($popup[3] / 2);
							";
							break;
					case 2: $JScript.="
							baner.style.top = 0;
							baner.style.left = Math.ceil(window_width / 2) - Math.ceil($popup[3] / 2);
							";
							break;
					case 3: $JScript.="
							baner.style.top = window_height - $popup[2];
							baner.style.left = Math.ceil(window_width / 2) - Math.ceil($popup[3] / 2);
							";
							break;
					case 4: $JScript.="
							baner.style.top = Math.ceil(window_height / 2) - Math.ceil($popup[2] / 2);
							baner.style.left = window_width - $popup[3];
							";
							break;
					case 5: $JScript.="
							baner.style.top = Math.ceil(window_height / 2) - Math.ceil($popup[2] / 2);
							baner.style.left = 0;
							";
							break;
					case 6: $JScript.="
							baner.style.top = 0;
							baner.style.left = 0;
							";
							break;
					case 7: $JScript.="
							baner.style.top = window_height - $popup[2];
							baner.style.left = 0;
							";
							break;
					case 8: $JScript.="
							baner.style.top = 0;
							baner.style.left = window_width - $popup[3];
							";
							break;
					case 9: $JScript.="
							baner.style.top = window_height - $popup[2];
							baner.style.left = window_width - $popup[3];
							";
							break;

				}
		}


//		$adodb->debug = true;
			
			$sql = "UPDATE api2_baner SET
					ab_lastviewed = $now,
					ab_lastvtime = $now,
					ab_count = ab_count + 1
					WHERE ab_id = $ab_id";

			$adodb->execute($sql);
		}
		$adodb->debug = false;

	}
	$adodb->debug = 0;


	
?>