<?
if ($INDEX_INCLUDED)
   return;
$INDEX_INCLUDED=1;

function unpolish($text)
{
 $text=ereg_replace("π","a",$text);
 $text=ereg_replace("Í","e",$text);
 $text=ereg_replace("Ò","n",$text);
 $text=ereg_replace("≥","l",$text);
 $text=ereg_replace("Û","o",$text);
 $text=ereg_replace("ø","z",$text);
 $text=ereg_replace("ü","z",$text);
 $text=ereg_replace("Ê","c",$text);
 $text=ereg_replace("ú","s",$text);
 $text=ereg_replace("±","a",$text);
 $text=ereg_replace("∂","s",$text);
 $text=ereg_replace("º","z",$text);

 $text=ereg_replace("•","a",$text);
 $text=ereg_replace(" ","e",$text);
 $text=ereg_replace("—","n",$text);
 $text=ereg_replace("£","l",$text);
 $text=ereg_replace("”","o",$text);
 $text=ereg_replace("Ø","z",$text);
 $text=ereg_replace("è","z",$text);
 $text=ereg_replace("∆","c",$text);
 $text=ereg_replace("å","s",$text);

 $text=ereg_replace("°","a",$text);
 $text=ereg_replace("¶","s",$text);
 $text=ereg_replace("¨","z",$text);

 return $text;
}

function polishtolower($text)
{
 global $CHARSET;

 if (strstr(strtolower($CHARSET),'utf')) return mb_strtolower($text, 'UTF-8');
 
 $text=ereg_replace("•","π",$text);
 $text=ereg_replace("å","ú",$text);
 $text=ereg_replace("Ø","ø",$text);
 $text=ereg_replace("è","ü",$text);
 $text=ereg_replace("∆","Ê",$text);
 $text=ereg_replace(" ","Í",$text);
 $text=ereg_replace("—","Ò",$text);
 $text=ereg_replace("”","Û",$text);
 $text=ereg_replace("£","≥",$text);
 $text=ereg_replace("°","±",$text);
 $text=ereg_replace("¶","∂",$text);
 $text=ereg_replace("¨","º",$text);
 return strtolower($text);
}

function slownik($html,$servername)
{
	global $adodb;
	$q=explode(" ",trim($html));
	for ($j=0;$j<count($q);$j++)
	{
		$slowo=trim($q[$j]);
		if (strlen($slowo))
		{
			$SQL = "SELECT s_slowo FROM search_slownik WHERE s_slowo='$slowo' AND servername='$servername'";
			$search_slownik=$adodb->Execute($SQL);
			if (!$search_slownik)
			{
				echo "search_slownik, select error "; 
				return 0;
			}
			if ($search_slownik->RecordCount()==0)
			{
				$SQL =" INSERT INTO search_slownik (s_slowo,servername) VALUES ('$slowo','$servername') ";
				$search_slownik=$adodb->Execute($SQL);
				if (!$search_slownik)
				{
					echo "search_slownik, insert error"; 
					return 0;
				}
			}
		}
	}
	return 1;
}

function addIndex($href,$html,$servername,$lang,$version,$p_id,$tree='')
{
	global $adodb;

	$html=wordsFromHtml($html);
	if (!slownik($html,$servername))
	{
		echo "error, funkcja slownik()"; 
		return 0;
	}
	$q=explode(" ",trim($html));
	for ($j=0;$j<count($q);$j++)
	{
		$slowo=trim($q[$j]);
		if (strlen($slowo))
		{
			$SQL =" INSERT INTO search_index (i_id,i_href,servername,lang,ver,i_page)";
			$SQL.=" SELECT s_id,'$href','$servername','$lang',$version,$p_id ";
			$SQL.=" FROM search_slownik WHERE servername='$servername' AND s_slowo='$slowo'";
	//		echo $SQL."\n\n";

			$insert=$adodb->Execute($SQL);
			if (!$insert) echo "error, function addIndex \n $SQL\n";
			$lp++;
		}
	}
	return $lp;
}

