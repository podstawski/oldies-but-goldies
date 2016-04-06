<form method="post" action="<?echo $self?>">
<table><tr><td width="50%" valign="top" nowrap>
<b>Rola osoby:</b><br>
<?

	global $_sj,$_sg,$_su,$_suf,$_ctx_more,$doupdate, $upsid;
	// sj: schemat_jednostek, sg: system_grupa, su: system_user, suf: system_user-fields
	parse_str($WEBTD->costxt);


	$_n=0;
	if ($WEBTD->next && !$np) $_n=1;
	if (!$WEBTD->next && $np) $_n=1;

	if ($doupdate && $upsid == $WEBTD->sid) 
	{
		$__su=$_su[d]+$_su[u]+$_su[m]+$_su[s]+$_su[w]+$_su[ww]+$_su[o]+$_su[v];

		$__suf=0;
		$__suf2=0;
		while( is_array($_suf) && list($k,$v)=each($_suf)) 
		{
			if ($k<30) $__suf+=$v;
			else $__suf2+=$v;
		}

		$_sj+=0;

		$_n=($WEBTD->next)?1:0;
		$ctx_more=$_ctx_more;
		$__ctx_more=urlencode($_ctx_more);
		$query="UPDATE webtd SET costxt='sg=$_sg&su=$__su&suf=$__suf&suf2=$__suf2&sj=$_sj&ctx_more=$__ctx_more&np=$_n' 
				WHERE sid=$WEBTD->sid";
		$kameleon_adodb->Execute($query);


		$sg=$_sg;
		$su=$__su;
		$suf=$__suf;
		$suf2=$__suf2;
		$sj=$_sj;
	}
	elseif ($_n)
	{
		$_n=($WEBTD->next)?1:0;
		if (!strpos($WEBTD->costxt,"np=")) $WEBTD->costxt.="&np=$_n";
		$costxt=ereg_replace("np=[01]","np=".$_n,$WEBTD->costxt);
		$query="UPDATE webtd SET costxt='$costxt' WHERE sid=$WEBTD->sid";
		$kameleon_adodb->Execute($query);
	}

?>
<hr size=1 align="left">
<input type="checkbox" name="_su[d]" <? if ($su&1) echo "checked"?> value=1> Dodawanie<br>
<input type="checkbox" name="_su[u]" <? if ($su&2) echo "checked"?> value=2> Usuwanie<br>
<input type="checkbox" name="_su[m]" <? if ($su&4) echo "checked"?> value=4> Modyfikacja<br>
<hr size=1 align="left">
<? if ($self_only) { ?>
<input type="checkbox" name="_su[s]" <? if ($su&8) echo "checked"?> value=8> W³asne dane<br>
<input type="checkbox" name="_su[v]" <? if ($su&128) echo "checked"?> value=128> Walidacja JS<br>
<? } else { ?>
<input type="checkbox" name="_su[s]" <? if ($su&8) echo "checked"?> value=8> Kierowana instytucja<br>
<? }  ?>
<hr size=1 align="left">

<input type="checkbox" name="_su[w]" <? if ($su&32) echo "checked"?> value=32> Wyszukiwarka<br>
<input type="checkbox" name="_su[ww]" <? if ($su&64) echo "checked"?> value=64> Wyszukiwarka wymagana<br>

<? if ($WEBTD->more) {?>
<hr size=1 align="left">
Wiêcej:<br>
<input type="text" class="forminput" name="_ctx_more" value="<?echo $ctx_more?>"><br>
<? }?>

<hr size=1 align="left">
<INPUT TYPE="hidden" name="doupdate" value=1>
<INPUT TYPE="hidden" name="upsid" value="<? echo $WEBTD->sid ?>">
<input type="submit" value="Zapisz" class="formbutton">

</td>
<td nowrap>
<?
	include ("$SKLEP_INCLUDE_PATH/autoryzacja/firmy_fields.h");
	include ("$SKLEP_INCLUDE_PATH/js.h");
	for ($i=0;$i<count($osoby_fields);$i++)
	{
		$of=$osoby_fields[$i];
		$p=($i<30)?pow(2,$i):pow(2,$i-30);
		$v=($i<30)?$suf:$suf2;
		$ch=($v&$p)?"checked":"";

		echo "<input type=\"checkbox\" name=\"_suf[$i]\" $ch value=\"$p\"> $of[0] <br>\n";
	}

?>

</td>
</tr>

</table>

</form>
