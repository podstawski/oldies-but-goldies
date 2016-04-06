<?
if ($INCLUDE_NAVIFUN) return;
$INCLUDE_NAVIFUN=1;

function naviIndex($href,$start,$offset,$ile,$size)
{
	global $navi;
	if ($start<0 || $start>$ile) $start=0;
	$start+=0;
	if (!strlen($navi)) $navi=1;
	$offset=0+$start;
	if ($start+$size<$ile)
		$next=$start+$size;

	$_dest=urlencode($dest);

	$naviend=5;
	$pom=$naviend * $size;
	if ($pom>$ile)
		$naviend=0+floor($ile / $size);
	else
		$naviend=5;

	if ($start==($navi+$naviend)*$size)
		$navi=$start/$size;
	else
	if ($start<$navi*$size)
		if ($navi-$naviend<=0)
			$navi=1;
		else
			$navi=$navi-$naviend;

	
	$all_link="$href&navi=$navi";

	$next_link=$all_link."&ile=$ile&start=$next";
	$back=$offset-$size;
	$prev_link=$all_link."&ile=$ile&start=$back";

	$linkp="&nbsp;";
	$linkn="&nbsp;";
	if ($start==0)
		$linkn="<a href=$next_link><b>nastêpne</b> &raquo;&raquo;</a> ";
	else
	{
		$linkp="<a href=$prev_link>&laquo;&laquo; <b>poprzednie</b></a>";
		if ($start+$size<$ile)
			$linkn="<a href=$next_link><b>nastêpne</b> &raquo;&raquo;</a>";
	}

	$pasek="";
	//echo "<br>navi=$navi, naviend=$naviend, ile=$ile, size=$size<br>";return;
	for ($i=$navi;$i<=$navi+$naviend;$i++)
	{
   		$n=$i*$size;
		$navistart=$n-$size;
		if ($n==$size)
		{
			if ($n==$start+$size)
				$pasek.="<font color=red><b>$i</b></font> ";
			else
				$pasek.="[<a style='nawigacja_link' href=$all_link&ile=$ile&start=$navistart>$i</a>] ";
		}
		else
		{
			if ($n==$start+$size)
				$pasek.=" <font color=red><b>$i</b></font> ";
			else
				$pasek.=" [<a style='nawigacja_link' href=$all_link&ile=$ile&start=$navistart>$i</a>] ";
		}
	}
	$stron= ceil ($ile / $size);
	$pasek.=" ... z <b>$stron</b>";
	if ($ile>$size)
	{
		$nawigacja="
		 <table border=0 cellpadding=0 cellspacing=3>
		 <tr>
		   <td align=left nowrap>$linkp &nbsp;&nbsp;</td>
		   <td align=left>Strony $pasek</td>
		   <td align=right>&nbsp;&nbsp;$linkn</td>
		 </tr>
 		 </table>";
	}
	return $nawigacja;
}


?>
