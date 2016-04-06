<?
parse_str($costxt);
?>

<div align="right">
<form method="post" id="searchfrm" name="searchfrm" action="<?echo $next?>">
<fieldset>
	<input type=hidden name=api_post value=1>
	<input type="text" id="search_input" name="api_query" size="" value="<? echo $ctx_inputword; ?>" onFocus="if (this.value == '<? echo $ctx_inputword; ?>') this.value = ''" onBlur="if (this.value == '') this.value = '<? echo $ctx_inputword; ?>'">
	<input type="submit" id="search_submit" value="<? echo $ctx_nextname; ?>">
</fieldset>
</form>
</div>