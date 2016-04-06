<script language="javascript">
function showModFun()
{
}
</script>
<?
	$a=xml2obj($costxt);
	$xml=$a->xml;
?>
<table cellpadding=4>
<tr class=k_form>
	<td colspan=3 class=k_formtitle><?echo label("Use for search engine")?>:</td>
</tr>

<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_plain_yes]" value=1 <? if ($xml->page_plain_yes) echo "checked"?>>
		<? echo label("Plain") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_plain_label?>" name="SEARCH[page_plain_label]" class="k_input"></td>
	<td>&nbsp</td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_keywords_yes]" value=1 <? if ($xml->page_keywords_yes) echo "checked"?>>
		<? echo label("Keywords") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_keywords_label?>" name="SEARCH[page_keywords_label]" class="k_input"></td>
	<td>&nbsp</td>

</tr>




<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_d_create_yes]" value=1 <? if ($xml->page_d_create_yes) echo "checked"?>>
		<? echo label("Creation date") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_d_create_label?>" name="SEARCH[page_d_create_label]" class="k_input"></td>
	<td>&nbsp</td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_d_update_yes]" value=1 <? if ($xml->page_d_update_yes) echo "checked"?>>
		<? echo label("Modification date") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_d_update_label?>" name="SEARCH[page_d_update_label]" class="k_input"></td>
	<td>&nbsp</td>

</tr>


<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_pagekey_yes]" value=1 <? if ($xml->page_pagekey_yes) echo "checked"?>>
		<? echo label("Page key") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_pagekey_label?>" name="SEARCH[page_pagekey_label]" class="k_input"></td>
	<td><input type="checkbox" name="SEARCH[page_pagekey_select]" value=1 <? if ($xml->page_pagekey_select) echo "checked"?>>
		<? echo label("only predefined values") ?></td>

</tr>

<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_title_yes]" value=1 <? if ($xml->page_title_yes) echo "checked"?>>
		<? echo label("Title") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_title_label?>" name="SEARCH[page_title_label]" class="k_input"></td>
	<td><input type="checkbox" name="SEARCH[page_title_select]" value=1 <? if ($xml->page_title_select) echo "checked"?>>
		<? echo label("only predefined values") ?></td>
</tr>

<tr class=k_form>
	<td><input type="checkbox" name="SEARCH[page_description_yes]" value=1 <? if ($xml->page_description_yes) echo "checked"?>>
		<? echo label("Description") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->page_description_label?>" name="SEARCH[page_description_label]" class="k_input"></td>
	<td><input type="checkbox" name="SEARCH[page_description_select]" value=1 <? if ($xml->page_description_select) echo "checked"?>>
		<? echo label("only predefined values") ?></td>

</tr>

<tr class=k_form>
	<td><? echo label("Search button") ?></td>
	<td><input type="text" size=50 value="<? echo $xml->search_button?>" name="SEARCH[search_button]" class="k_input"></td>

	<td><input type="checkbox" name="SEARCH[page_this]" value=1 <? if ($xml->page_this) echo "checked"?>>
		<? echo label("include this page in results") ?></td>
</tr>


</table>

