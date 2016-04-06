<?
if ($debug_mode) $adodb->debug=1;
include_once("../include/search.h");

$KAMELEON_MODE=1; // zeby nie robil tablicy linkow
include_once ('../include/kameleon_href.h');
$KAMELEON_MODE=0;


if (strlen($api_size))
	$limit=$api_size;
else
	$limit=10;



$sql="UPDATE search_ustawienia SET u_sid=".$API_REQUEST['sid']."  WHERE servername='$KEY' AND u_sid=0";
$adodb->Execute($sql);

switch ($api_mode)
{
	case 1:
	case 3:
		$query =" SELECT u_params,u_msg,u_tsearch2 FROM search_ustawienia WHERE servername='$KEY'";
		if ($API_REQUEST['sid']) $query.=" AND u_sid=".$API_REQUEST['sid'];
		$result=$adodb->Execute($query);
		
		if ($result->RecordCount()>0) parse_str(ado_ExplodeName($result,0));

		$tsearch2=unserialize(base64_decode($u_tsearch2));
	
		parse_str($u_params);


		if(strlen($api_query))
		{



			//echo "(start=$start, offset=$offset, ile=$ile, limit=$limit)";

			if ($u_status==-1)
			{

				if (!$ile)
				{
					$ile=queryIndex2($api_query,$KEY,"","",1);
					$start=0;
				}

				$offset=$start;		
				$wynik=queryIndex2($api_query,$KEY,$limit,$offset);
			}
			else
			{
				if (!$ile)
				{
					$wynik=queryIndex($api_query,$KEY,"","");
					$ile=count($wynik['result']);
					$start=0;
				}
				//else
				{
					$offset=$start;			
					$wynik=queryIndex($api_query,$KEY,$limit,$offset);
				}
			}

			$result_msg=$wynik['msg'];
			$wynik=$wynik['result'];

			if (is_array($wynik)) if ($u_status!=-1) sort($wynik);
			$lp=$start;
			echo "<table border=0 width=\"100%\" cellspacing=0 cellpadding=0 class=\"api_search_res\">";
			$search_result="";
			$len=count($wynik);
		
			$query=urlencode($api_query);
			if ($api_km)
				$href="$api_next&api_query=$query";
			else
				$href="$api_next?api_query=$query";

			$nawigacja=naviIndex($href,$start,$offset,$ile,$limit);
			


			for ($i=0;$i<$len && is_array($wynik); $i++)
			{
				//echo "ile=$ile <b>$wynik[$i]</b> <br>";
				$w=explode(":",$wynik[$i]);
				$_lang=$w[0];
				$_version=$w[1];
				if ($_page!=$w[2])
					$_page=0+$w[2];
				else
					;//continue;
				$_pri=$w[3];
				$_file=trim($w[4]);

				$query="SELECT nositemap FROM webpage,servers WHERE webpage.lang='$_lang' AND webpage.id=$_page AND webpage.ver<=$ver 
						AND webpage.server=servers.id AND servers.nazwa='$KEY' ORDER BY webpage.ver DESC LIMIT 1";
				parse_str(ado_query2url($query));

				if ($nositemap) continue;

				if (strlen($w[5])) $opis=unserialize(base64_decode($w[5]));
				else $opis=getDesc($_page,$KEY,$lang,$ver);

				if (is_array($opis))
				{
					$title=stripslashes($opis[0]);
					$description=stripslashes($opis[1]);
				}
				
				if ($api_km)
					$href="$api_href?page=$_page";
				else
					$href=$_file;

				if (strlen($title)>0)
				{
  					$lp++;
  
  					$trclass=($lp%2)?'odd':'even';
  				
					$search_result .= "
						<tr class=\"sr_hr\"><td colspan=2><hr size=1></td></tr>
						<tr class=\"$trclass\">
							<td class=\"sr_res\">$lp. <a href=$href>$title</a></td>
						</tr>";
						
  					if (strlen($description)>0)
  					{
						$search_result .= "
							<tr class=\"$trclass\">
								<td class=\"sr_desc\">$description</td>
							</tr>";
					}
				}
			}
			if ($len>0)
			{

				$search_header="<tr><td colspan=2 class=\"sr_res_summary\">".label("Matching results for query")." <b>$api_query</b>, ".label("pages found").": $ile</td></tr>";
				if (strlen($result_msg))
					$search_header.="<tr><td colspan=2 class=\"sr_res_msg\">".$result_msg."</td></tr>";

				$search_nawigacja="<tr><td colspan=2 class=\"sr_navi\">$nawigacja</td></tr>";

			}
			else
			{
				$search_header="<tr><td colspan=2 class=\"sr_res_summary\">".label("Sorry, no matching pages were found")."</td></tr>";
				$search_nawigacja="";
				if (strlen($result_msg))
					$search_header.="<tr><td colspan=2 class=\"sr_res_msg\">".$result_msg."</td></tr>";
			}
			
			echo '<thead>';
			echo $search_header;
			echo $search_nawigacja;
			echo '</thead>';
			echo '<tbody>';
			echo $search_result;
			echo '</tbody>';
			echo '<tfoot>';
			echo $search_nawigacja;
			echo '</tfoot>';
			echo "</table>";
		}

	if ($api_em)
	{
		$sql='SELECT count(*) AS c FROM fts';
		parse_str(ado_query2url($sql));
		$TSEARCH2=$c;


	
		//echo "u_status=$u_ustatus&u_lang=$u_lang&u_ver=$u_ver&u_button_img";
	
		$o[]=array(0,label("No index"));
		$o[]=array(1,label("Index server this night"));
		$o[]=array(2,label("Index server every sunday"));
		if ($TSEARCH2) $o[]=array(-1,label("Dont index, use tsearch2"));


		$option="";
		for ($i=0;$i<count($o);$i++)
		{
			if ($o[$i][0]==$u_status)
				$selected="selected";
			else
				$selected="";
			$value=$o[$i][0];
			$name=$o[$i][1];
			$option.="<option $selected value=$value>$name";
		}

		$status="<select name=u_status class=k_select>$option</select>";
		
		$o="";
		foreach ($API_REQUEST['KAMELEON_CONST_LANGS'] AS $l ) $o[]=array($l,label($l));

		$option="";
		for ($i=0;$i<count($o);$i++)
		{
			if ($o[$i][0]==$u_lang)
				$selected="selected";
			else
				$selected="";
			$value=$o[$i][0];
			$name=$o[$i][1];
			$option.="<option $selected value=$value>$name ($value)";
		}
		$sel_lang="<select name=u_lang class=k_select>$option</select>";
		if (!strlen($u_ver)) $u_ver=$api_ver;

		if ($u_index_type==1)
			$u_index_type_checked1="selected";
		else
			$u_index_type_checked2="selected";

		echo "
			<fieldset style=\"width:99%; margin-left:2px;\">
			<legend>".label('Search results')."</legend>
			<table cellpadding=2 cellspacing=0 border=0 class=api_table width='100%'>
			<form method='post' action='index.php?page=$page'>
			$GLOBAL_HIDDEN
			<input type=hidden name=api_action value='apiZapiszSearch'>


			<tr class=k_form>
				<td align=right>".label("When").":</td>
				<td>$status</td> 
			</tr>
			<tr class=k_form>
				<td align=right>".label("Version number").":</td>
				<td><input type=text class=k_input size=4 name=u_ver value='$u_ver'></td>
			</tr>
			<tr class=k_form> 
				<td align=right>".label("Language").":</td>
				<td>$sel_lang</td>
			</tr>

			<tr class=k_form>
				<td align=right>".label("Image button").":</td>
				<td><input type=text class=k_input size=30 name=u_button_img value='$u_button_img'></td>
			</tr>
			<tr class=k_form>
				<td align=right>".label("Index only this version, if no then also index previous versions").":</td>
				<td>
					<select class=api_search_input name='u_index_type'>
						<option value=1 $u_index_type_checked1>".label("api_yes")."</option>
						<option value=0 $u_index_type_checked2>".label("api_no")."</option>
					</select>
				</td>
			</tr>



			<tr class=k_form>
				<td align=right>".label("Tree limit").":</td>
				<td><input type=text class=k_input size=4 name=u_tree value='$u_tree'></td>
			</tr>	

			<tr>
				<td align=center colspan=2 class=k_formtitle>".label("TSearch2 parameters").":</td>
			</tr>	

			<tr class=k_form>
				<td align=right>".label("Order by").":</td>
				<td><input type=text class=k_input size=40 name='tsearch2[order]' value=\"$tsearch2[order]\"></td>
			</tr>

			<tr class=k_form>
				<td colspan=2 align=center><input class=k_button type='submit' value='".label("Save")."'></td>
			</tr>

			<tr>
				<td colspan=2 id='search_results_detail' style='display:none' class=k_formtitle>$u_msg</td>
			</tr> 
			<tr>
				<td colspan=2 style='cursor:pointer' onClick=\"this.style.display='none';document.getElementById('search_results_detail').style.display=''\">".label('Show status')."</td>
			</tr>
			</form>
			</table></fieldset><br/>&nbsp;";
	}
	break;



 default:

	$query =" SELECT u_params FROM search_ustawienia WHERE servername='$KEY' AND u_sid=".$API_REQUEST['sid'];
	$result=$adodb->Execute($query);
	if ($result->RecordCount()>0) parse_str(ado_ExplodeName($result,0));
	parse_str($u_params);
	
	if (strlen($u_button_img))
		$button="<input class=api_search_button type=image src='$u_button_img'>";
	else
		$button="<input class=api_search_button type='submit' value='".label("Search")."'>";

	switch ($api_mode)
	{
		case 2:
			//AM #4 W3C standard - poprawka 
			echo "
			<form style=\"margin:0\" method=post action=$api_next>
			$GLOBAL_HIDDEN
			<table cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td><input type='text' class='api_search_input' name='api_query' size='$api_size' value='$api_query'></td>
				<td>&nbsp;$button</td>
			</tr>
			</table>
			</form>
			";
		break;
		default:
			echo "
			<form method=post action=$api_next>
			$GLOBAL_HIDDEN
			<input type='text' class='api_search_input' name='api_query' size='$api_size' value='$api_query'><br>$button
			</form>";
		break;
	}
}
