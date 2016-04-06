<?
	global $SZABLON_PATH,$lang;	

	$calendar_css="$INCLUDE_PATH/calendar/calendar.css";
	if (file_exists("$SZABLON_PATH/modules/crm/calendar.css"))
		$calendar_css="$SZABLON_PATH/modules/crm/calendar.css";


?>

<link rel="stylesheet" type="text/css" media="all" 
	href="<?echo $calendar_css?>" title="win2k-1" />

<script type="text/javascript" src="<?echo $INCLUDE_PATH?>/calendar/calendar_stripped.js"></script>
<script type="text/javascript" src="<?echo $INCLUDE_PATH?>/calendar/calendar_lang_<?echo $lang?>.js"></script>
<script type="text/javascript" src="<?echo $INCLUDE_PATH?>/calendar/calendar_main.js"></script> 

<script type="text/javascript">
function crm_calendar(id)
{
	showCalendar(id,'dd-mm-y');
}
</script>