function deleteIndex($href,$servername,$lang,$version,$page=-1)
{
	global $adodb;
	if (strlen($href)>0)
		//$cond=" WHERE servername='$servername' AND i_href like '$href%' ";
		$cond=" WHERE servername='$servername' AND i_page=$page ";
	else
		$cond="WHERE servername='$servername'";  
	$SQL=" DELETE FROM search_index $cond AND lang='$lang' AND ver=$version";
	$delete=$adodb->Execute($SQL);
}

function verifySearchWord($w)
{
	if (!strlen(trim($w))) return;
	

	if (eregi('[\<\>]',$w))
	{
		return label('Expresion').' <span class="km_msg_expresion">'.$w.'</class> '.label('consists of unlegal characters.');

	}

	if (strlen(trim($w))==1)
	{
		return label('Expresion').' <span class="km_msg_expresion">'.$w.'</class> '.label('consists of only one letter. Should have at least two.');
	}

}


function queryIndex2($zapytanie,$servername,$limit,$offset,$count=0)
{
	global $adodb,$lang,$ver;
	global $C_SEARCH_MINOR_VERSION;
	global $API_REQUEST;


	$war="";
	if (strlen(trim($zapytanie)))
	{
		$q=explode(" ",trim($zapytanie));
		for ($i=0;$i<count($q);$i++)
		{
			$q[$i]=trim($q[$i]);
			if (strtolower($q[$i])=='do') continue;
	
			if ($msg=verifySearchWord($q[$i])) 
			{
				$wynik['msg'].=$msg.'<br/>';
				continue;
			}
			if (strlen($q[$i]))
			{
				if (strlen($war)) $war.=" OR ";
				$war.="fts_text @@ plainto_tsquery('$q[$i]')";
			}
		}
	}


	if (strlen($war)>0)
	{
		$query="SELECT u_params, u_tsearch2
				FROM search_ustawienia WHERE servername='$servername' AND u_sid=".$API_REQUEST['sid'];

		parse_str(ado_query2url($query));
		parse_str($u_params);
		$tsearch2=unserialize(base64_decode($u_tsearch2));

		$query="SELECT id AS server_id FROM servers WHERE nazwa='$servername'";
		parse_str(ado_query2url($query));

		$warunki='';

		if (strlen($u_tree))
		{
			$warunki.="AND (webpage.id=$u_tree OR webpage.tree ~ ':$u_tree:')";
		}
		
		$verand=$C_SEARCH_MINOR_VERSION ? "AND (fts_ver=$ver OR fts_ver=$C_SEARCH_MINOR_VERSION)":"AND fts_ver=$ver";

				
		$order="coalesce(webpage.keywords,'') ~* '$q[0]' DESC,
				webpage.title ~* '$q[0]' DESC,
				count(*) DESC,
				webpage.nd_update DESC,fts_page_sid";
		if (strlen($tsearch2[order])) $order=$tsearch2[order].','.$order;

		$query="SELECT id,fts_ver,webpage.title,webpage.description,file_name,default_file_name
				FROM fts
				LEFT JOIN webpage ON webpage.sid=fts_page_sid
				LEFT JOIN webtd ON webtd.sid=fts_td_sid
				WHERE ($war)
				AND fts_server=$server_id AND fts_lang='$lang' $verand
				AND (webpage.hidden IS NULL OR webpage.hidden=0)
				AND (webpage.nositemap IS NULL OR webpage.nositemap=0)
				AND (webtd.hidden IS NULL OR webtd.hidden=0)
				$warunki
				GROUP BY id,fts_ver,webpage.title,webpage.description,file_name,webpage.type,fts_server,default_file_name,
							webpage.nd_update,fts_page_sid,keywords
				ORDER BY $order
				";
		
		if (!$count) if (strlen($limit) && strlen($offset)) $query.=" LIMIT $limit OFFSET $offset";


		//$adodb->debug=1; //
		$res=$adodb->Execute($query);
		if (!$res) return;
		$row_len=$res->RecordCount();

	
		//echo "$row_len:<br>".nl2br($query); //
		if ($count) return $row_len;
		
		$query="SELECT file_name, default_file_name 
				FROM webpage 
				WHERE id=$page_result AND lang='$lang' AND server=$server_id AND ver<=$ver
				ORDER BY ver DESC 
				LIMIT 1";
		parse_str(ado_query2url($query));

		$myself=strlen($file_name)?$file_name:$default_file_name;
		


		for ($i=0;$i<$row_len;$i++)
		{
			parse_str(ado_ExplodeName($res,$i));

			$target=strlen($file_name)?$file_name:$default_file_name;
			$href=kameleon_relative_dir($myself,$target);

			$ar[]="$lang:$fts_ver:".sprintf("%06d",$id).":1:$href:".base64_encode(serialize(array(stripslashes($title),stripslashes($description))));
		}

	}
	else
	{
		if ($count) return 0;
	}

	$wynik['result']=$ar;

	return $wynik;

}



