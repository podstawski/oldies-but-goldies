<?
	parse_str($costxt);
?>


<form method=post action="<?echo $next?>" name="searchfrm">
<input type=hidden name=api_post value=1>
<input type="text" class="api_search_input" name="api_query" size="" value="<? echo $ctx_inputword; ?>" onFocus="if (this.value == '<? echo $ctx_inputword; ?>') this.value = ''" onBlur="if (this.value == '') this.value = '<? echo $ctx_inputword; ?>'">
<p onClick="document.searchfrm.submit()" style="cursor:pointer;font-weight: bold;"><? echo $ctx_nextname?>&nbsp;&nbsp;<img src="<?echo $IMAGES;?>/h1.gif" align="absmiddle"></p>
