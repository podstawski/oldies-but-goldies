<?
	include ("$SKLEP_INCLUDE_PATH/js/calendar.php");

		if (!strlen($_REQUEST[k_data_od]))
        {
                //$_REQUEST[k_data_od]=date("d-m-Y",$NOW-3600*24*750);
                $_REQUEST[k_data_do]=date("d-m-Y",$NOW);
        }
?>
<form class="zakres_dat" action="<? echo $self ?>" method="POST">
<? echo sysmsg('th_town','system')?>: <input name="list[miasto]" value="<?echo $LIST[miasto]?>" style="width:163" class="forminput">
<br><? echo sysmsg('th_company','system')?>: <input name="list[nazwa]" value="<?echo $LIST[nazwa]?>" style="width:163" class="forminput">
<br><? echo sysmsg('th_register date','system')?>: 
<input name="k_data_od" value="<?echo $_REQUEST[k_data_od]?>" style="width:75"
        class="forminput" onChange="document.cookie=this.name+'='+this.value"> -
<input name="k_data_do" value="<?echo $_REQUEST[k_data_do]?>" style="width:75"
        class="forminput" onChange="document.cookie=this.name+'='+this.value">

<br>
<input type="submit" value="Search" class="addbut">
</form>

<script>
        document.cookie='k_data_od=<?echo $_REQUEST[k_data_od]?>';
        document.cookie='k_data_do=<?echo $_REQUEST[k_data_do]?>';
</script>

<script type="text/javascript">

		Calendar.setup({
			inputField     :    "k_data_od",      // id of the input field
			ifFormat       :    "%d-%m-%Y",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "k_data_od",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			firstDay	   :    1,
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
		Calendar.setup({
			inputField     :    "k_data_do",      // id of the input field
			ifFormat       :    "%d-%m-%Y",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "k_data_do",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			firstDay	   :    1,
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});

</script>
