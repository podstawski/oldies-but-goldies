<?php
	
	$query="SELECT count(*) AS c FROM servers";
	parse_str(ado_query2url($query));

	echo "
	
	<div style=\"width: 500px; margin: 60px auto 60px auto; border: 1px solid #d3d3d3; \">	
	  <div class=\"secname\">".label("License")."</div>
    <table class=\"tabelka\" cellpadding=\"0\" cellspacing=\"0\">
			<tr class=\"line_0\"><td>".label("License name").": </td><td><b>$CONST_LICENSE_NAME</b></td></tr>
			<tr class=\"line_1\"><td>".label("Valid till").": </td><td><b>".FormatujDate($CONST_LICENSE_VALID, 0)."</b></td></tr>
			<tr class=\"line_0\"><td>".label("Maximum number of servers").": </td><td><b>$CONST_LICENSE_SERVERS</b></td></tr>
			<tr class=\"line_1\"><td>".label("Currently maintained servers").": </td><td><b>$c</b></td></tr>
			<tr class=\"line_0\"><td valign=\"top\">".label("Hosts allowed").": </td><td><b>";

	$host=explode(":",$CONST_LICENSE_HOST);
	for ($i=0;$i<count($host) ; $i++)
	{
		if (!strlen(trim($host[$i]))) continue;
		if ($h) echo ",<br>";
		echo $host[$i];
		$h++;
	}
			
	echo "</b></td></tr>
		</table></div>";

	include('include/action/deleteduplicateidentyfiers.h');
?>
