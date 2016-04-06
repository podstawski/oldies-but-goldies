<?
	global $WEBPAGE;
	global $nl_form;

	if (!strstr($WEBPAGE->file_name,"_$WEBPAGE->sid"))
	{
		$query="UPDATE webpage SET file_name='neswletter_$WEBPAGE->sid.php',hidden=1 
				WHERE sid=$WEBPAGE->sid";
		$adodb->execute($query);
	}

	if (!$WEBPAGE->hidden) 
	{
		$query="UPDATE webpage SET hidden=1 
				WHERE sid=$WEBPAGE->sid";
		$adodb->execute($query);
	}

	if (!$WEBPAGE->hidden) 
	{
		$WEBPAGE->pagekey=$CONST_MAILER_FROM;
		$query="UPDATE webpage SET pagekey='$CONST_MAILER_FROM' WHERE sid=$WEBPAGE->sid";
		$adodb->execute($query);
	}

	if (is_array($nl_form))
	{
		$set="";
		while( list($k,$v)=each($nl_form) )
		{
			$v=addslashes(stripslashes(ereg_replace("\"","&quote;",$v)));

			if (strlen($set)) $set.=",";
			$set.="$k='$v'";
			$WEBPAGE->$k=stripslashes($v);
		}

		$query="UPDATE webpage SET $set WHERE sid=$WEBPAGE->sid";
		$adodb->execute($query);
	}
	$adodb->debug=0;
?>


<form>
<input type=hidden name="page" value="<?echo $page?>">

<span style="color:black;width:60px">Temat:</span><input name="nl_form[title]" value="<?echo $WEBPAGE->title?>" size=80> <br>
<span style="color:black;width:60px">Od kogo:</span><input name="nl_form[pagekey]" value="<?echo $WEBPAGE->pagekey?>" size=80> <br>

<input type="submit" value="Zapisz">
</form>


<hr size=1>
