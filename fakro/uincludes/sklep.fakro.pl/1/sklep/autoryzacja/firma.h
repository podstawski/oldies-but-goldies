<?
	global $oddzial_id;
	global $suid;

	$table="";

	if (!strlen($suid) && strlen($FORM["parent_id"])) $suid = $FORM["parent_id"];
	if (strlen($suid) && strlen(!$oddzial_id)) $oddzial_id = $suid;
	
	if (!$KAMELEON_MODE) $oddzial_id = $FORM["parent_id"];

	$sj = $oddzial_id;
	if (strlen($oddzial_id)) $LIST[id] = $oddzial_id;

	$sg=0;
	parse_str($costxt);

	if (!($su&1)) $LIST[id]=$CIACHO[admin_su_id];

	if ($su&8) // swoje dane
	{
		$query="SELECT sag_grupa_id AS sg FROM system_acl_grupa WHERE sag_server=$SERVER_ID
			AND sag_user_id=$AUTH[id] LIMIT 1";
		parse_str(ado_query2url($query));
		$LIST[id]=$AUTH[id];
	}


	$sj_tr="";
	//$sj_tr.="<tr><th colspan=2>Przypisana do sklepów</th></tr>";

	$_grupy = array();

	if ($sj)
	{
		$query="SELECT * FROM system_user WHERE su_id=$sj";
		parse_str(ado_query2url($query));		
		$sj_tr.="<input type=\"hidden\" class=\"sys_input\" name=\"form[user_id]\" value=\"$sj\">";
		for ($i=0;$i<$result->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($result,$i));

			if (!$LIST[id] && $result->RecordCount()==1)
			{
				$su_p_nazwa=$bo_nazwa;
				$su_p_ulica=$bo_ulica;
				$su_p_kod_pocztowy=$bo_kod_pocztowy;
				$su_p_miasto=$bo_miasto;
				$su_p_telefon=$bo_tel;
			}
			

			$ch="";
		}
		$sj_tr.="</td></tr>";
					
	}

	$_action="FirmaDodaj";
	$id="";
	if ($LIST[id])
	{
		$_action="FirmaModyfikuj";
		$query="SELECT * FROM system_user WHERE su_id=".$LIST[id];
		parse_str(quoteUrlEnc(ado_query2url($query)));

		$sql = "SELECT sag_grupa_id FROM system_acl_grupa WHERE sag_user_id = ".$LIST[id];
		$res = $projdb->execute($sql);

		for ($g=0; $g < $res->recordCount(); $g++)
		{
			parse_str(ado_explodename($res,$g));
			$_grupy[] = $sag_grupa_id;
		}

		$oddzial_id	= $LIST[id];
	}

	if (!$LIST[id] && !($su&1)) return;

	include ("$SKLEP_INCLUDE_PATH/autoryzacja/firmy_fields.h");

	$id="<input type=\"hidden\" name=\"form[su_id]\" value=\"$LIST[id]:$sg:$sg_coid:$suf:$suf2\">";

	$JS="";

	$form="";
	for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
	{
		$p=($ofi<30)?pow(2,$ofi):pow(2,$ofi-30);
		$v=($ofi<30)?$suf:$suf2;
		if (!($p&$v)) continue;
		$of=$osoby_fields[$ofi];

		$f=$form[$of[2]];
		$f[]=$of;
		$form[$of[2]]=$f;
	}

	reset($osoby_fields_grupy);
	$table="";
	while (list($k,$v)=each($osoby_fields_grupy))
	{
		if (!is_array($form[$k])) continue;

//		$onChNM="onChange=\"noMore()\"";
		$onChNM="";

		//$table.="<tr><th colspan=2>$v</th></tr>\n";
		$f=$form[$k];
		for ($i=0;$i<count($f);$i++)
		{
			$pola=$f[$i];

			$table.="<tr>\n";

			$table.="\t<td>$pola[0]</td>\n";
			$width=8*$pola[3];
			if ($width>400) $width=400;



			eval("\$val=\$$pola[1];");


			$v=$osoby_fields_validate[$pola[1]];
			$validation="";
			if (is_array($v))
			{
				$validation="title=\"$pola[0]\"";
				$validation.=" validate=\"$v[0]\"";
				if ($v[1]) $validation.=" minlength=".$v[1];
				if ($v[2]) $validation.=" maxlength=".$v[2];

			}

			$type="text";
			if ($pola[1]=="su_pass") 
			{
				$type="password";
				$val="";
			}
			if (is_array($pola[4]))
			{
				$content="";
				$content.="<select name=\"form[$pola[1]]\" class=\"sys_input\" >";
				if (count($pola[4]))
				{
					foreach($pola[4] AS $p)
					{
						$sel=($val==$p)?"selected":"";
						$opis=sysmsg(substr($pola[1],3)."_$p","crm");
						
						$content.="<option value=\"$p\" $sel>$opis</option>";
					}
				}
				elseif ($pola[1]=="su_opiekun")
				{
					$content.="<option value=0>Bez</option>\n";
					$firma=$SYSTEM[master];
					if (!$firma) $firma=$AUTH[parent]+0;
					$query="SELECT su_id AS _su_id,su_imiona,su_nazwisko FROM system_user
							WHERE su_parent=$firma ORDER BY su_nazwisko ";
					$su_res=$projdb->Execute($query);

					for($op=0;$op<$su_res->RecordCount();$op++)
					{
						parse_str(ado_ExplodeName($su_res,$op));
						$sel=($val==$_su_id)?"selected":"";
						$content.="<option value=$_su_id $sel>$su_imiona $su_nazwisko</option>\n";
					}
				}
				$content.="</select>";
				
			}
			else
			{
				if ($pola[4]=="d") 
				{
					$val=($val)?humandate($val):"";
				}
				if (is_integer($pola[4]))
				{
					$height=15*$pola[4];
					$content="<textarea $onChNM name=\"form[$pola[1]]\" class=\"sys_input\" style=\"width:$width;height:$height\">$val</textarea>";
				}
				else
				{
					$content="<input $onChNM type=\"$type\" name=\"form[$pola[1]]\" value=\"$val\" class=\"sys_input\" style=\"width:$width\" $validation>";
				}
				if ($pola[4]=="d") $content.=" [dd-mm-rrrr]";
				
			}

			$table.="\t<td>$content</td>\n";
			$table.="</tr>\n";
		}
	}
