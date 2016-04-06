<?
	$LIST[id]=$CIACHO[admin_to_id];

	$to_id = $LIST[id];
	
	if (!strlen($to_id)) return;

	echo sysmsg($costxt,"system");

	$sql = "SELECT * FROM grupy_towarow 
			WHERE $to_id IN (gt_to_id1, gt_to_id2) 
			AND gt_grupa = '$costxt'";
	
	$res = $projdb->execute($sql);

	$select = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$sql = "SELECT to_indeks, to_nazwa FROM towar WHERE to_id IN ($gt_to_id1, $gt_to_id2) 
				AND to_id <> $to_id";
		parse_str(ado_query2url($sql));
		$select.= "<option value=\"$gt_id\">[$to_indeks] $to_nazwa</option>\n";
	}

	$indx = "indeks produktu";

	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<TR>
		<TD class=\"c4\" valign=\"top\">
			<INPUT TYPE=\"text\" NAME=\"form[new_indx]\" style=\"width:150px\" onClick=\"indxOnClick_$sid(this)\" onBlur=\"indxOnBlur_$sid(this)\" value=\"$indx\">
		</TD>
		<TD class=\"c4\" valign=\"top\" style=\"text-align:center\">
			<INPUT TYPE=\"button\" NAME=\"\" value=\"&raquo\" onClick=\"modGroup_$sid('in')\" style=\"width:20px; margin-bottom: 10px\"><br>
			<INPUT TYPE=\"button\" NAME=\"\" value=\"&laquo\" onClick=\"modGroup_$sid('out')\" style=\"width:20px\">
		</TD>
		<TD class=\"c4\" valign=\"top\" style=\"text-align:right\">
			<SELECT NAME=\"form[indx]\" size=\"10\" style=\"width:350px\">
			$select
			</SELECT>
		</TD>
	</TR>
	</TABLE>
	";

	$method = $KAMELEON_MODE?"POST":"GET";


	echo "
	<FORM METHOD=$method ACTION=\"$self\" name=\"ChangeModForm_$sid\">
	<INPUT TYPE=\"hidden\" name=\"form[grupa]\" value=\"$costxt\">
	<INPUT TYPE=\"hidden\" name=\"form[akcja]\" value=\"\" id=\"ack_$sid\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ModyfikujGrupe\">
	$table
	</FORM>";


?>
<script>
	function modGroup_<?echo $sid?>(act)
	{
		ak = getObject('ack_<?echo $sid?>');
		ak.value = act;
		frm = getObject('ChangeModForm_<?echo $sid?>');
		frm.submit();
	}

	function indxOnClick_<?echo $sid?>(obj)
	{
		if (obj.value=='<?echo $indx?>') obj.value='';
	}

	function indxOnBlur_<?echo $sid?>(obj)
	{
		if (obj.value.length == 0) obj.value='<?echo $indx?>';
	}

</script>
