<style>
	.frmtbl input {
		border: 1px solid black;
	}
	
	.label {
		font-weight:bold;
	}

</style>

<?
	
	global $WEBTD;

	$xml = $WEBTD->xml;
	$tab = unserialize(stripslashes($xml));

	$SHOW = $tab["SHOW"];
	$LABEL = $tab["LABEL"];
	$REQ = $tab["REQ"];

	$kim_jestem = "";
	if ($SHOW[kimjestem])
	{
		reset($SHOW);
		$i=0;
		$kim_jestem.= "
		<tr>
			<td rowspan=3 valign=\"top\" align=\"right\" class=\"label\">
			".$LABEL[kimjestem]." </td>";

		while (list($key,$val) = each($SHOW))
		{
			if (substr($key,0,5) == 'check' && $val)
			{
				if ($i && !($i%2))
					$kim_jestem.= "</tr><tr>";
				$nazwa = substr($key,6);
				$kim_jestem.= "<td><input type=\"Checkbox\" style=\"border:0\" id=\"".$key."\" name=\"FORM[$nazwa]\" value=\"TAK\"> 
								".$LABEL[$key]."</td>";		
				$addjs.= "&& !obj.$key.checked ";
				$i++;
			}
		}
		$kim_jestem.= "</tr>";
		if ($REQ[kimjestem]==1)
		{
			$addjs = "
					if (".substr($addjs,2).")
					{
						alert('".addslashes(stripslashes(str_replace("%label%",$LABEL[kimjestem],$LABEL[req])))."');
						return false;
					}
					";
		}
		else
			$addjs = "";
	}
?>

<form action=<? echo $next ?> method="post" onSubmit="return validate<? echo $WEBTD->sid ?>(this)">
<input type="hidden" name="action" value="FormSend">
<table cellspacing="2" cellpadding="2" border="0" width="100%" class="frmtbl">
<col class="label"><col>
<? echo $kim_jestem ?>
<?	
	if ($SHOW[temat])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[temat]." ".($REQ[temat]?"*":""); ?></td>
	<td colspan=2><input type="text" name="FORM[temat]" id="temat" value="" size="50" maxlength="70"></td>
</tr>
<?	
	}

	if ($SHOW[imie])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[imie]." ".($REQ[imie]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[imie]" id="imie" size="50" maxlength="70" value=""></td>
</tr>
<?
	}

	if ($SHOW[nazwisko])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[nazwisko]." ".($REQ[nazwisko]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[nazwisko]" id="nazwisko" size="50" maxlength="70" value=""></td>
</tr>
<?
	}

	if ($SHOW[telefon])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[telefon]." ".($REQ[telefon]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[telefon]" id="telefon" size="50" maxlength="70" value="" ></td>
</tr>
<?
	}	

	if ($SHOW[ulica])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[ulica]." ".($REQ[ulica]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[ulica]" id="ulica" size="50" maxlength="70" value=""></td>
</tr>
<?
	}

	if ($SHOW[ulica])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[kod]." ".($REQ[kod]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[kod]" style="width:50px" id="kod" size="10" maxlength="10" value=""></td>
</tr>
<?
	}

	if ($SHOW[miasto])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[miasto]." ".($REQ[miasto]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[miasto]" id="miasto" size="50" maxlength="70" value=""></td>
</tr>
<?
	}

	if ($SHOW[email])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[email]." ".($REQ[email]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[e_mail]" id="email" size="50" maxlength="70" value=""></td>
</tr>
<?
	}

	if ($SHOW[panstwo])
	{
?>
<tr>
	<td align="right" nowrap><? echo $LABEL[panstwo]." ".($REQ[panstwo]?"*":""); ?></td>
	<td colspan=2><input type="Text" name="FORM[kraj]" id="panstwo" size="50" maxlength="70" value=""></td>
</tr>
<?
	}

	if ($SHOW[uwagi])
	{

?>
<tr>
	<td colspan="3" nowrap><? echo $LABEL[uwagi]." ".($REQ[uwagi]?"*":""); ?></td>
</tr>
<tr>
	<td align="right"></td>
	<td colspan=2><textarea name="FORM[uwagi]" cols="56" id="uwagi" rows="4" style="border: 1px solid black;"></textarea></td>
</tr>
<?
	}

	if ($SHOW[regulamin])
	{
?>

<tr>
	<td valign="top" align="right">	
		<? echo $lang=='i'?"Tak":"Yes"; ?><input type="checkbox" name="FORM[dane_osobowe]" value="TAK" style="border:0" checked>
	</td>
	<td colspan=2><? echo $LABEL[regulamin] ?></td>
</tr>
<?
	}	
?>
<tr>
	<td colspan="3" align="right"><input type="submit" name="poczta" value="<? echo $lang=="i"?"Wyślij wiadomość":"Send Message"; ?>" class="submit"></td>
</tr>
</table>
</form>
<?
	
if (is_array($REQ))
{
	reset($REQ);
	while (list($key,$val) = each($REQ))
		if ($val && $key != "kimjestem")
		{
			$js.="
				if (obj.$key.value == '')
				{
					alert('".addslashes(stripslashes(str_replace("%label%",$LABEL[$key],$LABEL[req])))."');
					obj.$key.focus();
					return false;
				}
			";

		}
}
?>
<script>
	function validate<? echo $WEBTD->sid ?>(obj)
	{
		<? echo $addjs.$js ?>
		return true;
	}
</script>