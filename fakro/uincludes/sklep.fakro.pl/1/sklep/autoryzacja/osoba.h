<?
	global $oddzial_id;
	
	$oddzial_id = $_REQUEST["oddzial_id"];

	$sg=0;
	parse_str($costxt);


	if ($su&8) // swoje dane
	{
		$query="SELECT sag_grupa_id AS sg FROM system_acl_grupa WHERE sag_server=$SERVER_ID
			AND sag_user_id=$AUTH[id] LIMIT 1";
		parse_str(ado_query2url($query));
		$LIST[id]=$AUTH[id];
	}

//	if (!$sg) return;

	$sj_tr="";


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

	$_action="OsobaDodaj";
	$id="";
	if ($LIST[id] && ($su&4))
	{
		$_action="OsobaModyfikuj";
		$query="SELECT * FROM system_user WHERE su_id=".$LIST[id];
		parse_str(ado_query2url($query));	
	}
	if (!$su&1 || !$su&4) $_action="";

	if (!$LIST[id] && !($su&1)) return;

	include ("$SKLEP_INCLUDE_PATH/autoryzacja/osoby_fields.h");

	$id="<input type=\"hidden\" name=\"form[su_id]\" value=\"$LIST[id]:$sg:$sg_coid:$suf:$suf2\">";

	$JS="";

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

	while (list($k,$v)=each($osoby_fields_grupy))
	{
		if (!is_array($form[$k])) continue;

//		$onChNM="onChange=\"noMore()\"";
		$onChNM = "";

		//$table.="<tr><th colspan=2>$v</th></tr>\n";
		$f=$form[$k];
		for ($i=0;$i<count($f);$i++)
		{
			$pola=$f[$i];

			$table.="<tr>\n";

			$table.="\t<td align=\"right\">$pola[0]</td>\n";
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
				while (list($p1,$p2)=each($pola[4]))
				{
					$sel=($val==$p1)?"selected":"";
					$content.="<option value=\"$p1\" $sel>$p2</option>";
				}
				$content.="</select>";
				
			}
			elseif (strstr($pola[1],"su_id_"))
			{
				$onch="";
				if (strlen($pola[4])) $onch="onChange=\"this.v=this.value; init_polska(this.value,'$pola[4]')\"";
				$content="";
				$content.="<select name=\"form[$pola[1]]\" id=\"$pola[1]\" class=\"formselect\" v=\"$val\" style=\"width:$width\" $onch child=\"$pola[4]\">";
				$content.="<option value=\"\">Wybierz</value>";
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
	

//if (!strlen($su_parent)) 
$su_parent = $oddzial_id;
?>


<? 
/*
	$sql = "SELECT COUNT(*) AS ile FROM system_user WHERE su_login = '$su_login'";
	parse_str(ado_query2url($sql));
	echo "== $ile =="; 
*/	
?>
<form action="<?php echo $prevpage?>" method="POST" name="rollbackForm">
<INPUT TYPE="hidden" name="form[parent_id]" value="<? echo $su_parent ?>">
<?php
	echo $id;
	echo sort_navi_options($LIST);
?>
</form>


<form action="<?php echo $np?$next:$prevpage?>" method="POST" name="commitForm" 
	onSubmit="return validateForm(this)">
<INPUT TYPE="hidden" name="form[parent_id]" value="<? echo $su_parent ?>">

<input type="hidden" name="action" value="<?php echo $_action?>">


<?php
	echo $id;
	echo sort_navi_options($LIST);
?>
<table class="sys_table">

<? 
	echo $table;
	echo $sj_tr;	 

?>

<tr><td colspan=2 class="submit" align="right">
	<INPUT TYPE="hidden" name="form[parent_id]" value="<? echo $su_parent ?>">
	
	<input type="button" class="but" value="Powrót" onClick="document.rollbackForm.submit()">
	<? if (strlen($_action)) { ?>
	<input type="submit" value="Zapisz" class="but">
	<? } ?>
	</td></tr>

</table>
</form>

<?
	$action = "";
?>


<?
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
