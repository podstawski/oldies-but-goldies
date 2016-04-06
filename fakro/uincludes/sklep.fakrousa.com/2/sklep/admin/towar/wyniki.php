<?
	include("$SKLEP_INCLUDE_PATH/list.h");
	if (!strlen($LIST[sort_f])) $LIST[sort_f]="to_nazwa";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$qs = $LIST[szukaj];
	
	if (strlen($costxt))
		parse_str($costxt);

	$ZASTOSOWANO_FILTR="";
	$pr_options="";
	$sql = "SELECT * FROM producent ORDER BY pr_nazwa";
	$result = $adodb->execute($sql);
	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));

		$sel=($CIACHO[pr_id]==$pr_id) ? "selected":"sel";
		$pr_options.="<option value='$pr_id' $sel>$pr_nazwa</option>\n";

		if ($CIACHO[pr_id]==$pr_id) $ZASTOSOWANO_FILTR.="<B>".sysmsg('Limit','admin').":</B> ".sysmsg('producer','admin')." = <B>$pr_nazwa</B><br>"; 
	}


	if (strlen($CIACHO[kateg]))
	{
		$sql = "SELECT ka_nazwa FROM kategorie WHERE ka_id=".$CIACHO[kateg];
		parse_str(ado_query2url($sql));

		$path = getFullPath($CIACHO[kateg]);
		$path = explode(";",$path);
		$path = array_reverse($path);
		$path = implode("->",$path); 		

		$ZASTOSOWANO_FILTR.="<B>".sysmsg('Limit','admin').":</B> ".sysmsg('category','admin')." = <B>$path</B><br>"; 
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
			@session_register("KAT");// co ciekawe - bez tego tego sesyja nie dzia³a :(
		}		
		return $wynik;
	}

	$options=towar_select_options($projdb,$CIACHO[kateg]);
	$options = "<option value=\"\">".sysmsg('All categories','admin')."</option>$options";	
	$kateg = "<SELECT NAME=\"ciacho[kateg]\" onChange=\"document.cookie=this.name+'='+this.value+';path=/'; document.list_sort_form.submit()\">
	$options</SELECT>";

	echo "<div align='right'>";
	if (strlen($pr_options))
	{
		echo "
				<select name='ciacho[pr_id]' 
					onChange=\"document.cookie=this.name+'='+this.value+';path=/'; document.list_sort_form.submit()\">
				<option value=0>Wszyscy producenci</option>
				$pr_options
				</select>
			";
	}
	echo "$kateg</div>";


	$qs = trim($qs);
	if (strlen($qs))
	{
		$qs = ereg_replace("  ","",$qs);
		$qs = strtolower($qs);

		function checkForPolish($str)
		{
			if (strpos(" ".$str,"¶")) return true;
			if (strpos(" ".$str,"±")) return true;
			if (strpos(" ".$str,"¼")) return true;
			if (strpos(" ".$str,"¦")) return true;
			if (strpos(" ".$str,"¡")) return true;
			if (strpos(" ".$str,"¬")) return true;
			if (strpos(" ".$str,"ê")) return true;
			if (strpos(" ".$str,"Ê")) return true;
			if (strpos(" ".$str,"ñ")) return true;
			if (strpos(" ".$str,"Ñ")) return true;
			if (strpos(" ".$str,"³")) return true;
			if (strpos(" ".$str,"£")) return true;
			if (strpos(" ".$str,"æ")) return true;
			if (strpos(" ".$str,"Æ")) return true;
			if (strpos(" ".$str,"¿")) return true;
			if (strpos(" ".$str,"¯")) return true;
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
	

	$FROMWHERE = "FROM towar LEFT JOIN towar_sklep ON to_id=ts_to_id AND ts_sk_id=$SKLEP_ID  
					$addfrm ";
	if (strlen(trim($add_sql))) 
	{
		$where=eregi_replace("^and","WHERE",trim($add_sql));
		$FROMWHERE.=" $where";
	}

	$sql = "SELECT * $FROMWHERE ORDER BY ".$sort;


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
		echo sysmsg('Invalid characters where used when searching','admin')." !!!<br>"; 
		return;

	}

	echo "$ZASTOSOWANO_FILTR<br>";

	if (!$result->RecordCount()) return;

	$n_wymiar=sysmsg("th_name","system");
	$i_wymiar=sysmsg("th_index","system");

	if ($ctx_cena)
		$cn = "<th sort=\"ts_cena\">Cena netto";

	$table ="
	$navi
	<table id=\"wydruk\" class=\"list_table\">
	<TR>		
		<th sort=\"ts_pri\" title=\"".sysmsg('Sort by','admin')."\">".sysmsg("Lp.","system")."
		<th sort=\"to_indeks\" nowrap>$i_wymiar
		<th sort=\"to_nazwa\">$n_wymiar
		$cn
		<th sort=\"ts_pri2\" title=\"".sysmsg('Sort by','admin')." 2\">Akcje";

	$qs=sort_navi_qs($LIST);

	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));


		$disable=$ts_aktywny?"":"disabled";


		$options="&nbsp;";
		$options = "<img src=\"$SKLEP_IMAGES/i_zobacz.gif\" alt=\"".sysmsg('Look','admin')."\" style=\"cursor:hand\" onClick=\"show_item('$to_id');\">&nbsp;";
		$options.= "<img src=\"$SKLEP_IMAGES/i_delete.gif\" style=\"cursor:hand\" alt=\"".sysmsg('Delete','admin')."\" onClick=\"usunTow('$to_id')\">";

		if ($i && ($LIST[sort_f]=='ts_pri' || $LIST[sort_f]=='ts_pri2'))
		{
			$options.= "<img src=\"$SKLEP_IMAGES/i_up.gif\" style=\"cursor:hand\" alt=\"".sysmsg('Move up','admin')."\" onClick=\"chpri($ts_id,$last_ts_id)\">";
		}
		$last_ts_id=$ts_id;

		if (file_exists("$SOAP_PATH/WS_AktualizacjaTowaru.h")) 
		{
			$options.=" <a href='$self${next_char}$qs&list[to_id]=$to_id&action=WS_AktualizacjaTowaru'>
				<img src=\"$SKLEP_IMAGES/i_ws.gif\" border=\"0\" style=\"cursor:hand\" alt=\"Aktualizacja towaru na podstawie Subiekta\" ></a>";
		}


		$lst = "";
	
		if ($ctx_cena)
		{
			$cn = "<td align=\"right\">".u_cena($ts_cena);
		}
		$table.="<tr dbid=\"$to_id\" $disable>
			<td title=\"$ts_pri\">".($i+1+$LIST[start])."
			<td title=\"w $to_ka_c kategoriach\">".stripslashes($to_indeks)."
			<td>".stripslashes($to_nazwa)."
			$cn
			<td title=\"$ts_pri2\">$options";
	}

	$table.="</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killTow\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TowarUsun\">
	<INPUT TYPE=\"hidden\" name=\"form[killid]\" id=\"killId\" value=\"\">
	".sort_navi_options($LIST)."
	</FORM>

	<FORM METHOD=POST ACTION=\"$self\" name=\"chpriform\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ChangeTSPri\">
	<INPUT TYPE=\"hidden\" name=\"form[sort_f]\" id=\"sort_f\" value=\"$LIST[sort_f]\">
	<INPUT TYPE=\"hidden\" name=\"form[ts_1]\" id=\"id_1\" value=\"\">
	<INPUT TYPE=\"hidden\" name=\"form[ts_2]\" id=\"id_2\" value=\"\">
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
		kartoteka_popup('<? echo $next?>','towar');
	}

	function show_selected_item()
	{
		table=getObject('wydruk');
		if (!table.selectedId) return;

		document.cookie='ciacho[admin_to_id]='+table.selectedId;
		kartoteka_popup('<? echo $next?>','towar');

	}

	function list_selected_item()
	{		
	}

	function usunTow(id)
	{		
		if (confirm('<? echo sysmsg('Are you sure you want to delete','admin')?> ?'))
		{
			document.killTow.killId.value = id;
			document.killTow.submit();
		}
	}

	function chpri(id_1,id_2)
	{		
		document.chpriform.id_1.value = id_1;
		document.chpriform.id_2.value = id_2;
		document.chpriform.submit();
	}

</script>
