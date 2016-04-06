<?

if (!$KAMELEON_MODE)
{
	echo "This module can't be used outside WebKameleon !";
	return;
}

global $explore,$mailing,$GRUPA, $mail_action, $_M_TRYB;

if (!strlen($costxt)) $costxt = '1110';

$mail_page_number = $costxt;

$CONST_MAILER_LOOKUP_COND="ver<=$ver AND lang='$lang' AND page_id=$costxt ORDER BY ver DESC";

$sql = "SELECT DISTINCT c_email2 FROM crm_customer
		WHERE c_server = $SERVER_ID AND c_email2 IS NOT NULL
		AND c_email2 <> '' ORDER BY c_email2";

$res = $adodb->Execute($sql);

$groups_list = "";

for ($i=0; $i < $res->RecordCount(); $i++)
{
	parse_str(ado_explodename($res,$i));
	$groups_list.="<INPUT TYPE=\"checkbox\" class=\"api2_nletter_checkbox\" NAME=\"GRUPA[$c_email2]\" checked value=\"1\"> grupa \"$c_email2\" <br>";

}

if ($mailing[pri])
{
	
	$query ="SELECT c_email FROM crm_customer WHERE c_server = $SERVER_ID
			AND c_email2 = 'na_pewno_nie_ma_takiej_grupy'";
	
	if (is_array($GRUPA))
		while (list ($key, $val) = each ($GRUPA)) 
			$query.= " OR c_email2 = '$key' ";

	$query.= " ORDER BY c_email";

	$res=$adodb->Execute($query);

	unset ($mailbcc);
	$mailbcc_list = "";
	for ($i=0;$i < $res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$email = trim($email);
		if (!strlen($c_email)) continue;
		if (!$mailing[test]) 
		{
			if (!strpos($c_email,";"))
			{
				$mailbcc[]=$c_email;
				$mailbcc_list.= $c_email.",";
			} 
			else
			{
				$maillist = explode(";",$c_email);
				for ($indeks=0; $indeks < count($maillist); $indeks++)
				{
					$maillist[$indeks] = trim($maillist[$indeks]);
					if (strlen($maillist[$indeks])) 
					{
						$mailbcc[]=$maillist[$indeks];
						$mailbcc_list.= $maillist[$indeks].",";
					}
				}
			}
		}
	}
	$mailbcc_list = substr($mailbcc_list,0,-1);

/*
	$subject=$mailing[title];
	$mailto=$mailing[to];
	$mailfrom=$mailing[costxt];

	$old_CONST_MAILER_LOOKUP_COND=$CONST_MAILER_LOOKUP_COND;
	$CONST_MAILER_LOOKUP_COND="pri=$mailing[pri] AND $CONST_MAILER_LOOKUP_COND";
	$msg_pre="<style>
		a { color: #000000; font-family: Verdana;  text-decoration: none;}
		a:active { color: #EF0000; }
		a:hover { text-decoration: underline; }
		body { color: #000000; font-family: Verdana; font-size: 10px; }
		p { color: #000000; font-family: Verdana; font-size: 10px; }
		td { color: #000000; font-family: Verdana; font-size: 10px; }
		</style>";

	include ("$INCLUDE_PATH/action/SendMail.h");
*/
	$mailer_groups = $mailbcc;
	$grupy_mailera = $mailbcc_list;
	$action="SendmailOnAction";
	$sendmail_action = $mail_action;
	include("$INCLUDE_PATH/.api/action/SendmailOnAction.h");


	$sysinfo = "Mail >> $sendmail_action << zosta³ rozes³any do ".count($mailbcc). " osób.";

	echo "
	<script>
		alert('$sysinfo');
	</script>
		";
	$CONST_MAILER_LOOKUP_COND=$old_CONST_MAILER_LOOKUP_COND;
}

/* OLD
$query="SELECT pri,title,costxt,d_update,plain,bgcolor FROM webtd
	WHERE server=$SERVER_ID AND ver=$ver AND $CONST_MAILER_LOOKUP_COND
	,pri";
*/

$query="SELECT sid,pri,title,costxt,nd_update,plain,bgcolor, mod_action FROM webtd
		WHERE server=$SERVER_ID AND ver=$ver AND html='@api/sendmail.h'
		ORDER BY pri";


$res=$adodb->Execute($query);
$res_numrows = $res->RecordCount();

if ($res_numrows)
{
	echo "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" class=\"api2_nletter_table\" width=\"100%\">
			<col><col><col><col width=\"1%\" align=\"center\">
			<tr class=\"api2_nletter_tr\">
				<td class=\"api2_nletter_head_td\">".label("Title")."</td>
				<td class=\"api2_nletter_head_td\">".label("Data")."</td>
				<td class=\"api2_nletter_head_td\">".label("Sender")."</td>
				<td class=\"api2_nletter_head_td\">".label("actions")."</td></tr>";
}