function queryIndex($zapytanie,$servername,$limit,$offset)
{

	global $adodb,$lang,$ver;
	global $C_SEARCH_MINOR_VERSION;
	
	$zapytanie=polishtolower($zapytanie);
	$war="";
	if (strlen(trim($zapytanie)))
	{
		$q=explode(" ",trim($zapytanie));
		for ($i=0;$i<count($q);$i++)
		{
			$q[$i]=trim($q[$i]);
			if (strlen($q[$i]))
			{
				if ($i)
					$war.=" OR s_slowo = '$q[$i]'";
				else
					$war.="s_slowo = '$q[$i]'";
			}


		}
	};
	
	if (strlen($war)>0)
	{
		$query="SELECT u_params FROM search_ustawienia WHERE servername='$servername'";
		parse_str(ado_query2url($query));
		parse_str($u_params);


		$SQL =" SELECT s_id FROM search_slownik WHERE servername='$servername' AND ($war)";
		$res=$adodb->Execute($SQL);
		$row_len=$res->RecordCount();	
		

		for ($i=0;$i<$row_len;$i++)
		{
			parse_str(ado_ExplodeName($res,$i));
			if ($i)
			{
				$verand=$C_SEARCH_MINOR_VERSION ? '':"AND ver=$ver";
				$SQL =" SELECT i_href FROM search_index WHERE i_id=$s_id AND servername='$servername' AND lang='$lang' $verand AND i_href IN (SELECT '0' AS i_href UNION $SQL)";
			}
			else
			{
				$SQL =" SELECT i_href FROM search_index WHERE i_id=$s_id AND servername='$servername' AND lang='$lang'";
			}

			if (strlen($u_tree))
			{
				$SQL.="AND (i_page=$u_tree OR s_tree ~ ':$u_tree:')";
			}
		}

		if (strlen($limit) && strlen($offset)) $SQL.=" LIMIT $limit OFFSET $offset";
		$search_index=$adodb->Execute($SQL);
		$row_len=$search_index->RecordCount();
	
		for ($i=0;$i<$row_len;$i++)
		{
			parse_str(ado_ExplodeName($search_index,$i));
			$w=explode(":",$i_href);
			$_lang=$w[0];
			$_version=$w[1];
			if ( (!$C_SEARCH_MINOR_VERSION && $_version!=$ver) || $_lang!=$lang) continue;

			$ar[]=$i_href;
		}

		$wynik['result']=$ar;
		return $wynik;
	}


}

