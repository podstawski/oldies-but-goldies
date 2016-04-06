<?
	$GENERATE_ONLY_WEBPAGE_OBJECT=1;

	if (!$trans_goto) return;

	include_once('include/transfun.h');
	include_once('include/utf8.h');

	$sql="SELECT * FROM webtrans WHERE wt_sid=$trans_goto";
	parse_str(ado_query2url($sql));

	if ($wt_verification)
	{
		$sql="UPDATE webtrans SET wt_verification=NULL WHERE wt_sid=$trans_goto AND wt_server=$SERVER_ID";
		$adodb->execute($sql);
		
	}
	else
	{

		if (!strstr($wt_o_html,'<')) $new_wt_t_html='wt_t_plain';
		else
		{
			$html=stripslashes($wt_o_html);
			$wynik='';
			$current_o_pos=0;

			//echo htmlspecialchars($html).'<br><br>';

			while(is_array($transmatrix) && list($positions,$part)=each($transmatrix))
			{
				$part=trim(stripslashes($part));

				$p=explode(':',$positions);
				$p[0]+=0;
				$p[1]+=0;
				if ($p[0]>=$p[1]) continue;

				//echo "($current_o_pos) $p[0]-$p[1] $part<br>";

				$wynik.=substr($html,$current_o_pos,$p[0]-$current_o_pos);
				$wynik.=utf82lang($part);
				$current_o_pos=$p[1];


			}
			if (is_array($transmatrix)) $wynik.=substr($html,$current_o_pos);
			else $wynik=$html;

			$wynik=addslashes($wynik);
			if (strlen($wynik)) $new_wt_t_html="'$wynik'";
			//echo htmlspecialchars($wynik);

		}
		
		if (isset($new_wt_t_html))
		{
		
			$query="UPDATE webtrans SET 
					wt_t_html=$new_wt_t_html,
					wt_verification=".$adodb->now.",
					wt_verificator='".$kameleon->user[username]."'
					WHERE wt_sid=$trans_goto AND wt_server=$SERVER_ID";

			$adodb->execute($query);

			$wt_t_html=addslashes(stripslashes(kameleon_webtrans($trans_goto,true)));

			$query="UPDATE webtrans SET 
					wt_t_html='$wt_t_html'
					WHERE wt_sid=$trans_goto AND wt_server=$SERVER_ID";

			$adodb->execute($query);
		}
	}


	//return;


	if ($trans_ch_nv) $trans_goto=$trans_next;
?>
<script>
	top.location.href='index.php?trans_goto=<?echo $trans_goto?>';
</script>