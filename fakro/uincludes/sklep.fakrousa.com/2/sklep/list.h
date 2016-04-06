<?
	$METHOD=$KAMELEON_MODE?"POST":"GET";
	// get jest lepsze, bo nie ma tych odswiez ...
?>
<form name="list_sort_form" method="<?echo $METHOD?>" action="<?echo $self?>">
<input type="hidden" name="list[sort_f]" id="s_field" value="<?echo $LIST[sort_f]?>">
<input type="hidden" name="list[sort_d]" id="s_dir" value="<?echo $LIST[sort_d]?>">
<input type="hidden" name="list[ile]" id="s_ile" value="<?echo $LIST[ile]?>">
<input type="hidden" name="list[start]" id="s_start" value="<?echo $LIST[start]?>">
<input type="hidden" name="list[szukaj]" id="s_szukaj" value="<?echo $LIST[szukaj]?>">
<input type="hidden" name="action" id="s_action" value="">
<input type="hidden" name="list[id]" id="s_id" value="<?echo $LIST[id]?>">
<input type="hidden" name="list[_table]" id="s_table" value="<?echo $LIST[_table]?>">
<input type="hidden" name="oddzial_id" value="<?echo $oddzial_id?>">
</form>

<form name="list_fwd_form" method="<?echo $METHOD?>" action="<?echo $next?>">
<input type="hidden" name="list[sort_f]" id="s_field" value="<?echo $LIST[sort_f]?>">
<input type="hidden" name="list[sort_d]" id="s_dir" value="<?echo $LIST[sort_d]?>">
<input type="hidden" name="list[ile]" id="s_ile" value="<?echo $LIST[ile]?>">
<input type="hidden" name="list[start]" id="s_start" value="<?echo $LIST[start]?>">
<input type="hidden" name="list[szukaj]" id="s_szukaj" value="<?echo $LIST[szukaj]?>">
<input type="hidden" name="action" id="s_action" value="">
<input type="hidden" name="list[id]" id="s_id" value="<?echo $LIST[id]?>">
<input type="hidden" name="oddzial_id" value="<?echo $oddzial_id?>">
</form>
	
