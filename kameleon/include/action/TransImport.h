<?
	include_once('include/transfun.h');
	include_once('include/utf8.h');

	$plik = $_FILES['transfile']['tmp_name'];
	if (!file_exists($plik)) return;

	$word=read_file($plik);

	$page="";

	while (strlen($pos=strpos(strtolower($word),'<td')))
	{

		$word=substr($word,$pos);
		$endtag=strpos($word,'>')+1;

		$td=substr($word,0,$endtag);
		$word=substr($word,$endtag);


		$id=ereg_replace(".+ id=([^ >]+).*>","\\1",$td);

		if ($id==$td) continue;
		
		if ($id[0]=="'" || $id[0]=='"') $id=substr($id,1,strlen($id)-2);
		$id=explode('-',$id);

		$md5=$id[0];
		$wt_sid=$id[1]+0;

		$wt_o_plain='';
		$query="SELECT wt_o_plain,wt_similar FROM webtrans WHERE wt_sid=$wt_sid";
		parse_str(ado_query2url($query));
		$md5o=md5(stripslashes($wt_o_plain));

		if ($md5o!=$md5) continue;

		$endtag=strpos(strtolower($word),'</td>');


		$intag=trim(trans_unhtml(substr($word,0,$endtag)));
		$word=substr($word,$endtag);

		if (!strlen($intag)) continue;
		
		$wt_t_plain=addslashes(utf82lang($intag));

		if ($ch_saveall && strlen($wt_similar)) 
		{
			$wt_sid.=",$wt_similar";
		}

		$query="UPDATE webtrans SET 
				wt_t_plain='$wt_t_plain',
				wt_translation=".$adodb->now.",
				wt_translator='".$kameleon->user[username]."'
				WHERE wt_sid IN ($wt_sid) AND wt_server=$SERVER_ID AND wt_verification IS NULL";

		//$adodb->debug=1;	
		$adodb->execute($query);

	}

?>