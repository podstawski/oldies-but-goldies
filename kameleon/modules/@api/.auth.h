<script language="javascript">
function showModFun()
{
}
</script>
<?

	if (!strlen($xml)) $xml=$costxt;

	
	$a=xml2obj($xml);
	$xml=$a->xml;

?>
<table cellpadding=4>

<tr class=k_form>
	<td align="right"><?echo label("Authorization method")?></td>
	<td><input type="radio" value="email" 
			<? if ($xml->method=="email") echo "checked" ?>
			name="AUTH[method]" class="k_input"> email, 
		<input type="radio" value="username" 
			<? if ($xml->method=="username") echo "checked" ?>
			name="AUTH[method]" class="k_input"> username 
		</td>

</tr>

<tr class=k_form>
	<td align="right"><?echo label("Authorization required")?></td>
	<td><input type="checkbox" value="1" 
			<? if (!$cos) echo "checked" ?>
			name="cos_auth_required" class="k_input">  
		</td>

</tr>

<?
	$sql = "SELECT DISTINCT(c_email2) FROM crm_customer WHERE
			c_server = $SERVER_ID ORDER BY c_email2";

	$res = $adodb->execute($sql);
	$select_group = "<SELECT name=\"AUTH[email2]\" id=\"c_email2_select\" class=\"k_input\">";
	$gc = $res->RecordCount();
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		if ($xml->email2 == $c_email2) $sel = "selected";
		else $sel = "";
		$select_group.= "<option value=\"$c_email2\" $sel>$c_email2</option>"; 
	}
	$select_group.= "</SELECT>";

$lista = "<img src=\"img/i_tree_n.gif\" id=\"select_img\" onclick=\"reloadSelect()\" style=\"cursor:hand\">";
$da_input = "<img src=\"img/i_new_n.gif\" id=\"input_img\" onclick=\"reloadSelect()\" style=\"cursor:hand\">";
?>

<tr class=k_form>
	<td><? echo label("Group");?> </td>
	<td><? echo $lista." ".$da_input; echo $select_group?><input type="text" size=25 value="<? echo $xml->email2?>" 
		name="AUTH[email2]" id="c_email2_input" class="k_input"></td>
</tr>



</table>
<script>
			function reloadSelect()
			{
				if(document.all['c_email2_select'].style.display == 'none')
				{
					document.all['c_email2_select'].disabled = false;
					for (i=0; i < document.all['c_email2_select'].options.length;i++)
						if (document.all['c_email2_select'].options[i].value == document.all['c_email2_input'].value)
							document.all['c_email2_select'].options[i].selected = true;

					document.all['c_email2_select'].style.display = 'inline';
					document.all['c_email2_input'].style.display = 'none';
					document.all['c_email2_input'].disabled = true;
					document.all['select_img'].style.display = 'none';
					document.all['input_img'].style.display = 'inline';
				} else
				{
					document.all['c_email2_input'].value = document.all['c_email2_select'].value;
					document.all['c_email2_select'].style.display = 'none';
					document.all['c_email2_select'].disabled = true;
					document.all['c_email2_input'].disabled = false;
					document.all['c_email2_input'].style.display = 'inline';
					document.all['select_img'].style.display = 'inline';
					document.all['input_img'].style.display = 'none';
				}
			}
			<?
				if ($gc)
				{
					echo "
					document.all['c_email2_select'].style.display = 'inline';\n
					document.all['c_email2_input'].style.display = 'none';\n
					document.all['c_email2_input'].disabled = true;\n
					document.all['c_email2_select'].disabled = false;\n
					document.all['select_img'].style.display = 'none';\n
					document.all['input_img'].style.display = 'inline';\n
					";
				} 
				else
				{
					echo "reloadSelect();\n";
				}

			?>

</script>