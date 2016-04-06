<style>
.c_task {
	padding: 3px;
	border: 0px solid #c0c0c0;
	background-Color: #e0e0e0;
}
.c_task_td {
	padding: 3px;
	background-Color: #ffffff;
}
.c_task_td_lp {
	padding: 3px;
	background-Color: #e0e0e0;
}
.c_task_td_gantt {
	padding: 0px;
	border: 0px;
	background-Color: #ffffff;
}

.c_gantt_task {
	height:10px;
	background-color:lightblue;
}

.c_gantt_task_user {
	height:10px;
	background-color:lightgreen;
}

.c_gantt_task_offset {
	height:10px;
	background-color:#ffffff;
}

.c_gantt_proc {
	height:3px;
	background-color:black;
}
.c_gantt_proc_offset {
	height:5px;
	background-color:#ffffff;
}


</style>
<?
	global $KAMELEON;

	$conf=xml2obj($costxt);
	$conf=$conf->xml;

	$person=$conf->person;
	$username=$conf->username;

	if ($username==0) $username="";
	if ($username==1) $username=$KAMELEON[username];

//	echo "$person = $username <br>";
//	echo "$conf->table <br>";

//	include("$INCLUDE_PATH/".ereg_replace("crm","task_calendar",$conf->table)).".h";




//	authot: Robert Posiadala
//	date: 24-04-2003

//$person;
//$username;

//stale
// w tablicy $C_CALENDAR

include_once ("$INCLUDE_PATH/task_calendar_fun.h");

global $C_CALENDAR;
$C_CALENDAR["plus"]="[+]";
$C_CALENDAR["minus"]="[-]";


// stale do kalendarza
$t=getdate(time());
$C_CALENDAR["today"]=sprintf("%02d-%02d-%04d",$t["mday"],$t["mon"],$t["year"]);

$C_CALENDAR["today_sql"]=sprintf("%04d-%02d-%02d",$t["year"],$t["mon"],$t["mday"]);
$C_CALENDAR["month"]=sprintf("%02d",$t["mon"]);
$C_CALENDAR["year"]=sprintf("%04d",$t["year"]);

$C_CALENDAR["time"]=sprintf("%02d:00",$t["hours"]);


$C_CALENDAR["first_day"]= $C_CALENDAR["year"]."-".$C_CALENDAR["month"]."-01";
$C_CALENDAR["last_day"]=date("d",mktime(0,0,0,$C_CALENDAR["month"]+1,0,$C_CALENDAR["year"]));
$C_CALENDAR["last_day_sql"] = $C_CALENDAR["year"]."-".$C_CALENDAR["month"]."-".$C_CALENDAR["last_day"];

$C_CALENDAR["indent"]=20;
$C_CALENDAR["gantt_width"]=15;





//***********************************************
for ($i=1; $i<=$C_CALENDAR["last_day"]; $i++)
{
	$width=$C_CALENDAR["gantt_width"];
	$GANTT_TITLE.="<td align=center class=c_task_td_gantt style='width=${width}px;height:9px;font-size:10px;background-color:silver;'>$i</td>";
}
$GANTT_TITLE="<table cellpadding=0 cellspacing=0><tr>$GANTT_TITLE</tr></table>";


$lp=0;
switch($conf->table)
{
	case "crm_proc":
		$calendar = calendar_proc(0,$person,$username);
		break;

	case "crm_proc_hist":
		$calendar = calendar_proc_hist($lp,0,0,$person,$username);
		break;

	case "crm_task":
		$calendar = calendar_tasks($lp,0,0,$person,$username);
		$GANTT_TITLE="";
		break;


	default:
		;
}


$thead.="
	<tr>
		<td class=c_task_td_lp></td>
		<td class=c_task_td>Task name</a></td>
		<td class=c_task_td>Duration</td>
		<td class=c_task_td>Author</td>
		<td class=c_task_td>Executive</td>
		<td class=c_task_td_gantt>$GANTT_TITLE</td>
	</tr>
";


echo "
	<table cellspacing=1 class=c_task>
		$thead
		$calendar
	</table>";


?>