for ($i=0;$i<$res_numrows;$i++)
{
	parse_str(ado_ExplodeName($res,$i));

//	if (!strlen($costxt)) $costxt=$CONST_MAILER_FROM;
	
	if (strlen($costxt))
		$mail_info = xml2obj($costxt);

	$mail_action = $mail_info->xml->subject;
	$mail_from = $mail_info->xml->from;
	$mail_to = $mail_info->xml->to;

	$d=FormatujDate($nd_update);

	$n=$mail_page_number;

	$link1 = "javascript:window.location.href='tdedit.php?page=$page&page_id=$n&pri=$pri'";
	$link2 = "javascript:window.location.href='$self&explore=$sid#formularz'";
	
	$to_link = urlencode($mail_action);
	$link2 = "javascript:window.location.href='$self&explore=$sid&mail_action=$to_link#formularz'";

	if ($editmode) $button1 = "<A HREF=\"$link1\"><img src=\"img/i_editmode_n.gif\" border=\"0\" alt=\"edytuj\"></A>";
	$button2 = "<A HREF=\"$link2\"><img src=\"img/i_mailsend_n.gif\" border=\"0\" alt=\"".label("send")."\"></A>";
	echo "<tr class=\"api2_nletter_tr\">
			<td class=\"api2_nletter_list_td\" nowrap><b>$mail_action</b></td>
			<td class=\"api2_nletter_list_td\" nowrap>$d</td>
			<td class=\"api2_nletter_list_td\"><A HREF=\"mailto:$mail_from\">$mail_from</A></td>
			<td class=\"api2_nletter_list_td\" align=\"center\">
			$button11&nbsp;$button2</td></tr>";

	if ($explore==$sid)
	{
		$query="SELECT count(*) AS c FROM crm_customer WHERE c_server = $SERVER_ID";
		parse_str(ado_query2url($query));
		$form = "<tr class=\"api2_nletter_tr\">
				<td class=\"api2_nletter_mailtitle_td\" colspan=\"4\">".label("Message").": $mail_action</td></tr>";
		$form.="<tr class=\"api2_nletter_tr\"><td colspan=\"4\" class=\"api2_nletter_mail_td\">
				<a name=formularz href=''></a>
				<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
					<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_mailfrom_td_label\">".label("From").":</td>
						<td class=\"api2_nletter_mailfrom_td\"><A HREF=\"mailto:$mail_from\">$mail_from</A></td></tr>
					<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_mailto_td_label\">".label("To").":</td>
						<td class=\"api2_nletter_mailto_td\">
							<input name=\"mailing[to]\" value=\"$mail_to\" class=\"api2_nletter_mailto_input\" style=\"width: 300px\"></td></tr>
					<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_mailbcc_td_label\">".label("CC").":</td>
						<td class=\"api2_nletter_mailbcc_td\">Newsletter ($c)</td></tr>
					<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_mailsubject_td_label\">".label("Subject").":</td>
						<td class=\"api2_nletter_mailsubject_td\">$mail_action</td></tr>
				</table>
				</td></tr>";
		
		$form.= "<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_mailcontent_td\" colspan=\"5\">".label("Content").":</td></tr>";		
		$form.="<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_mailplain_td\" colspan=\"4\">";
		$form.=stripslashes($plain);

		$label1 = label("Send only to broadcaster");
		$label2 = label("Send");

		$button1 = "<input class=\"api2_nletter_button\" type=\"button\" value=\"$label1\" onClick=\"javascript:mailingform.mailingtest.value='1';mailingform.submit();\">";
		$button1img = "<a href=\"javascript:mailingform.mailingtest.value='1';mailingform.submit();\"><img src=\"img/i_mailsendone_n.gif\" border=\"0\" alt=\"$label1\"></a>";	
		$button2 = "<input class=\"api2_nletter_button\" type=\"button\" value=\"$label2\" onClick=\"potwierdz_mailing()\">";

		$form.="
					<tr class=\"api2_nletter_tr\">
						<td class=\"api2_nletter_grouptitle_td\" colspan=\"4\" align=\"left\" nowrap>
						".label("Groups")." :</td></tr>
					<tr class=\"api2_nletter_tr\"><td class=\"api2_nletter_grouplist_td\" colspan=\"4\" align=\"left\" nowrap>
					$groups_list
					</td></tr>";
					
		$form.="
					<tr class=\"api2_nletter_tr\">
						<td class=\"api2_nletter_bottom_td\" colspan=\"4\" align=\"center\" nowrap>
						<input type=\"hidden\" name=\"mailing[title]\" value=\"$title\">
						<input type=\"hidden\" name=\"mail_action\" value=\"$mod_action\">
						<input type=\"hidden\" name=\"mailing[pri]\" value=\"$pri\">
						<input type=\"hidden\" name=\"mailing[costxt]\" value=\"$costxt\">
						<input type=\"hidden\" name=\"mailing[test]\" value=\"0\" id=\"mailingtest\">
						$button1&nbsp;&nbsp;$button2</td></tr>";
	}
}

if ($res_numrows)
	echo "</table>";

$are_you_sure = label("Are You sure You want to send this message to all this persons");
//Czy jestes pewny, ze chcesz wys³aæ do tylu osób ?
?>
<form method=post action="<?echo $self?>" name=mailingform>
<?echo $form?>
</form>

<script>
	function potwierdz_mailing()
	{
		if (confirm("<? echo $are_you_sure ?> ?"))
		{
			document.mailingform.submit();
		}
	}
</script>