?>

<form action="<?php echo $prevpage?>" method="POST" name="rollbackForm_<? echo $sid ?>">
<INPUT TYPE="hidden" name="form[parent_id]" value="<? echo $oddzial_id ?>">
<?php
	echo $id;
	echo sort_navi_options($LIST);
?>
</form>


<form action="<?php echo $np?$next:$self?>" method="POST" name="commitForm" 
	onSubmit="return validateForm(this)">


<input type="hidden" name="action" value="<?php echo $_action?>">


<?php
	echo $id;
	echo sort_navi_options($LIST);
?>
<table  cellspacing=0 cellpadding=0 border=0 class="sys_table" width="100%">
<col width="125">
<tbody>
<? 
	echo $table;
	echo $sj_tr;	 
?>
</tbody>
<tfoot>
<tr><td colspan=2>
	<?if ($su&1) { ?>
	<input type="button" class="sys_button" value="Powrót" onClick="document.rollbackForm_<? echo $sid ?>.submit()">
	<? }  ?>
	<input type="submit" class="sys_button" value="Zapisz">
	</td></tr>
</tfoot>
</table>
</form>


<?
	if ($firma_JS_defined) return;
	$firma_JS_defined=1;


	if (!($su&128))
	{
		echo "<script>
				function validateForm(obj)
				{
					return true;
				}
				
				</script>
			";
		return;
	}
?>
<script>

function validateForm(obj)
{
	
	oCol=obj.elements;
	oColLen=oCol.length;
	for (i=0;i<oColLen;i++ )
	{
		var oElem=oCol(i);
		var wyr=oElem.validate;

		if (wyr==1)
		{
			if (oElem.minlength != null && oElem.minlength>oElem.value.length )
			{
				if (oElem.value.length==0) alert('Nie podano danych w polu "'+oElem.title+'"'); 
				else alert('Za mało znaków w polu "'+oElem.title+'"');
				oElem.focus();
				return false;
			}

		}
		if (wyr=='email')
		{
			if (oElem.minlength != null && oElem.minlength>oElem.value.length )
			{
				alert('Nie podano danych w polu "'+oElem.title+'"'); 
				oElem.focus();
				return false;
			}
			if (oElem.value.length>0 && checkEmail(oElem))
			{
				alert('Nieprawidłowy format adresu E-mail');
				oElem.focus();
				return false;
			}
		}
	}
	return true;
}

function checkEmail(obj)
{
	re = new RegExp("[a-z|A-Z|0-9|\.|\-|\_]+@[a-z|A-Z|0-9|\.|\-|\_]+");

	if (!re.test(obj.value))
	{
	 	obj.focus();
		return true;
	}
	return false;
}

</script>
