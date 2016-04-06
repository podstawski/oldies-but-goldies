<?
	//print_r($API_REQUEST);

	$ob=0;
	$plain='';
	$sql="SELECT ob,plain FROM webtd WHERE sid=".$API_REQUEST[sid];
	parse_str(ado_query2url($sql));

	if (($ob & 1) && ($ob & 2))
	{
		$plain=stripslashes($plain);
		$body="	<TR>
		<TD valign=\"top\">".label('Content').":</TD>
		<TD valign=\"top\">".$plain."</TD>
		</TR>
		";
	}
	include_once("captcha/kcaptcha.php");

	if (!$_REQUEST[api_em])
	{
		include('include/send_form.h');
		return;
	} else {
	
		$api_mode = set_cos_api_mode();

		$ustawienia="
			<fieldset style=\"width:99%; margin-left:2px;\">
			<legend>".label('Friend email')."</legend>
			<form method=post name=api_ogloszenia action=$api_next>
			$GLOBAL_HIDDEN
			<input type=hidden name=api_action value=''>
			<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
			<tr><td>
					<input type='hidden' value='0' name='api_form_opt[0]'>
					<input type='checkbox' value='$sid' name='api_form_opt[16]' ".(($api_mode & 16)?'checked':'')."> ".label("enable captcha")."
					<input type='checkbox' value='$sid' name='api_form_opt[32]' ".(($api_mode & 32)?'checked':'')."> ".label("image captcha")."
				</td>
				<td align=right>
				<input type='submit' class='k_button' value='".label("Save")."' onClick=\"document.api_ogloszenia.api_action.value='apiZapiszOgloszeniaUstawienia'\"></td></tr>
			</table>
			</form>	
			</fieldset>
			<br/>&nbsp;";
		
		echo $ustawienia;
	}
	

	//$MAILF=$API_REQUEST[MAILF];

	$xml = array("MAILF"=>$MAILF);

	if ($API_REQUEST[sid])
	{
		if ($API_REQUEST[ssid] == $API_REQUEST[sid])
		{
			$sql = "UPDATE webtd SET xml = '".addslashes(serialize($xml))."' WHERE sid = ".$API_REQUEST[sid];

			$adodb->execute($sql);
			$tab = $xml;
		}
		else
		{
			$query="SELECT xml FROM webtd WHERE sid=".$API_REQUEST[sid];
			parse_str(ado_query2url($query));
			$tab = unserialize(stripslashes($xml));
		}
	}
	
	$MAILF = $tab["MAILF"];

	echo "
	<fieldset style=\"width:99%; margin-left:2px;\">
	<legend>".label('Kameleon send form via email')."</legend>
	<form method=post action=\"index.php?page=$page\">
	<INPUT TYPE=\"hidden\" NAME=\"ssid\" value=\"".$API_REQUEST[sid]."\">
	<TABLE>
	<TR>
		<TD>".label('From').":</TD>
		<TD><INPUT TYPE=\"text\" size=50 NAME=\"MAILF[mailfrom]\" value=\"".$MAILF[mailfrom]."\"></TD>
	</TR>
	<TR>
		<TD>".label('To').":</TD>
		<TD><INPUT TYPE=\"text\" size=50 NAME=\"MAILF[mailto]\" value=\"".$MAILF[mailto]."\"></TD>
	</TR>
	<TR>
		<TD>".label('Subject').":</TD>
		<TD><INPUT TYPE=\"text\" size=50 NAME=\"MAILF[subject]\" value=\"".$MAILF[subject]."\"></TD>
	</TR>
	$body
	<TR>
		<TD colspan=2>
		
		<INPUT TYPE=\"submit\" value=\"".label('Save')."\"  class=\"k_button\" ></TD>
	</TR>
	</TABLE>
	</form>
	</fieldset><br/>&nbsp;
	";
?>
