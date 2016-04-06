<?php

	if (!is_array($RESULT)) return;

	$TABLE="<table border=1 cellspacing=0 align=\"center\" width=\"600\">\n";

	$gifs[-1]	=	"hym.gif";
	$gifs[1]	=	"ok.gif";
	$gifs[0]	=	"stop.gif";

	function everything2underline($str)
	{
		$wynik=@ereg_replace("[\.-/]","_",$str);
		//echo "$str ... $wynik<br>";
		return $wynik;
	}


	while ( list($title,$more) = each($RESULT) )
	{
		$status=1;
		$expl_table="";
		while ( list($subtitle,$res) = each($more) )
		{
			$_subtitle=everything2underline($subtitle);
			$expl_table.="			<tr><td valign=\"top\" width=1";

			if ( strlen($res[1]) ) $expl_table.=" style=\"cursor:hand\" onClick=\"hs(document.all['$_subtitle'])\"";
			$expl_table.="><img src=\"".$gifs[$res[0]]."\"></td>";

			$expl_table.="<td class=\"k_td\"><b>".label($subtitle)."</b>";


			$divstyle=($res[0]==0)?"color:red":"display:none";
			if ( strlen($res[1]) ) $expl_table.="<div id=\"$_subtitle\" style=\"$divstyle\">$res[1]</div>";
			$expl_table.="</td>";

			$expl_table.="</tr>\n";

			if ($res[0]==0) 
			{
				$status=0;
			}

			if ($res[0]==-1 && $status!=0 ) 
			{
				$status=-1;
			}

		}
		$expl_table="		<table width=\"100%\" >\n$expl_table\n		</table>\n";

		$_title=everything2underline($title);

		$TABLE.="	<tr><td valign=\"top\" class=\"k_formtitle\" style=\"text-align:left\">";

		$TABLE.="<img src=\"".$gifs[$status]."\" style=\"cursor:hand\" onClick=\"hs(document.all['$_title'])\" align=\"absMiddle\">";

		$TABLE.=" &nbsp;".label($title)."</td></tr>\n";

		$style=($status!=1)?"":"style=\"display:none\"";
		$TABLE.="	<tr id=\"$_title\" $style><td style=\"padding-left:20px\">$expl_table</td></tr>\n";
	}

	$TABLE.="</table>
	
	<script>
		function hs(obj)
		{
			if (obj.style.display=='none') 
				obj.style.display='block';
			else 
				obj.style.display='none';
		}
	</script>	
	";
?>
