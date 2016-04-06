<?
	$GENERATE_ONLY_WEBPAGE_OBJECT=1;


	$query="UPDATE webtrans SET 
			wt_t_html=wt_t_plain,
			wt_verification=".$adodb->now.",
			wt_verificator='".$kameleon->user[username]."'
			WHERE wt_translation>0 
			AND wt_server=$SERVER_ID
			AND wt_lang='$lang'
			AND wt_verification IS NULL
			AND wt_o_html !~ '<'
			AND wt_t_plain <> ''";

	$adodb->execute($query);


?>
<script>
	top.location.href='index.php?trans_goto=<?echo $trans_goto?>';
</script>