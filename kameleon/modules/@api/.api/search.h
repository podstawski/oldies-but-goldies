<?
	$a=xml2obj($costxt);
	$xml=$a->xml;

	$cokolwiek=0;
	include_once("$INCLUDE_PATH/.api/search_fun.h");

	$OR = $xml->page_this ? "OR id=$page" : "";
	$search_page_cond="(tree ~ ':$page:' $OR)";
?>


<form method="POST" action="<?echo $next?>">

<table cellspacing=0 cellpadding=0 class="api2_search_table">
<? if ($xml->page_plain_yes) { $cokolwiek=1; ?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_plain_label ?>
		</td>
		<td class="api2_search_td_input">
			<input name="SEARCH[plain]" value="<? echo stripslashes($SEARCH[plain])?>" class="api2_search_input">
		</td>

	</tr>
<?}?>


<? if ($xml->page_keywords_yes) { $cokolwiek=1;?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_keywords_label ?>
		</td>
		<td class="api2_search_td_input">
			<input name="SEARCH[keywords]" value="<? echo stripslashes($SEARCH[keywords])?>" class="api2_search_input">
		</td>

	</tr>
<?}?>


<? if ($xml->page_d_create_yes) { $cokolwiek=1;?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_d_create_label ?>
		</td>
		<td class="api2_search_td_input">
			<input name="SEARCH[d_create_from]" value="<? echo $SEARCH[d_create_from]?>" class="api2_search_date">
			-
			<input name="SEARCH[d_create_to]" value="<? echo $SEARCH[d_create_to]?>" class="api2_search_date">
		</td>

	</tr>
<?}?>

<? if ($xml->page_d_update_yes) { $cokolwiek=1;?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_d_update_label ?>
		</td>
		<td class="api2_search_td_input">
			<input name="SEARCH[d_update_from]" value="<? echo $SEARCH[d_update_from]?>" class="api2_search_date">
			-
			<input name="SEARCH[d_update_to]" value="<? echo $SEARCH[d_update_to]?>" class="api2_search_date">
		</td>

	</tr>
<?}?>


<? if ($xml->page_pagekey_yes) { $cokolwiek=1;?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_pagekey_label ?>
		</td>
		<td class="api2_search_td_input">
			<? if ($xml->page_pagekey_select) {?>
				<select name="SEARCH[pagekey]" class="api2_search_select">
					<option value=""><? echo $xml->page_pagekey_label ?></option>
					<?
						$res=null;
						$query="SELECT DISTINCT pagekey AS str FROM webpage
							WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver
							AND pagekey<>'' AND pagekey IS NOT NULL
							AND $search_page_cond";
						$res=$adodb->Execute($query);
						for ($i=0;$res && $i<$res->RecordCount();$i++)
						{	
							parse_str(ado_ExplodeName($res,$i));
							if ($__pagekey[$str]==1) continue;
							$__pagekey[$str]=1;
							$s=($str==$SEARCH[pagekey]) ? "selected" : "";
							echo "<option value=\"$str\" $s>$str</option>\n";
						}

					?>
				</select>
			<?} else {?>
				<input name="SEARCH[pagekey]" value="<? echo stripslashes($SEARCH[pagekey])?>" class="api2_search_input">
			<?}?>
		</td>

	</tr>
<?}?>

<? if ($xml->page_title_yes) { $cokolwiek=1;?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_title_label ?>
		</td>
		<td class="api2_search_td_input">
			<? if ($xml->page_title_select) {?>
				<select name="SEARCH[title]" class="api2_search_select">
					<option value=""><? echo $xml->page_title_label ?></option>
					<?
						$res=null;
						$query="SELECT DISTINCT title AS str FROM webpage
							WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver
							AND title<>'' AND title IS NOT NULL
							AND $search_page_cond";
						$res=$adodb->Execute($query);
						for ($i=0;$res && $i<$res->RecordCount();$i++)
						{	
							parse_str(ado_ExplodeName($res,$i));
							if ($__title[$str]==1) continue;
							$__title[$str]=1;
							$s=($str==$SEARCH[title]) ? "selected" : "";
							echo "<option value=\"$str\" $s>$str</option>\n";
						}

					?>
				</select>
			<?} else {?>
				<input name="SEARCH[title]" value="<? echo stripslashes($SEARCH[title])?>" class="api2_search_input">
			<?}?>
		</td>

	</tr>
<?}?>


<? if ($xml->page_description_yes) { $cokolwiek=1;?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_label">
			<? echo $xml->page_description_label ?>
		</td>
		<td class="api2_search_td_input">
			<? if ($xml->page_description_select) {?>
				<select name="SEARCH[description]" class="api2_search_select">
					<option value=""><? echo $xml->page_description_label ?></option>
					<?
						$res=null;
						$query="SELECT DISTINCT description AS str FROM webpage
							WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver
							AND description<>'' AND description IS NOT NULL
							AND $search_page_cond";
						$res=$adodb->Execute($query);
						for ($i=0;$res && $i<$res->RecordCount();$i++)
						{	
							parse_str(ado_ExplodeName($res,$i));
							if ($__description[$str]==1) continue;
							$__description[$str]=1;
							$s=($str==$SEARCH[description]) ? "selected" : "";
							echo "<option value=\"$str\" $s>$str</option>\n";
						}

					?>
				</select>
			<?} else {?>
				<input name="SEARCH[description]" value="<? echo stripslashes($SEARCH[description])?>" class="api2_search_input">
			<?}?>
		</td>

	</tr>
<?}?>



