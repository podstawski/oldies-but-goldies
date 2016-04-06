<?

	$KAMELEON_WEBTRANS_SID_TEMPLATE="kameleon_webtrans(%09d)";

	function trans_prc($prc,$obj)
	{
		echo "
				<script>
					document.getElementById('$obj').innerHTML='$prc';
				</script>
			";
		flush();
		ob_flush();
	}

	function trans_ins($t,$f,$s,$l,$v,$context)
	{
		global $adodb;

		$sql="INSERT INTO webtrans
				(wt_server,wt_lang,wt_table,wt_table_sid,wt_table_field,wt_o_html,wt_context)
				SELECT $s,'$l','$t',sid,'$f',$f,$context 
				FROM $t
				WHERE server=$s AND lang='$l' AND ver=$v AND $f<>''";
		$adodb->execute($sql);
	}

	function trans_ins_sub($s,$l,$html,$parent,$obj='')
	{
		global $adodb;

		$h=addslashes(stripslashes($html));
		$obj=substr($obj,0,16);

		$sql="INSERT INTO webtrans
				(wt_server,wt_lang,wt_o_html,wt_parent,wt_table,wt_context,wt_path)
				SELECT $s,'$l','$h',wt_sid,'$obj',wt_context,wt_path
				FROM webtrans
				WHERE wt_sid=$parent
				;";
		$adodb->execute($sql);

		$sql="SELECT max(wt_sid) AS wt_sid FROM webtrans WHERE wt_server=$s";

		parse_str(ado_query2url($sql));

		return $wt_sid;
	}


	function trans_unhtml($html)
	{
		$html=ereg_replace("[\n\r\t]+"," ",$html);
		$html=str_replace('&nbsp;',' ',$html);

		$html=ereg_replace("kameleon_webtrans\([0-9]+\)","",$html);
		

		$html=eregi_replace("<br[^>]*>","\n",$html);
		$html=eregi_replace("</p>","\n\n",$html);
		$html=eregi_replace("</div>","\n\n",$html);
		$html=eregi_replace("</h[0-9]>","\n\n",$html);
		$html=eregi_replace("</li>","\n\n",$html);
		$html=eregi_replace("</option>","\n",$html);
		$html=eregi_replace("</textarea>","\n",$html);

		$html=eregi_replace("<[^>]+>","",$html);

		$html=ereg_replace("[ ]+"," ",$html);
		$html=ereg_replace("\n[\n]+","\n\n",$html);

		return trim($html);
	}


	function trans_requires_trans($plain)
	{
		if (!strlen($plain)) return false;
		if (eregi("[a-z]+",$plain)) return true;
		return false;
	}


	function trans_decription($wt_sid)
	{
		$query="SELECT * FROM webtrans WHERE wt_sid=$wt_sid";
		parse_str(ado_query2url($query));

		if ($wt_parent)
		{
			$wynik=array("<a href='index.php?trans_goto=$wt_sid' class=k_a>".label('Subphrase')." $wt_table</a>");
			return array_merge(trans_decription($wt_parent),$wynik);
		}


		$wynik="<a href='index.php?trans_goto=$wt_sid' class=k_a>".label("$wt_table $wt_table_field")."</a>";

		if ($wt_table=='weblink')
		{
			$query="SELECT * FROM weblink WHERE sid=$wt_table_sid";
			parse_str(ado_query2url($query));
	

			$query="SELECT page_id FROM webtd WHERE menu_id=$menu_id AND ver=$ver AND lang='$lang' AND server=$server LIMIT 1";
			parse_str(ado_query2url($query));
		
			if ($page_id<0) $wynik.=' ['.label('Header/footer menu').']';
			else $wynik.=' <a class=k_a href="'.kameleon_href('','',$page_target).'">['.label('Menu at page').' '.$page_target.']</a>';
			
		}

		if ($wt_table=='webtd')
		{
			$query="SELECT * FROM webtd WHERE sid=$wt_table_sid";
			parse_str(ado_query2url($query));

			if ($page_id<0) $wynik.=' ['.label('Header/footer module').']';
			else $wynik.=' <a class=k_a href="'.kameleon_href('','',$page_id).'">['.label('Module at page').' '.$page_id.']</a>';
		}


		return array($wynik);
	}


	function kameleon_webtrans($wt_sid,$translated=false)
	{
		$co=$translated?'wt_t_html':'wt_o_html';

		$sql="SELECT $co AS wynik FROM webtrans WHERE wt_sid=$wt_sid";
		parse_str(ado_query2url($sql));

		while (ereg("kameleon_webtrans\(([0-9]+)\)",$wynik,$regs))
		{
			$wynik=str_replace($regs[0],kameleon_webtrans($regs[1]+0,$translated),$wynik);
		}
		
		return $wynik;
	}
?>