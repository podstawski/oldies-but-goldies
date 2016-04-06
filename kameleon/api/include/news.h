<?
if (strlen($api_size)==0)
	$limit=10;
else
	$limit=$api_size;

if ($api_mode!=1)
{

	$query =" SELECT img,headline,pri,akt,mies,rok,more AS _wiecej,nd_akt ";
	$query.=" FROM webaktual WHERE servername='$KEY' ORDER BY pri DESC LIMIT $limit";
	$_result=$adodb->Execute($query);
	for ($_i=0;$_i<$_result->RecordCount();$_i++)
	{
		parse_str(ado_ExplodeName($_result,$_i));
		$headline=stripslashes($headline);
		$akt=stripslashes($akt);
		$more=stripslashes($more);
		$nd_akt=FormatujDate($nd_akt);
		if (strlen($img)>0)
		$foto="<td width=1%><img src=$img border=0></td>";
		else $foto="";
		$okres=sprintf("%02d.$rok",$mies);
		echo "
		<table border=0 cellspacing=0 cellpadding=2 width=100%>
		<tr><td colspan=2 class=api_news_headline>$headline</td></tr>
		<tr><td colspan=2 class=api_news_date>$nd_akt</td></tr>
		<tr>$foto<td valign=top class=api_news_akt>$akt</td></tr> 
		"; 
		if ($pri == $api_aktid) 
		{
			$more_query="SELECT more, mies, rok
			FROM webaktual WHERE servername='$KEY' AND pri=$api_aktid LIMIT 1";
			$res=$adodb->Execute($more_query);
			parse_str(ado_ExplodeName($res,0));
			
			$more=nl2br(stripslashes($more));
			echo "<tr><td colspan=2 class=api_news_more>$more</td></tr>";
			echo "<tr><td colspan=2 width=100%>&nbsp;</td></tr>";
		}
		else
		{
			if (strlen($_wiecej))
			{
				//ustaw $href w zale¿noœci od tego czy jest serwis wyeftepowany czy w kameleonie
				if ($api_km) 
					$href="$api_next&api_aktid=$pri";
				else
					$href="$api_next?api_aktid=$pri";
				echo "<tr><td colspan=2>";
				echo "<table border='0' align='right'><tr>";
				echo "<td background='/images/news_dotline.gif' width=100%>&nbsp;</td>";
				echo "<td><a href=$href class=api_news_more>".label("more")."&nbsp;>></a></td>";
				echo "</tr></table><br clear='all'>";
				echo "</td></tr>";
			}
		}
		echo "</table>";
	}
}
else
{
	if ($api_km)
		$href="$api_next&api_action=apiDodajAktual";
	else
		$href="$api_next?api_action=apiDodajAktual";
	echo "<a href=\"$href\">".label("Add news")."</a>";
?>
<br>
<br>

<table width=100% cellspacing=0 cellpadding=2 border=1 bordercolor=#c0c0c0>
<?
$query="SELECT * FROM webaktual
	WHERE servername='$KEY'
	ORDER BY pri DESC"; 
$result=$adodb->Execute($query);
for ($i=0;$i<$result->RecordCount();$i++)
{
	parse_str(ado_ExplodeName($result,$i));
	$nd_akt=FormatujDate($nd_akt);
	$headline=stripslashes($headline);
	$akt=stripslashes($akt);
	$more=stripslashes($more);
	echo "<tr>";
	if (strlen($img)>0)
		$foto="<td width=1%><img src=$img border=0></td><td";
	else
		$foto="<td colspan=2";
	$m=sprintf("%02d",$mies);
	//echo td("left","$m/$rok",0); 
	$akt=nl2br($akt); 
	echo "
		<td>
			<table width=100%>
			<tr><td colspan=2 class=api_news_headline>$headline</td></tr>
			<tr><td colspan=2 class=api_news_date>$nd_akt</td></tr>
			<tr>$foto valign=top>$akt</td></tr>
			</table>
		</td>";
	if ($api_km)
		$href="$api_next&api_pri=$pri";
	else
		$href="$api_next?api_pri=$pri";
	
	$delete="<a href=javascript:api_zmiana('$pri','apiUsunAktual')>
		<img src=$API_URL/img/ikona-smietnik-b.gif alt='".label("Delete")."' width=12 height=12 border=0></a>";
	$edit="<a href=$href&api_action=apiEdytujAktual>
		<img src=$API_URL/img/ikona-zmien-b.gif alt='".label("Edit")."' width=12 height=12 border=0></a>";
	$up="<a href=$href&api_dir=down&api_action=apiMoveAktual>
		<img src=$API_URL/img/ikona-up-b.gif alt='".label("Move up")."' width=12 height=12 border=0></a>";
	$down="<a href=$href&api_dir=up&api_action=apiMoveAktual>
		<img src=$API_URL/img/ikona-down-b.gif alt='".label("Move down")."' width=12 height=12 border=0></a>";
	echo "<td nowrap>$up $down $edit $delete</td>";
	echo "</tr>";
}
?>
</table>
<form name=api_zmiany method=post action=<?echo $api_next?>>
 <?echo $GLOBAL_HIDDEN?>
 <input type=hidden name=page value="<?echo $page?>">
 <input type=hidden name=api_action value="">
 <input type=hidden name=api_pri value="">
</form>
<script>
function api_zmiana(pri,api_action)
{
	if ( api_action=="apiUsunAktual"  
		&& !confirm("Jesteœ pewien, ¿e chcesz usun¹æ")) return;

	document.api_zmiany.api_pri.value=pri;	
	document.api_zmiany.api_action.value=api_action;	
	document.api_zmiany.submit();
}
</script>
<?
}
?>