function naviIndex($href,$start,$offset,$ile,$size)
{
	global $navi;
	if ($start<0 || $start>$ile) $start=0;
	$start+=0;
	if (!strlen($navi)) $navi=1;
	$offset=0+$start;
	if ($start+$size<$ile)
		$next=$start+$size;

	$_dest=urlencode($dest);

	$naviend=5;

	if ($start==($navi+$naviend)*$size)
		$navi=$start/$size;
	else
		if ($start<$navi*$size)
		{
			if ($navi-$naviend<=0)
				$navi=1;
			else
				$navi=$navi-$naviend;
		}
		else
			if ($start+$size==($navi+$naviend)*$size)
				$navi=$navi+$naviend;

	$pom=($naviend+$navi) * $size;
	if ($pom>=$ile)
	{
		$naviend=(0+floor($ile / $size)) % 5;
	}
	else
		$naviend=5;

//	echo "navi=$navi, naviend=$naviend";
	
	$all_link="$href&navi=$navi";

	$next_link=$all_link."&ile=$ile&start=$next";
	$back=$offset-$size;
	$prev_link=$all_link."&ile=$ile&start=$back";

	$linkp="&nbsp;";
	$linkn="&nbsp;";
	if ($start==0)
		$linkn="<a href=$next_link>&raquo;&raquo;</a> ";
	else
	{
		$linkp="<a href=$prev_link>&laquo;&laquo;</a>";
		if ($start+$size<$ile)
			$linkn="<a href=$next_link>&raquo;&raquo;</a>";
	}

	$pasek="";
	//echo "<br>navi=$navi, naviend=$naviend, ile=$ile, size=$size<br>";return;

	$stron=ceil ($ile / $size);
	if (ceil ($ile / $size) == floor ($ile / $size) && $navi+$naviend>$stron ) $naviend--;

	for ($i=$navi;$i<=$navi+$naviend;$i++)
	{
   		$n=$i*$size;
		$navistart=$n-$size;
		if ($n==$start+$size)
			$pasek.="<font color=\"red\" class=\"sr_navi_this\"><b>$i</b></font> ";
		else
			$pasek.="[<a style='nawigacja_link' href=$all_link&ile=$ile&start=$navistart>$i</a>] ";
	}

	$pasek.=" ... z <b>$stron</b>";
	if ($ile>$size)
	{
		$nawigacja="
		 <table border=0 cellpadding=0 cellspacing=0>
		 <tr>
		   <td align=left nowrap class=\"sr_navi_prev\">$linkp</td>
		   <td align=left>$pasek</td>
		   <td align=right class=\"sr_navi_next\">$linkn</td>
		 </tr>
 		 </table>";
	}
	return $nawigacja;
}

function addDesc($page,$title,$desc,$servername,$lang,$ver)
{
	global $adodb;
	$title=addslashes($title);
	$desc=addslashes($desc);
	$SQL =" INSERT INTO search_desc (d_page,d_title,d_desc,servername,lang,ver)";
	$SQL.=" VALUES ($page,'$title','$desc','$servername','$lang',$ver)";
	$insert=$adodb->Execute($SQL);
}

function deleteDesc($page,$servername,$lang,$ver)
{
	global $adodb;
	if (strlen($page))
		$war=" d_page=$page AND servername='$servername'";
	else
		$war=" servername='$servername'";
	$SQL ="DELETE FROM search_desc WHERE $war AND lang='$lang' AND ver=$ver";
	$delete=$adodb->Execute($SQL);
}

function getDesc($page,$servername,$lang,$ver)
{
	global $adodb;
	global $C_SEARCH_MINOR_VERSION;

	$verand=$C_SEARCH_MINOR_VERSION ? '':"AND ver=$ver";

	if (strlen($page))
	{
		$SQL =" SELECT d_title,d_desc FROM search_desc 
				WHERE d_page=$page 
				AND servername='$servername' AND lang='$lang' $verand LIMIT 1";
		$select=$adodb->Execute($SQL);
		if ($select->RecordCount())
		{
			parse_str(ado_ExplodeName($select,0));
			$ar[]=$d_title;
			$ar[]=$d_desc;
			return $ar;
		}
		else
			return 0;
	}
	else
	return 0;	
}

