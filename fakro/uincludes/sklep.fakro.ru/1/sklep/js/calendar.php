<?
	switch ($lang)
	{
		case 'i':
			$clang='pl-iso';
			break;

		case 'fr':
		case 'de':
		case 'sp':
		case 'nl':
			$clang=$lang;
			break;


		case 'r':
		case 'ru':
			$clang='ru_win_';
			$clang='ru';
			break;

		default:
			$clang='en';
			break;
	}


/*	$txt=implode('',file("$SKLEP_INCLUDE_PATH/js/calendar/lang/calendar-ru-utf8.js"));
	$txt=enc2enc($txt,'utf-8','iso-8859-5');
	$f=fopen('/tmp/calendar-ru.js','w');
	fwrite($f,$txt);
	fclose($f);
*/
?>



  <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>calendar-win2k-cold-1.css" title="win2k-cold-1" />

  <!-- main calendar program -->
  <script type="text/javascript" src="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>lang/calendar-<?echo $clang?>.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="<? echo "$SKLEP_INCLUDE_PATH/js/calendar/"?>calendar-setup.js"></script>


  <?
	return;

  ?>

<!--
PrzykГad:

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


