<script language="javascript">
function showModFun()
{
}
</script>
<?
	$a=xml2obj($costxt);
	$xml=$a->xml;

	if (!strlen($xml->order_email))
	{
		$xml->order_email=$KAMELEON[email];
	}

?>
<table cellpadding=4>
<tr class=k_form>
	<td colspan=2 class=k_formtitle>Polcard Easy-Entry:</td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'amount'</td>
	<td><input type="text" size=50 value="<? echo $xml->amount?>" name="POLCARD[amount]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'order_id'</td>
	<td><input type="text" size=50 value="<? echo $xml->order_id?>" name="POLCARD[order_id]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'email'</td>
	<td><input type="text" size=50 value="<? echo $xml->email?>" name="POLCARD[email]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'street'</td>
	<td><input type="text" size=50 value="<? echo $xml->street?>" name="POLCARD[street]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'phone'</td>
	<td><input type="text" size=50 value="<? echo $xml->phone?>" name="POLCARD[phone]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'postcode'</td>
	<td><input type="text" size=50 value="<? echo $xml->postcode?>" name="POLCARD[postcode]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'city'</td>
	<td><input type="text" size=50 value="<? echo $xml->city?>" name="POLCARD[city]" class="k_input"></td>
</tr>


<tr class=k_form>
	<td align="right"><?echo label("Field label")?> 'country'</td>
	<td><input type="text" size=50 value="<? echo $xml->country?>" name="POLCARD[country]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Submit data")?> </td>
	<td><input type="text" size=50 value="<? echo $xml->submit_data?>" name="POLCARD[submit_data]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right"><?echo label("Submit payment")?> </td>
	<td><input type="text" size=50 value="<? echo $xml->submit_payment?>" name="POLCARD[submit_payment]" class="k_input"></td>
</tr>


<tr class=k_form>
	<td align="right">POSID</td>
	<td><input type="text" size=10 value="<? echo $xml->pos_id?>" name="POLCARD[pos_id]" id='pos_id' class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right">E-MAIL</td>
	<td><input type="text" size=40 value="<? echo $xml->order_email?>" name="POLCARD[order_email]" class="k_input"></td>
</tr>

<tr class=k_form>
	<td align="right">TEST</td>
	<td><input type="checkbox" value=1 <? if ($xml->test) echo "checked" ?> name="POLCARD[test]" class="k_input"></td>
</tr>

<tr class=k_form> 
	<td align="right">Polcard</td>
	<td>
			<input type=password name='_polcard_password' size=20 class=k_input
				onBlur="polcard<?echo $sid?>(this.value)">
	</td>
</tr>

</table>

<script>
	function polcard<?echo $sid?>(pass)
	{
		if (!pass.length) return;

		a=open("empty.php","polcard",
				"toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,\
				 resizable=1,width=620,height=500");

		txt='<FORM ACTION="https://post.polcard.com.pl/cgi-bin/sprzedaz/index.pl" name=polcard method="post" name="polcard">';
		txt+='<input type=hidden name="posid" size=20 value="'+document.all['pos_id'].value+'">';
		txt+='<input type=hidden name="password" size=20 value="'+pass+'">';
		txt+='</FORM><scr'+'ipt>document.polcard.submit()<\/script>';

		a.document.write(txt);
	}
</script>