<?
	global $STATE;

	module_select($MODULES->crm->files->state);
		
	if (!strlen($STATE[ps_title])) 
	{
		$query="SELECT title FROM webpage 
			WHERE server=$SERVER_ID AND ver=$ver
			AND lang='$lang' AND id=$page_id";
		parse_str(ado_query2url($query));
		$STATE[ps_title]=$title;
	}
	if ($STATE[ps_complete]) $STATE[ps_complete_checked]="checked";
	$STATE[ps_proc_init].="";
	$STATE[ps_time].="";

	_display_form($MODULES->crm->files->state);
?>