//$changes {1|0}, 1-tylko strony ktore sie zmienily, 0-wszystkie strony
function index_page($adodb,$page,$lang,$ver,$KEY,$changes=0)
{
	global $INDEX_PAGE,$PAGE_RESULT;
	global $server;
	global $totalsize,$totalwords,$operator;

	$_page_id=sprintf("%05d",$page);
	$version=$ver;

	$data=getdate(time());
   	$wday=$data["wday"];
	$cond="";
	//jesli jest niedziela to oznacza ze jest indeksacja co tydzien wiec trzeba zobaczyc czy
	//na danej stronie wystapily zmiany. jesli nie to return
	if ($wday==0 || $changes)
	{
		$nd_upd=time()-3600*24*$changes-3600;
		$cond=" AND nd_update>=$nd_upd";
	}

	$query="SELECT id,title,description,keywords,tree FROM webpage 
		WHERE id=$page AND ver $operator $version AND lang='$lang' AND server=$server AND (hidden<>1 OR hidden IS NULL) $cond
		LIMIT 1";
	parse_str(query2url($query));
	// brak zmian to return
	if (!strlen($id))
	{
//		echo "brak stron\n $query";
		return;
	}
	
	$keywords=" $title $description $keywords ";

	
	deleteDesc($page,$KEY,$lang,$ver);
	addDesc($page,$title,$description,$KEY,$lang,$ver);

	$query="SELECT plain,title,pri FROM webtd 
		WHERE page_id=$page AND server=$server  
                      AND ver $operator $version
                      AND lang='$lang'
					  AND (hidden<>1 OR hidden IS NULL)";

	$result=$adodb->Execute($query);
	$count=$result->RecordCount();
    $html=$keywords;
	for ($i=0;$i<$count;$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$plain=trim($plain);
		$title=trim($title);
		$html.="$title $plain ";	
	}
	if (strlen($html))
	{
		$numerek=$_page_id+0;
		$k_href=kameleon_href("","",$numerek);
		deleteIndex("$lang:$version:$_page_id:",$KEY,$lang,$version,$_page_id);
		$ile_slow=addIndex("$lang:$version:$_page_id:$pri:$k_href",$html,$KEY,$lang,$version,$numerek,$tree);
		$totalwords+=$ile_slow;
		//echo "$lang:$version:$_page_id:$pri:$k_href\n";
	}

	return $ile_slow;
}



// do indeksowania strony w czasie zapisu TD lub PAGE
function index_page_now ()
{
	global $server, $KEY, $operator, $SERVER_ID;
	global $adodb,$page,$lang,$ver,$SERVER_NAME;

	$server=$SERVER_ID;
	$KEY=$SERVER_NAME;
	$operator="=";
	index_page($adodb,$page,$lang,$ver,$SERVER_NAME);
}

//
function wordsFromHtml ($html)
{
	//usun entery
	$html=ereg_replace("\n|\r|&nbsp;"," ",$html);
	//usun kod html
	$html=ereg_replace("<[^>]+>"," ",$html);
	//zamien przecinki,kropki,itd na spacje
	$html=ereg_replace("[\.,;:\/\'\"-]"," ",$html);
	//usun wszystkie niepotrzebne znaki oprÛcz tych dopuszczalnych
	//$html=ereg_replace("[^a-z|A-Z|0-9| |_|\"|\.|\,|\n|\/|πúøüÊÍÒÛ≥|•åØè∆ —”£|@|±∂º°¶¨:;()[]!$%*&?]"," ",$html);
	$html=polishtolower($html);

	//teraz usuÒ duplikaty
	$words="";
	$q=explode(" ",$html);	
	for ($j=0;$j<count($q);$j++)
	{
		$slowo=trim($q[$j]);
		if (strlen($slowo) && !$S[$slowo])
		{
			$S[$slowo]=1;
			$words.=" $slowo";
		}
	}
	return $words;	
}
?>
