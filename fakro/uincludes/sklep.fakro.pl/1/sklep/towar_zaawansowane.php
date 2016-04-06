<?
	include_once("$SKLEP_INCLUDE_PATH/templates/cartform.php");
	include("$SKLEP_INCLUDE_PATH/list.h");
	if (!strlen($LIST[sort_f])) $LIST[sort_f]="to_nazwa";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$qs = $LIST[szukaj];
	
	if (strlen($costxt))
		parse_str($costxt);
	
	$msg_limit=sysmsg("Limit","towar");
	$msg_producer=sysmsg("limit-producer","towar");
	$msg_category=sysmsg("limit-category","towar");


	$ZASTOSOWANO_FILTR="";
	$pr_options="";
	$sql = "SELECT * FROM producent ORDER BY pr_nazwa";
	$result = $adodb->execute($sql);
	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));

		$sel=($CIACHO[pr_id]==$pr_id) ? "selected":"sel";
		$pr_options.="<option value='$pr_id' $sel>$pr_nazwa</option>\n";

		if ($CIACHO[pr_id]==$pr_id) $ZASTOSOWANO_FILTR.="<B>$msg_limit:</B> $msg_producer = <B>$pr_nazwa</B><br>"; 
	}


	if (strlen($CIACHO[kateg]))
	{
		$sql = "SELECT ka_nazwa FROM kategorie WHERE ka_id=".$CIACHO[kateg];
		parse_str(ado_query2url($sql));

		$path = getFullPath($CIACHO[kateg]);
		$path = explode(";",$path);
		$path = array_reverse($path);
		$path = implode("->",$path); 		

		$ZASTOSOWANO_FILTR.="<B>$msg_limit:</B> $msg_category = <B>$path</B><br>"; 
	}

	function towar_select_options($adodb,$page,$id="",$tab="")
	{
		global $projdb;
		global $SKLEP_SESSION;
		static $K;

	
		$K=$SKLEP_SESSION["KAT"];

		$ID=$id?"=$id":" IS NULL";
		$query="SELECT * FROM kategorie WHERE ka_parent$ID ORDER BY ka_nazwa";
		$result=$adodb->Execute($query);
		for ($i=0;$i<$result->RecordCount();$i++)
		{	
			parse_str(ado_ExplodeName($result,$i));

			
			if (!is_array($K[$ka_id]))
			{
				$query="SELECT count(*) AS c FROM kategorie WHERE ka_parent=$ka_id";
				parse_str(ado_query2url($query));
				//$query="SELECT count(*) AS t FROM towar_kategoria WHERE tk_ka_id=$ka_id";
				//parse_str(ado_query2url($query));

				//$K[$ka_id][t]=$t;
				$K[$ka_id][c]=$c;
				
			}
			else
			{
				$c=$K[$ka_id][c];
				//$t=$K[$ka_id][t];
			}

			//$style=$t?"color:#000000":"color:#a0a0a0";
			$value=$t?$ka_id:0;
			$value=$ka_id;
			$sel=($page==$ka_id)?" selected":"";

			//if ($t) $ka_nazwa.=" ($t szt)";
			if (strlen($ka_kod)) $ka_kod=" ....... strona $ka_kod";
			//if (!$t && !$c) $ka_kod.=" (id=$ka_id)"; 
			$wynik.="\n<option $sel value=\"$value\" style=\"$style\">$tab$ka_nazwa</option>";
			
			if ($c)
				$wynik.=towar_select_options($adodb,$page,$ka_id,$tab."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		}
		

		if (!$id) 
		{
			$SKLEP_SESSION["KAT"]=$K;
			$KAT=$K;
			session_register("KAT");// co ciekawe - bez tego tego sesyja nie działa :(
		}		
		return $wynik;
	}

	$options=towar_select_options($projdb,$CIACHO[kateg]);
	$options = "<option value=\"\">Wszystkie kategorie</option>$options";	
	$kateg = "
	<SELECT NAME=\"ciacho[kateg]\" onChange=\"document.cookie=this.name+'='+this.value+';path=/'; document.list_sort_form.submit()\">
	$options</SELECT>";

	if (strlen($pr_options))
	{
		$opcj =  "				
				<select name='ciacho[pr_id]' 
					onChange=\"document.cookie=this.name+'='+this.value+';path=/'; document.list_sort_form.submit()\">
				<option value=0>Wszystkie publikacje</option>
				$pr_options
				</select>
			";
	}
	echo "<div align='right'>
		<TABLE>
		<TR>
			<TD>Wybierz typ publikacji</TD>
			<TD>Wybierz kategorię</TD>
		</TR>
		<TR>
			<TD>$opcj</TD>
			<TD>$kateg</TD>
		</TR>
		</TABLE>
		</div>";


	$qs = trim($qs);
	if (strlen($qs))
	{
		$qs = ereg_replace("  ","",$qs);
		$qs = strtolower($qs);

		function checkForPolish($str)
		{
			if (strpos(" ".$str,"ś")) return true;
			if (strpos(" ".$str,"ą")) return true;
			if (strpos(" ".$str,"ź")) return true;
			if (strpos(" ".$str,"Ś")) return true;
			if (strpos(" ".$str,"Ą")) return true;
			if (strpos(" ".$str,"Ź")) return true;
			if (strpos(" ".$str,"ę")) return true;
			if (strpos(" ".$str,"Ę")) return true;
			if (strpos(" ".$str,"ń")) return true;
			if (strpos(" ".$str,"Ń")) return true;
			if (strpos(" ".$str,"ł")) return true;
			if (strpos(" ".$str,"Ł")) return true;
			if (strpos(" ".$str,"ć")) return true;
			if (strpos(" ".$str,"Ć")) return true;
			if (strpos(" ".$str,"ż")) return true;
			if (strpos(" ".$str,"Ż")) return true;
			return false;
		}

		$slowa = explode(" ",$qs);
		if (count($slowa)) $add_sql = "AND (";
		for ($i=0; $i < count($slowa); $i++)
		{
			$slowa[$i]=eregi_replace("&nbsp;"," ",$slowa[$i]);
			if (!strlen(trim($slowa[$i]))) continue;
			if (!strlen(trim($slowa[$i])) == "+") continue;
			if (substr($slowa[$i],0,1) == "+")
			{			
				$operator = "AND";
				$slowa[$i] = substr($slowa[$i],1);
			}
			else
				$operator = "OR";
 
			if (!strlen(trim($slowa[$i]))) continue;
			if (!$i) $operator = "";

			$add_stage = "";		
			if (checkForPolish($slowa[$i]))
				$add_stage = " OR to_nazwa ~* '".strtoupper($slowa[$i])."' OR to_indeks ~* '".strtoupper($slowa[$i])."'";

			$add_sql.= $operator." (to_ean='".$slowa[$i]."' OR to_nazwa ~* '".$slowa[$i]."' OR to_indeks ~* '".$slowa[$i]."' $add_stage) ";
		}
		if (count($slowa)) $add_sql.= ")";

	}

	if ($CIACHO[pr_id]) $add_sql.=" AND to_pr_id=".$CIACHO[pr_id];
	if (strlen($CIACHO[kateg])) 
	{
		$addfrm = ", towar_kategoria";
		$add_sql.=" AND tk_to_id=to_id AND tk_ka_id=".$CIACHO[kateg];
	}
	

	$FROMWHERE = "FROM towar $addfrm";
	if (strlen(trim($add_sql))) 
	{
		$where=eregi_replace("^and","WHERE",trim($add_sql));
		$FROMWHERE.=" $where";
	}

	$sql = "SELECT towar.* $FROMWHERE ORDER BY ".$sort;


	if (!$LIST[ile])
	{
		$query="SELECT count(to_id) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}
	$navi=$size?navi($self,$LIST,$size):"";
		
	if (strlen($navi))
		$result = $projdb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($sql);	
	if (!is_object($result)) 
	{
		echo "Użyto nieprawidłowych znaków przy wyszukiwaniu !!!<br>"; 
		return;

	}

	echo "$ZASTOSOWANO_FILTR<br>";

	if (!$result->RecordCount()) return;

	$n_wymiar=sysmsg("th_name","system");
	$i_wymiar=sysmsg("th_index","system");

	if ($ctx_cena)
		$cn = "<th class=\"cw\">Cena netto";

	$table ="
	$navi
	<table id=\"wydruk\" class=\"list_table\">
	<TR>		
		<th class=\"c1\">".sysmsg("Lp.","system")."
		<th sort=\"to_indeks\">$i_wymiar
		<th sort=\"to_nazwa\">$n_wymiar
		$cn
		<th class=\"co\">&nbsp;";
//		<th class=\"cw\" sort=\"to_ka_c\" title=\"".sysmsg("title_to_ka_c","system")."\">".sysmsg("K","system")."

	$qs=sort_navi_qs($LIST);

	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));


		$ts_aktywny=0;
		$query="SELECT ts_aktywny FROM towar_sklep WHERE ts_to_id=$to_id AND ts_sk_id=$SKLEP_ID";
		parse_str(ado_query2url($query));
		
		if (!$ts_aktywny) continue;

		$disable=$ts_aktywny?"":"disabled";

		$options="&nbsp;";

		if ($AUTH[id]>0)
			$JS_CART = "addItem2Cart('$to_id',".$WM->kwant_towaru($to_id).",'".sysmsg("$to_jm","cart")."')";
		else
			$JS_CART = "chageItemQuantity('$to_id',".$WM->kwant_towaru($to_id).",".$WM->kwant_towaru($to_id).",0,'".sysmsg("$to_jm","cart")."')";

		$lst = "";
/*
		if ($to_ka_c)
		{
			$sql = "SELECT ka_nazwa FROM kategorie, towar_kategoria 
					WHERE tk_ka_id = ka_id AND tk_to_id = $to_id";

			$subres = $projdb->execute($sql);
			for ($k=0; $k < $subres->RecordCount(); $k++)
			{
				parse_str(ado_explodename($subres,$k));			
				$lst.= "<br>$ka_nazwa";
			}
		}
*/		
		if ($ctx_cena)
		{
			$sql = "SELECT ts_cena FROM towar_sklep WHERE ts_to_id = $to_id
					AND ts_sk_id = $SKLEP_ID";
			parse_str(ado_query2url($sql));
			$cn = "<td class=\"cw\" nowrap>".u_cena($ts_cena);
		}

		$table.="<tr dbid=\"$to_id\" $disable>
			<td class=\"c1\">".($i+1+$LIST[start])."
			<td>".stripslashes($to_indeks)."
			<td>".stripslashes($to_nazwa)."
			$cn
			<td class=\"co\">
				
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"but\"><tr><td class=\"butl\">&nbsp;</td>
			<td class=\"butc\" align=\"center\">
			<input type=\"button\" style=\"width:60px; padding: 0;\" onClick=\"$JS_CART\" value=\"zamawiam\"></td><td class=\"butr\">&nbsp;</td></tr></table>
				
			
			";
//			<td class=\"cw\">$to_ka_c $lst
	}

	$table.="</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killTow\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TowarUsun\">
	<INPUT TYPE=\"hidden\" name=\"form[killid]\" id=\"killId\" value=\"\">
	".sort_navi_options($LIST)."
	</FORM>
	";
	echo $table;

	include("$SKLEP_INCLUDE_PATH/js.h");
?>
<script>
	list_table_init('wydruk','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);
	
	
	function show_item(seldId)
	{
		table=getObject('wydruk');
		table.selectedId = seldId;
		document.cookie='ciacho[admin_to_id]='+table.selectedId;
		opisProduktuClick(seldId);
		//kartoteka_popup('<? echo $next?>','towar');
	}

	function show_selected_item()
	{
		table=getObject('wydruk');
		if (!table.selectedId) return;
		document.cookie='ciacho[admin_to_id]='+table.selectedId;
		opisProduktuClick(table.selectedId);
		//kartoteka_popup('<? echo $next?>','towar');

	}

	function list_selected_item()
	{		
	}

	function usunTow(id)
	{		
		if (confirm('Na pewno usunąć ten produkt ?'))
		{
			document.killTow.killId.value = id;
			document.killTow.submit();
		}
	}


</script>
