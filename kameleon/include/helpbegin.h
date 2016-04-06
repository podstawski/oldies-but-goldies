<?
/*
if (!$KAMELEON_MODE) return;

$HELP_LINK=$REQUEST_URI;

if (strstr($HELP_LINK,"sethelpmode=")) $HELP_LINK=ereg_replace("[&]*sethelpmode=[0-9]","",$HELP_LINK);

$HELP_LINK.=strstr($HELP_LINK,"?")?"&":"?";
if (!strstr($HELP_LINK,"page=")) $HELP_LINK.="page=$page&";
if (strstr($HELP_LINK,"action=")) $HELP_LINK.="action=&";
$HELP_LINK.="sethelpmode=";
$HELP_LINK.=$helpmode?0:1;


if ($editmode && !$helpmode)
{
	echo "
		<script language=\"javascript\">
			function onF1OpenHelp()
			{
				location.href='$HELP_LINK';
				return false;
			}
			window.onhelp=onF1OpenHelp;
		</script>";

}


if ($helpmode && $editmode)
{

	$w=$CONST_HELP_WIDTH-25;
	echo "<table border=0 cellspacing=0 cellpadding=0 width=\"100%\">
			<tr><td valign=\"top\" style=\"border-right: 1px inset\"
				bgcolor=\"Silver\" width=\"$CONST_HELP_WIDTH\"
				onMouseOut=\"this.MouseIsOver=0; CloseKameleonHelpTimeout(30,this)\"
				onMouseOver=\"this.MouseIsOver=1\"
				>";

	echo "<table border=0 cellspacing=0 cellpadding=2 width=\"100%\">
			<tr>
				<td class=\"k_text\" width=\"$w\" style=\"border: 1px solid silver\">";
	echo label("Help");
	echo "
				</td>
				<td align=\"center\" style=\"border: 1px\" 
					onMouseOver=\"this.style.borderStyle='outset'\"
					onMouseOut=\"this.style.borderStyle='none'\">
					<a href=\"$HELP_LINK\" class=\"k_a\" style=\"text-decoration:none\">x</a>
				</td>
			</tr>
			<tr><td colspan=2 style=\"border-top: 1px outset\">";

	if (file_exists("incuser/help.h")) include("incuser/help.h");
	if (file_exists("include/help.h")) include("include/help.h");

	echo "</td></tr>";

	if (strlen($KAMELEON_HELP))	
	{
		echo "
				<tr><td colspan=2 style=\"border-top: 1px outset\" align=\"right\">
				<a href=\"$KAMELEON_HELP\" target=\"_blank\" class=\"k_a\" 
					style=\"text-decoration:none\">";
	
		echo label("full documentation");
		echo " &raquo;</a>
				</td></tr>";
	}

	echo "</table><img src=\"img/spacer.gif\" height=1 width=\"$CONST_HELP_WIDTH\"";
						


	echo "</td><td valign=\"top\">";
}
*/
?>
