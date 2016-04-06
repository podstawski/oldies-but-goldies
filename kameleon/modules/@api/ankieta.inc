<?
	global $show_score, $size, $how_long, $answer, $IMAGES, $INPUTS, $REMOTE_ADDR, $VOTE, $ankieta_id;


	$API_VARS=null;
	if (strlen($show_score)) $API_VARS[]="show_score";
	if (strlen($answer)) $API_VARS[]="answer";
	if (strlen($ankieta_id)) $API_VARS[]="ankieta_id";
	if (is_array($INPUTS)) $API_VARS[]="INPUTS";
	if (is_array($VOTE)) $API_VARS[]="VOTE";

	//$API_VARS=array("show_score","answer","ankieta_id","INPUTS","VOTE");

	$API_CLASS="api2_quest_table";

	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
	}
	else
	{
		include("$INCLUDE_PATH/.api/ankieta.h");
		return;

	}

?>

<script>

	function isSelected<? echo "_$sid" ?>()
	{
		document.ankietaf<? echo "_$sid" ?>.isselected.value = 1;
	}

	function setSelected<? echo "_$sid" ?>(r_id,my_id)
	{
		if (document.all[my_id].value.length > 0)
		{
			document.all[r_id].checked = true;
			isSelected<? echo "_$sid" ?>();
		}
	}

	function sendVote<? echo "_$sid" ?>()
	{
			if (document.ankietaf<? echo "_$sid" ?>.isselected.value == 0)
			{
				alert('Nie zaznaczono odpowiedzi!');
				return;
			}

		<?
			if (!$VOTE[$aq_name])
			{
				echo "document.ankietaf_$sid.submit();";
			}
			else
			{
				echo "alert('Odda³e(a)œ ju¿ swój g³os w tej ankiecie');";
			}

		?>
	}
	
</script>