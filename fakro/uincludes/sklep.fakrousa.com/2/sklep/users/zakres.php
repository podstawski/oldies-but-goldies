<?
	include ("$SKLEP_INCLUDE_PATH/js/calendar.php");

	if (!$size) $size=7;

	if (!strlen($_REQUEST[c_data_od]))
	{
			$_REQUEST[c_data_od]=date("d-m-Y",$NOW-3600*24*$size);
			$_REQUEST[c_data_do]=date("d-m-Y",$NOW);
	}
?>
<form class="zakres_dat">
<input name="c_data_od" value="<?echo $_REQUEST[c_data_od]?>" style="width:75"
        class="forminput" onChange="document.cookie=this.name+'='+this.value"> -
<input name="c_data_do" value="<?echo $_REQUEST[c_data_do]?>" style="width:75"
        class="forminput" onChange="document.cookie=this.name+'='+this.value">


<a onclick="if (document.list_sort_form!=null) {document.list_sort_form.s_ile.value=''; document.list_sort_form.s_start.value=0;document.list_sort_form.submit(); return false;}" 
	href="<?echo $self?>"><img src="<?echo $SKLEP_IMAGES?>/sb/refr_n.gif" border=0 align="absMiddle"></a>
</form>

<script>
		IMAGES='<?echo $SKLEP_IMAGES?>';
        document.cookie='c_data_od=<?echo $_REQUEST[c_data_od]?>';
        document.cookie='c_data_do=<?echo $_REQUEST[c_data_do]?>';
</script>

<script type="text/javascript">

ie4 = (document.all)? true:false;

if (ie4)
{
		Calendar.setup({
			inputField     :    "c_data_od",      // id of the input field
			ifFormat       :    "%d-%m-%Y",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "c_data_od",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			firstDay	   :    1,
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
		Calendar.setup({
			inputField     :    "c_data_do",      // id of the input field
			ifFormat       :    "%d-%m-%Y",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "c_data_do",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			firstDay	   :    1,
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
}
</script>
