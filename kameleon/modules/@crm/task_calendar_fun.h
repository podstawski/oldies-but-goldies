<?
//	autor:	Robert Posiadala
//	data:	25-04-2003
//	modyfikacje:
//
// funkcje

function calendar_grantt($style,$duration_offset,$duration,$duration_suffix)
{
	global $C_CALENDAR;

	if ($duration_suffix<0) $duration_suffix=0;
	$width_suffix=$duration_suffix*$C_CALENDAR["gantt_width"];
	$width_offset=$duration_offset*$C_CALENDAR["gantt_width"];
	$width=$duration*$C_CALENDAR["gantt_width"];
	if ($width_offset>0 && $width>0)
		$GANTT="<table cellpadding=0 cellspacing=0 border=0>
					<tr>
						<td class=${style}_offset style='width:${width_offset}px;'></td>
						<td class=${style} style='width:${width}px;'></td>
						<td class=${style}_offset style='width:${width_suffix}px;'></td>
					</tr>
				</table>";
	else
		$GANTT="";
	return $GANTT;
}

function calendar_tab($tab)
{
	global $C_CALENDAR;
	if ($tab > 0)
	{
		$width=$tab * $C_CALENDAR["indent"];
//		echo $width;
		$offset="<table align=left>
					<tr>
						<td style='width:${width}px;height:1px;'></td>
					</tr>
				</table>";
	}
	else
		$offset="";
	return $offset;
}


function calendar_tasks(&$lp,$proc,$tab,$person,$username)
{
	global $SERVER_ID,$adodb,$C_CALENDAR;

	$tab++;

	$f_day=$C_CALENDAR["first_day"];
	$l_day=$C_CALENDAR["last_day_sql"];

	if (strlen($username))
		$war_user="AND t$person='$username'";
	else
		$war_user="";

	if ($proc != 0)
		$war_proc=" AND t_proc_state=$proc";
	else
		$war_proc="";

	$query="
		SELECT *,t_d_deadline-t_d_start +1 AS duration, t_d_start - '$f_day' AS duration_offset,
			'$l_day' - t_d_deadline +1 AS duration_suffix
		FROM crm_task
		WHERE 
			t_server=$SERVER_ID
			$war_proc
			$war_user
		";
//	echo $query."<br>";return;
	$res=$adodb->Execute($query);
	if ($res)
		$count=$res->RecordCount();

	$table_tr="";
	for ($i=0;$i<$count;$i++)
	{
		$lp++;
		parse_str(ado_ExplodeName($res,$i));
		$href=kameleon_href("","",$t_page_id);


		if ($username==$t_executive)
			$styl="c_gantt_task_user";
		else
			$styl="c_gantt_task";
		
		$GANTT=calendar_grantt($styl,$duration_offset,$duration,$duration_suffix);
		$wciecie=calendar_tab($tab);
		if ($proc==0)
		{
			$GANTT="</td><td class=c_task_td_gantt>$t_d_start</td><td class=c_task_td_gantt>$t_d_deadline";
			$wciecie="";
		}
		//prezentacja zadan
		$table_tr.="
			<tr>
				<td class=c_task_td_lp>$lp</td>
				<td class=c_task_td>$wciecie<a href=$href>$t_title</a>/$username</td>
				<td class=c_task_td>$duration</td>
				<td class=c_task_td>$t_author</td>
				<td class=c_task_td>$t_executive</td>
				<td class=c_task_td_gantt>$GANTT</td>
			</tr>
		";
	}
	return $table_tr;

} // function calendar_tasks($proc)

function calendar_proc_hist(&$lp,$proc,$tab,$person,$username)
{
	global $SERVER_ID,$adodb,$C_CALENDAR;

	$tab++;
	$f_day=$C_CALENDAR["first_day"];
	$l_day=$C_CALENDAR["last_day_sql"];

	if (strlen($username))
		$war_user="AND ph_author='$username'";
	else
		$war_user="";

	if ($proc != 0)
		$war_proc=" AND ph_proc=$proc";
	else
		$war_proc="";


	$query="
		SELECT *,ph_d_deadline-ph_d_start +1 AS duration, ph_d_start - '$f_day' AS duration_offset,
				'$l_day' - ph_d_deadline +1 AS duration_suffix
		FROM crm_proc_hist
		WHERE 
			ph_server=$SERVER_ID
			$war_user $war_proc
		ORDER BY ph_d_start
		";
	$res=$adodb->Execute($query);
	if ($res)
		$count=$res->RecordCount();

	$table_tr="";
	for ($i=0;$i<$count;$i++)
	{
		$lp++;
		parse_str(ado_ExplodeName($res,$i));
		$href=kameleon_href("","",$ph_page_id);

		$GANTT=calendar_grantt("c_gantt_proc",$duration_offset,$duration,$duration_suffix);

		$wciecie=calendar_tab($tab);
		//prezentacja podprocesow
		$table_tr.="
			<tr>
				<td class=c_task_td_lp>$lp</td>
				<td class=c_task_td>$wciecie<a href=$href><B>$ph_title</B></a></td>
				<td class=c_task_td>$duration</td>
				<td class=c_task_td>$ph_author</td>
				<td class=c_task_td>$ph_executive</td>
				<td class=c_task_td_gantt>$GANTT</td>
			</tr>
		";
		$table_tr.=calendar_tasks(&$lp,$ph_id,$tab,$person,$username);
	}
	return $table_tr;

} // function calendar_proc_hist(&$lp,$proc)


function calendar_proc($proc,$person,$username)
{
	global $SERVER_ID,$adodb,$C_CALENDAR;

	$f_day=$C_CALENDAR["first_day"];
	$l_day=$C_CALENDAR["last_day_sql"];

	if (strlen($username))
		$war="AND p$person='$username'";
	else
		$war="";
	$query="
		SELECT *,p_d_deadline-p_d_start+1 AS duration ,
			p_d_start - '$f_day' AS duration_offset,
			'$l_day' - p_d_deadline + 1 AS duration_suffix
		FROM crm_proc
		WHERE 
			p_server=$SERVER_ID
			AND p_d_deadline IS NOT NULL
			AND (p_d_start >= '$f_day' OR p_d_start <= '$l_day')
			$war
		";
	//		AND (p_d_start <= '$f_day'  OR p_d_deadline<='$l_day' )
//	echo $query;return;
	$res=$adodb->Execute($query);
	if ($res)
		$count=$res->RecordCount();
	$proc="";
	$proc_tr="";
	$lp=0;
	for ($i=0;$i<$count;$i++)
	{
		$lp++;
		parse_str(ado_ExplodeName($res,$i));
		$href=kameleon_href("","",$p_page_id);

		$GANTT=calendar_grantt("c_gantt_proc",$duration_offset,$duration,$duration_suffix);
		//prezentacja glownych procesow - naczelnych
		$proc_tr.="
			<tr>
				<td class=c_task_td_lp>$lp</td>
				<td class=c_task_td><a href=$href><B>$p_title</B></a></td>
				<td class=c_task_td>$duration</td>
				<td class=c_task_td>$p_author</td>
				<td class=c_task_td></td>
				<td class=c_task_td_gantt>$GANTT</td>
			</tr>
		";
		// prezentacja zadan jesli s¹
//		$proc_tr.=calendar_tasks($lp,$p_id,0,$person,$username);
		// prezentacja podprocesow
		$proc_tr.=calendar_proc_hist($lp,$p_id,0,$person,$username);
	}
	return $proc_tr;
} //calendar_proc($proc,$person,$username)
?>