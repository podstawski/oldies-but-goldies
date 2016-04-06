
  <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>calendar-win2k-cold-1.css" title="win2k-cold-1" />

  <!-- main calendar program -->
  <script type="text/javascript" src="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>lang/calendar-en.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>calendar-setup.js"></script>



<!--
Przyk³ad:

<form action="#" method="get">
<input type="text" name="date" id="f_date_b" /><button type="reset" id="f_trigger_b">...</button>
</form>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",      // id of the input field
        ifFormat       :    "%d-%m-%Y",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
		firstDay	   :    1,
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });

</script>


-->