<? if ($cokolwiek) { ?>
	<tr class="api2_search_tr">
		<td class="api2_search_td_submit" colspan=2>
			<input type="submit" value="<? echo $xml->search_button?>" class="api2_search_submit">
		</td>
	</tr>
<?}?>

</table>

</form>
<?
	if ($cos) return;
	$showform=$cokolwiek;

	$where="webpage.server=$SERVER_ID AND webpage.ver=$ver
		AND webpage.lang='$lang' AND (webpage.hidden=0 OR webpage.hidden IS NULL) 
		AND $search_page_cond";
	$from="webpage";
	
	$cokolwiek=0;


	if (strlen(trim($SEARCH[description])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		if ($xml->page_description_select) $where.="description = ";
		else $where.="description ~* ";

		$where.="'".trim($SEARCH[description])."'";
		
	}


	if (strlen(trim($SEARCH[pagekey])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		if ($xml->page_pagekey_select) $where.="pagekey = ";
		else $where.="pagekey ~* ";

		$where.="'".trim($SEARCH[pagekey])."'";
		
	}


	

	if (strlen(trim($SEARCH[keywords])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		if ($xml->page_keywords_select) $where.="keywords = ";
		else $where.="keywords ~* ";

		$where.="'".trim($SEARCH[keywords])."'";
		
	}

	if (strlen(trim($SEARCH[title])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		if ($xml->page_title_select) $where.="title = ";
		else $where.="title ~* ";

		$where.="'".trim($SEARCH[title])."'";
		
	}

	if (strlen(trim($SEARCH[plain])))
	{
		$query="SELECT nazwa AS servername FROM servers WHERE id=$SERVER_ID";
		parse_str(ado_query2url($query));

		$cokolwiek=1;
		$where.="\n	AND search_slownik.s_slowo ~* ";
		$where.="'".trim($SEARCH[plain])."'";
		$from.=",search_slownik,search_index";
		$where.="\n     AND search_slownik.s_id=search_index.i_id 
						AND search_index.i_page=webpage.id 
						AND search_slownik.servername='$servername'
						AND search_index.ver=$ver AND search_index.lang='$lang'";
	
	}

	if (strlen(trim($SEARCH[d_create_from])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		$where.="webpage.nd_create >= ".FormatujDateSQL($SEARCH[d_create_from]);
	}

	if (strlen(trim($SEARCH[d_create_to])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		$where.="webpage.nd_create <= ".FormatujDateSQL($SEARCH[d_create_to]);

		
	}


	if (strlen(trim($SEARCH[d_update_from])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		$where.="webpage.nd_update >= ".FormatujDateSQL($SEARCH[d_update_from]);
		
	}

	if (strlen(trim($SEARCH[d_update_to])))
	{
		$cokolwiek=1;
		$where.="\n	AND ";
		$where.="webpage.nd_update <= ".FormatujDateSQL($SEARCH[d_update_to]);
		
	}


	$query="SELECT webpage.id,webpage.title,webpage.nd_create,webpage.nd_update 
			FROM $from
			WHERE $where
			ORDER BY webpage.nd_create DESC,webpage.tree";

	//echo nl2br($query);
	//$adodb->debug=1;
	$t=time();
	if (!$cokolwiek) return;
	$res=$adodb->Execute($query);
	//echo time()-$t;

	if (!$res || !$res->RecordCount()) 
	{
		if (!$showform) echo label("Sorry, no matching pages were found");
		return;
	}
	
	echo "<table cellspacing=0 cellpadding=0 class=\"api2_search_res_table\">\n";

	$lp=1;

	for ($i=0;$i<$res->RecordCount();$i++)
	{	
		parse_str(ado_ExplodeName($res,$i));

		if ($search_pages[$id]) continue;
		$search_pages[$id]=1;
		if (!$i) 
		{
			$colspan=2;
			if ($xml->page_d_create_yes) $colspan++;
			echo "<tr><td class=\"api2_search_res_td_title\" colspan=\"$colspan\">".label("Matching results for query")."</td></tr>";
		}
		echo "<tr class=\"api2_search_res_tr\">";
		
		echo "<td class=\"api2_search_res_td_counter\">";
		echo $lp++;
		echo ".</td>";
		echo "<td class=\"api2_search_res_td\">";
		echo "<a class=\"api2_search_res_a\" href=\"";
		echo kameleon_href("","",$id)."\">";
		echo stripslashes($title);
		echo "</a></td>";
		
		if ($xml->page_d_create_yes)
		{
			echo "<td nowrap class=\"api2_search_res_td\">";
			echo "<a class=\"api2_search_res_a\" href=\"";
			echo kameleon_href("","",$id)."\">";
			echo FormatujDate($nd_create);
			echo "</a></td>";

		}

		
		echo "</tr>\n";
	}
	echo "</table>\n";

?>
