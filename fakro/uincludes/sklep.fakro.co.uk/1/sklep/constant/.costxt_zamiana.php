<?
	global $COSTXT_ZAMIANA;

	function ent2iso($txt)
	{
		$win='¥¹æÆ£³ñÑêÊ¿¯';
		$iso='¡±¶¦æÆ£³ñÑêÊ¿¯¼¬';
		$ent='&#260;&#261;&#347;&#346;&#263;&#262;&#321;&#322;&#324;&#323;&#281;&#280;&#380;&#379;&#378;&#377;';


		$enta=explode(';',$ent);

			for($i=0;$i<count($enta);$i++)
		{
			if (!strlen($enta[$i])) continue;

			//echo '<b>'.$enta[$i].'; -> '.$iso[$i].'</b><br>';

			$txt=str_replace($enta[$i].';',$iso[$i],$txt);
		}

		return $txt;
	}

	
	if ( is_array($COSTXT_ZAMIANA))
	{

		$co=addslashes(stripslashes(urlencode(ent2iso($COSTXT_ZAMIANA[co]))));
		$naco=addslashes(stripslashes(urlencode(ent2iso($COSTXT_ZAMIANA[naco]))));
		$ev=stripslashes(ent2iso($COSTXT_ZAMIANA['eval']));

		if (strlen($co))
		{
			$sql="UPDATE webtd SET costxt=replace(costxt,'$co','$naco') WHERE server=$SERVER_ID AND html='td_js_selector.php'";
		}

		if (strlen($ev))
		{
			eval("$ev;");
			echo $ev;
		}


		//$kameleon_adodb->debug=1;
		if (strlen($sql)) $kameleon_adodb->execute($sql);
	}

	/*
	echo urlencode('¯').'<br>';
	echo htmlspecialchars($COSTXT_ZAMIANA[co]).'<br>';
	echo urlencode(ent2iso($COSTXT_ZAMIANA[co])).'<br>';
	echo ent2iso($COSTXT_ZAMIANA[co]);

	*/
?>

<form method="post" action="<?echo $self?>" style="margin:20px">
co: <input name="COSTXT_ZAMIANA[co]" value="<?//echo $COSTXT_ZAMIANA[co]?>" class="k_input" style="width:400;"> <br>

na: <input name="COSTXT_ZAMIANA[naco]" value="<?//echo $COSTXT_ZAMIANA[naco]?>" class="k_input" style="width:400;"> <br>

ev: <textarea name="COSTXT_ZAMIANA[eval]" style="width:400;height:100" class="k_input"><?//echo $COSTXT_ZAMIANA[eval]?></textarea> <br>
<input type="submit" value="change" class="k_button" style="margin-top:8px">
</form>