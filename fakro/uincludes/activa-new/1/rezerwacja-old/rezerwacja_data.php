<?
unset($_SESSION['rezerwacja']['id']);
?>

<script type="text/javascript">
	var languageCode = 'pl';
	var pathToImages = '<?=$_path?>images/';
</script>

<link rel="stylesheet" href="<?=$_path?>css/calendar-1.css?random=20051112" media="screen">
<script type="text/javascript" src="<?=$_path?>js/calendar.js?random=20060118"></script>

<div align="center">

<form action="<?=$_action;?>" method="post" id="rezerwacja_form" onsubmit="return checkData(this);">
<input type="hidden" name="mode" value="akcja">
<input type="hidden" value="" name="calendar_field" id="calendar_field"/>
<table>
<tr>
	<td>Data przyjazdu: <input type="text" value="" onmousedown="this.className='date_over'" onclick="displayCalendar(document.getElementById('rezerwacja_form').rezerwacja_date_od,'yyyy-mm-dd',this); calendarField('od');" readonly="" name="rezerwacja_date_od" id="rezerwacja_date_od" class="date"/></td>
	<td>Data wyjazdu: <input type="text" value="" onmousedown="this.className='date_over'" onclick="displayCalendar(document.getElementById('rezerwacja_form').rezerwacja_date_do,'yyyy-mm-dd',this); calendarField('do');" readonly="" name="rezerwacja_date_do" id="rezerwacja_date_do" class="date"/></td>
	<td align="right"><input type="submit" value="Sprawdź dostępność"></input></td>
</tr>
</table>
</form>

</div>
