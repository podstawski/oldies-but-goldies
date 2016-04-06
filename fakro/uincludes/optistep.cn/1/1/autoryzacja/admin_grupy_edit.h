<?
	include ("$INCLUDE_PATH/autoryzacja/config.inc.php");

	global $sg_id, $this_rights;

	if (strlen($sg_id))
	{
		$sql = "SELECT * FROM system_grupa
		WHERE sg_id = $sg_id";
		parse_str(query2url($sql));

		$sql = "SELECT * FROM system_acl_obiekt
				WHERE sao_grupa_id = $sg_id
				AND sao_server = $SERVER_ID";
		$res = pg_exec($db,$sql);
		$this_rights = array();
		for ($i=0; $i < pg_numrows($res); $i++)
		{
			parse_str(pg_explodename($res,$i));
			$this_rights[] = $sao_klucz;
		}
		if ($sg_admin) $achck = "checked";
	}


//	if ($show_page_tree)
		$strony = printNode($_AUTH_TREE_ROOT,$_AUTH_PRE_SELECT);
	

	echo "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszPrawa\">
	<INPUT TYPE=\"hidden\" name=\"grupa_id\" value=\"$sg_id\">
	<TABLE width=\"100%\" border=0 style=\"\">
	<TR>
		<TD>nazwa grupy <INPUT TYPE=\"text\" NAME=\"nazwa_grupy\" value=\"$sg_nazwa\" class=\"ilong\"></TD>
		<TD>
			<INPUT TYPE=\"button\" value=\"Anuluj\" class=\"sys_button\" onClick=\"document.goBackForm.submit()\">	<INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"sys_button\">
		</TD>
	</TR>
	<TR>
		<TD colspan=2 align=left>
		<TABLE width=\"100%\">
		<TR>
			<TD valign=top>$strony</TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
<!--	
	<TR>
		<TD align=\"center\" width=\"50%\"><INPUT TYPE=\"button\" value=\"Anuluj\" class=\"fb\" onClick=\"document.goBackForm.submit()\"></TD>
		<TD align=\"center\" width=\"50%\"><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"ex_button\"></TD>
	</TR>
-->
	</TABLE>
	</FORM>	
	<FORM METHOD=POST ACTION=\"$next\" name=\"goBackForm\">	
	</FORM>
		";

	function printNode($id,$preselect = 0)
	{
		global $db, $this_rights;
		$sql = "SELECT * FROM system_obiekt
				WHERE so_parent = $id";
		$res = pg_exec($db,$sql);
		if (!pg_numrows($res)) return "";
		$ret = "<TABLE width=\"100%\" id=\"table_$id\">";
		for ($i=0; $i < pg_numrows($res); $i++)
		{
			parse_str(pg_explodename($res,$i));
			$search_id = ereg_replace("p_","",$so_klucz);
			$node = printNode($search_id,$preselect);
			if (strlen($node)) 
				$znak = "-";
			else
				$znak = "";
			if ($preselect)
				$chck = "checked";
			if (is_array($this_rights))
				if (!in_array($so_klucz,$this_rights))
					$chck = "";
				else
					$chck = "checked";

			//style=\"visibility:hidden;display:none\"
			$ret.="
			<TR>
				<TD id=\"td_$so_klucz\" valign=\"middle\" style=\"cursor:hand\" 
				width=\"15\" onMouseDown=\"changeNode('$so_klucz')\">$znak</TD>
				<TD nowrap valign=\"middle\">
				<INPUT TYPE=\"checkbox\" onClick=\"changeRights('table_$search_id',this.checked)\" NAME=\"PRAWA[$so_klucz]\" $chck value=\"1\">
				$so_nazwa ($search_id)</TD>
			</TR>
			<tr id=\"tr_$so_klucz\">
				<TD valign=\"top\" width=\"15\"></TD>
				<TD valign=\"middle\">".$node."</TD>
			</tr>
			";
		}
		$ret.= "</TABLE>";
		return $ret;
	}

?>
<script>
	function changeNode(id)
	{
		if (document.all['td_'+id+''].innerHTML == '-')
		{
			document.all['td_'+id+''].innerHTML = '+';
			document.all['tr_'+id+''].style.visibility = 'hidden';
			document.all['tr_'+id+''].style.display = 'none';
		}
		else if (document.all['td_'+id+''].innerHTML == '+')
		{
			document.all['td_'+id+''].innerHTML = '-';
			document.all['tr_'+id+''].style.visibility = 'visible';
			document.all['tr_'+id+''].style.display = 'inline';
		}
	}
	function changeRights(id,val)
	{
		var tr_obj=document.all[id];
		if (tr_obj == null) return;
		oColl=tr_obj.all;
		oInputs=oColl.tags("INPUT");
		var c_len=oInputs.length;
		ile = c_len+1;
		if (c_len)
			if (confirm('Na pewno ustawiæ/zabraæ wszystkie ('+ile+') prawa ?'))
				for (var j=0; j<c_len; j++)
				{
					oInput=oInputs(j);
					oInput.checked = val;
				}
	}
</script>