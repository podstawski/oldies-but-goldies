<style>
*	{font-family: Tahoma,Verdana,Arial; font-size:12px}
</style>

<?
	$action="";

	include_once('../include/transfun.h');
	$KAMELEON_MODE=1;
	include_once("../include/kameleon_href.h");


	if (!strlen($trans[lang]))
	{
		echo label('No language submited');
		exit();
	}
	if (!strlen($trans[ver]))
	{
		echo label('No version submited');
		exit();
	}

	if (!is_array($transusers))
	{
		echo label('No language submited');
		exit();
	}

	$v=$trans[ver];
	$l=$trans[lang];
	$s=$server;

	$ver=$v;
	$lang=$l;
	$SERVER_ID=$s;

	$trans[users]=array_keys($transusers);

	

	$sql="SELECT trans AS tr FROM servers WHERE id=$server";
	parse_str(ado_query2url($sql));
	$_trans=unserialize($tr);
	$_trans[$l]=$trans;
	$strans=serialize($_trans);
	$sql="UPDATE servers SET trans='$strans' WHERE id=$server";
	$adodb->execute($sql);

	echo label('Copying').' <span id="cp">0</span> %<br>';

	$co=array(
		'webpage:title','webpage:title_short','webpage:keywords','webpage:description',
		'weblink:alt','weblink:alt_title',
		'webtd:title','webtd:plain'
		);

	
	$sql="DELETE FROM webtrans WHERE wt_server=$s AND wt_lang='$l'";
	$adodb->execute($sql);
	
	for($i=0;$i<count($co);$i++)
	{
		$p=explode(':',$co[$i]);
		$c='id';
		if ($p[0]=='weblink') $c='menu_id';
		if ($p[0]=='webtd') $c='page_id';
		
		trans_ins($p[0],$p[1],$s,$l,$v,$c);
		trans_prc(round(100*$i/count($co)),'cp');
	}
	trans_prc(100,'cp');


	echo label('Path titles').' <span id="tit">0</span> %<br>';
	$sql="SELECT wt_table,wt_sid,wt_table_sid FROM webtrans WHERE wt_server=$server AND wt_lang='$l'";
	$res=$adodb->Execute($sql);
	$rc=$res->RecordCount();
	for ($i=0;$i<$rc;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$pole='id';
		if ($wt_table=='weblink') $pole='page_target';
		if ($wt_table=='webtd') $pole='page_id';

		$sql="SELECT $pole AS page FROM $wt_table WHERE sid=$wt_table_sid";
		parse_str(ado_query2url($sql));

		$path=addslashes(stripslashes(trans_unhtml(kameleon_path($page,' - '))));
		$sql="UPDATE webtrans SET wt_path='$path' WHERE wt_sid=$wt_sid";
		$adodb->execute($sql);

	

		trans_prc(round(100*$i/$rc),'tit');
	}
	trans_prc(100,'tit');


	echo label('Table detection').' <span id="td">0</span> %<br>';
	$rand=rand(1000,9000);
	$rand5=rand(10000,90000);

	
	$sql="SELECT wt_o_html,wt_sid FROM webtrans WHERE wt_server=$server AND wt_o_html ~* '<table' AND wt_lang='$l'";
	$res=$adodb->Execute($sql);
	$rc=$res->RecordCount();
	for ($i=0;$i<$rc;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$html=stripslashes($wt_o_html);
		$tds=array();
		$global_pos=0;

		
		while (strlen(strpos($html,"$rand"))) $rand=rand(1000,9000);
		while (strlen(strpos($html,"$rand5"))) $rand5=rand(10000,90000);

		$h=strtolower($html);
		$h=str_replace('<td>',$rand,$h);
		$h=str_replace('<td ',$rand,$h);
		$h=str_replace('<th>',$rand,$h);
		$h=str_replace('<th ',$rand,$h);
		
		while(1)
		{
			$pos=strpos($h,"$rand");
		
			if (!strlen($pos)) break; 
			$tds[]=($global_pos+$pos);

			$global_pos+=$pos+4;
			$h=substr($h,$pos+4);

		}
	
		
		$h=strtolower($html);
		$h=str_replace('</td>',$rand5,$h);
		$h=str_replace('</th>',$rand5,$h);

		$replace_tab=array();
		
		for ($t=0; $t<count($tds) ; $t++ )
		{
			$pos=$tds[$t];
			$end=$pos + strpos( substr($h,$pos) ,"$rand5");
			$tpos=strpos(substr($h,$pos,$end-$pos),'<table');
			if (strlen($tpos)) $end=$pos+$tpos;

			$pos+=strpos(substr($html,$pos),'>')+1;

			$inside_html=substr($h,$pos,$end-$pos);
			$inside_unhtml=trans_unhtml($inside_html);

			if (!trans_requires_trans($inside_unhtml)) continue;

			$sid=trans_ins_sub($s,$l,$inside_html,$wt_sid,'table-td');
			if ($sid) $replace_tab[]=array($sid, $pos, $end);

			//echo htmlspecialchars(substr($h,$tds[$t],5))." (".$tds[$t].")<br>";
		}

		$newhtml='';

		$pos=0;
		foreach ($replace_tab AS $rt)
		{
			$newhtml.=substr($html,$pos,$rt[1]-$pos);
			$newhtml.=sprintf($KAMELEON_WEBTRANS_SID_TEMPLATE,$rt[0]);
			$pos=$rt[2];
		}
		if ($pos) $newhtml.=substr($html,$pos);

		if (strlen($newhtml))
		{
			$html=addslashes($newhtml);
			$sql="UPDATE webtrans SET wt_o_html='$html' WHERE wt_sid=$wt_sid";
			$adodb->execute($sql);
		}


		trans_prc(round(100*$i/$rc),'td');
	}
	trans_prc(100,'td');


	echo label('Tag alt,title,value detection').' <span id="tag">0</span> %<br>';

	
	$sql="SELECT wt_o_html,wt_sid FROM webtrans WHERE wt_server=$server AND wt_o_html ~ '<' AND wt_lang='$l'";
	$res=$adodb->Execute($sql);
	$rc=$res->RecordCount();

	for ($i=0;$i<$rc;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$html=stripslashes($wt_o_html);


		$h=$html;
		while ($tag=eregi("<([a-z]+) [^>]*(value|alt|title)=\"([^\"]+)\"[^>]*>",$h,$regs))
		{
			$h=str_replace($regs[0],'',$h);

			if (!trans_requires_trans($regs[3])) continue;
			
			$tagname=$regs[1];
			$argname=$regs[2];
			$value=$regs[3];

			$sid=trans_ins_sub($s,$l,$regs[3],$wt_sid,"$tagname-$argname");

			$kameleon_trans=sprintf($KAMELEON_WEBTRANS_SID_TEMPLATE,$sid);
			$html=ereg_replace("(<${tagname}[^>]*)${value}([^>]*>)","\\1$kameleon_trans\\2",$html);
		}
		
		$html=addslashes($html);
		$sql="UPDATE webtrans SET wt_o_html='$html' WHERE wt_sid=$wt_sid";
		$adodb->execute($sql);


		trans_prc(round(100*$i/$rc),'tag');
	}

	trans_prc(100,'tag');


	echo label('Extracting plain text').' <span id="plain">0</span> %<br>';

	$sql="SELECT wt_o_html,wt_sid FROM webtrans WHERE wt_server=$server AND wt_lang='$l'";
	$res=$adodb->Execute($sql);
	$rc=$res->RecordCount();

	for ($i=0;$i<$rc;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$query="SELECT count(*) AS dzieci FROM  webtrans WHERE wt_parent=$wt_sid";
		parse_str(ado_query2url($query));

		$plain=trans_unhtml($wt_o_html);

		if (!trans_requires_trans($plain) && !$dzieci)
		{
			$sql="DELETE FROM webtrans WHERE wt_sid=$wt_sid";
			$adodb->execute($sql);
			continue;
		}

		$plain=addslashes($plain);
		$sql="UPDATE webtrans SET wt_o_plain='$plain' WHERE wt_sid=$wt_sid";
		$adodb->execute($sql);

		trans_prc(round(100*$i/$rc),'plain');
	}

	trans_prc(100,'plain');


	echo label('Searching for similars').' <span id="sim">0</span> %<br>';


	$sql="SELECT wt_o_plain,wt_sid FROM webtrans WHERE wt_server=$server AND wt_o_plain<>'' AND wt_lang='$l'";
	$res=$adodb->Execute($sql);
	$rc=$res->RecordCount();

	for ($i=0;$i<$rc;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$plain=addslashes(stripslashes($wt_o_plain));
		$similar=array();

		$sql="SELECT wt_sid AS sim_sid FROM webtrans WHERE wt_server=$server AND wt_lang='$l' AND wt_sid<>$wt_sid AND wt_o_plain='$plain'";
		$res2=$adodb->Execute($sql);
		$rc2=$res2->RecordCount();

		for ($sim=0;$sim<$rc2;$sim++)
		{
			parse_str(ado_ExplodeName($res2,$sim));
			$similar[]=$sim_sid;
		}
	
		if (count($similar))
		{
			$wt_similar=implode(',',$similar);
			$sql="UPDATE webtrans SET wt_similar='$wt_similar' WHERE wt_sid=$wt_sid";
			$adodb->execute($sql);

		}
		

		trans_prc(round(100*$i/$rc),'sim');
	}

	trans_prc(100,'sim');



	$sql="SELECT count(*) AS c FROM webtrans WHERE wt_server=$s AND wt_lang='$l'";
	parse_str(ado_query2url($sql));

	echo '<b>'.label('Total translation items').": $c<b><br><br>
	
	<input type=\"button\" value=\" OK \" onClick=\"top.location.href='servers.php'\">
	";
	$adodb->commit();
	exit();
?>