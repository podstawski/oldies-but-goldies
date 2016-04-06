<?
	include_once('include/transfun.h');
	include_once('include/utf8.h');

	$moreand="";

	$page="";

	if ($trans_ch_ep) $moreand.=" AND wt_o_plain<>'' AND wt_o_plain IS NOT NULL";

	if (is_array($trans_ch_ex) && count($trans_ch_ex)) $moreand.=" AND wt_table NOT IN ('".implode("','",array_keys($trans_ch_ex))."')";

	if (strlen($trans_search))
	{
		$s=addslashes(utf82lang(stripslashes($trans_search),$trans_srclang));
		$moreand.=" AND wt_o_plain~*'$s'";
	}

	if (strlen($trans_context))
	{
		$s=addslashes(utf82lang(stripslashes($trans_context),$trans_srclang));
		$moreand.=" AND wt_context='$s'";
	}
	

	$query="UPDATE webtrans SET wt_t_plain=wt_o_plain,
			wt_translation=".$adodb->now.",
			wt_translator='".$kameleon->user[username]."'
			WHERE wt_server=$SERVER_ID AND wt_lang='$lang' 
			AND wt_translation IS NULL $moreand";

	$adodb->execute($query);

?>