<?
	global $infx;
	include_once("$INCLUDE_PATH/newsletter2/infx_show.h");

	if ($infx[0]==$WEBTD->sid)
	{
		//print_r($infx);
/*
		$xml="";
		for ($i=1;$i<=3 && strlen($infx[$i][0]);$i++)
		{
			$query="SELECT * FROM lm WHERE lm_id=$infx[$i]";
			parse_str(ado_query2url($query));

			if (strlen($xml)) $xml.="|";
			$xml.=implode(":",$infx[$i]);
			
		}
*/
		$query="UPDATE webtd SET cos=$infx[10],costxt='$infx[11]|$infx[12]|$xml'
			WHERE sid=$WEBTD->sid";
		$adodb->execute($query);

		$WEBTD->cos=$infx[10]+0;
		$WEBTD->costxt="$infx[11]|$infx[12]|$xml";
			
		
	}
	$ar=explode("|",$WEBTD->costxt);

	include("$INCLUDE_PATH/newsletter2/infx.h");
	

	if (!$WEBTD->staticinclude)
	{
		$query="UPDATE webtd SET staticinclude=1 WHERE sid=$WEBTD->sid";
		$adodb->execute($query);
	}

	

?>
<hr size=1>
<form action="<?echo $self?>" method=post>
<input name="infx[0]" type=hidden value="<?echo $WEBTD->sid?>">

<?
	$sizes=array(4,4,2,4,4,1,4,2,5,2,1);
	for ($i=1;$i<4;$i++)
	{
		$li=explode(":",$ar[$i+1]);

		for ($j=0;$j<11;$j++)
		{
			echo "<input size=$sizes[$j] name='infx[$i][$j]' value='$li[$j]'>";
		}
		echo "<br>";
	}


?>

&nbsp;&nbsp;&nbsp; 
<select name="infx[10]">
<?
	global $INFX_TYPY;
	for ($i=0;$i<count($INFX_TYPY);$i++)
	{
		$sel=($WEBTD->cos==$i)?"selected":"";
		echo "<option value=$i $sel>$INFX_TYPY[$i]</option>";	
	}

?>
</select>
Kolor: <input name="infx[11]" value="<?echo $ar[0]?>" size=5 id="bgcolor_<?echo $WEBTD->sid?>"> 
<a class=k_a href="javascript:otworzPalete('bgcolor_<?echo $WEBTD->sid?>',document.all['bgcolor_<?echo $WEBTD->sid?>'].value)">
<img class=k_imgbutton border=0 src=img/i_colors_n.gif align=middle onmouseover="this.src='img/i_colors_a.gif'" onmouseout="this.src='img/i_colors_n.gif'" width=23 height=22 align=absmiddle></a>
<br>
&nbsp;&nbsp;&nbsp;
Tekst linka "dalej": <input name="infx[12]" value="<?echo $ar[1]?>" size=50>
<br>
&nbsp;&nbsp;&nbsp;
<input type=submit value="Przypisz">

</form>

<script>
	var pole_koloru="";
	function ustawKolor(par,kolor)
    	{
	    document.all[pole_koloru].value=kolor;
    	}

	function otworzPalete(nazwa,kolor)
	{
		pole_koloru=nazwa;
		
		if (kolor.substring(0,1)=="#") kolor=kolor.substring(1,7);
		a=open('kolory.php?u_color='+kolor,'Kolory',
			"toolbar=0,location=0,directories=0,\
			status=1,menubar=0,scrollbars=0,resizable=0,\
			width=400,height=400");

	}

</script>
