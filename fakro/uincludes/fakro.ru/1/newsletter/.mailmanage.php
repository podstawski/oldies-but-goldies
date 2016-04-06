<?
	push($adodb);
	$adodb=$kameleon_adodb;

	global $multi_mail,$mail_group, $addpage,$imgsrv;


	if ($addpage)
	{
		include($INCLUDE_PATH.'/'.dirname($WEBTD->html)."/.dodajNL.php");
		return;
	}

	if (strlen($imgsrv))
	{
		$query="UPDATE webtd SET costxt='$imgsrv' WHERE sid=".$WEBTD->sid;
		$adodb->execute($query);
		$costxt=$imgsrv;
	}


	function _newsletter_id($email)
	{
		$email=trim(strtolower($email));

		$query="SELECT c_id FROM crm_customer WHERE lower(c_email)='$email'";
		parse_str(ado_query2url($query));
	
		return $c_id+0;
	}


	$mail_group=trim($mail_group);
	if (strlen($mail_group))
		foreach (explode("\n",$multi_mail) AS $mail)
		{
			$mail=trim(strtolower($mail));
			if (!strlen($mail)) continue;
			$k_id=_newsletter_id($mail);
			if (!$k_id) 
				$query="INSERT INTO crm_customer (c_email,c_email2,c_server) VALUES ('$mail','$mail_group',$SERVER_ID)";
			else
				$query="UPDATE klienci SET c_email='$mail_group' WHERE c_id=$k_id";

			$adodb->execute($query);
			//echo "$query <br>";
		}


	$adodb=pop();
?>

<hr size=1>

<form action="<?echo $self?>" method="post" name="mailing">


<table class="tf" width="100%">
<thead><tr><td>Adres serwisu publicznego</td></tr></thead>

<tbody>
<tr><td><input type="text" name="imgsrv" value="<?echo $costxt?>" style="width:250px"></td></tr>
</tbody>

<tfoot>
<tr><td colspan="2"><input type="submit" value="Aktualizuj"></td></tr>
</tfoot>

</table>
</form>

<hr size=1>

<table class="tf" width="100%">
<thead><tr><td>Zarzadzanie NewsLetterem</td></tr></thead>
<tbody>
<tr><td>Aby dodaц nowБ wiadomoЖц do wysyГki naciЖnij poniПszy przycisk</td></tr>
</tbody>
<tfoot>
<tr><td>
<form action="<?echo $self?>" method="post" name="addpage">
<INPUT TYPE="hidden" name="addpage" value="1">
<INPUT TYPE="submit" value="Dodaj newsletter">
</form>
</td></tr>
</tfoot>
</table>

<hr size=1>
<form action="<?echo $self?>" method="post" name="mailing">
<table class="tf" width="100%">
<col class="c1"><col class="cn">
<thead>
	<tr><td colspan="2">Zarzadzanie grupami NewsLettera</td></tr></thead>
<tbody>
<tr><td colspan="2">Aby dodaц grupъ wpisz jej nazwъ w polu "grupa" i naciЖnij przycisk "Aktualizuj".<br>
	JeПeli chcesz dodaц uПytkownikѓw do istniejБcej grupy wpisz jej nazwъ do pola grupa a z polu "Adresy uПytkownikѓw" wpisz listъ nowych adresѓw, kaПdy adres od nowej linii.</td></tr>
<tr><td align="right">grupa:</td><td><input name="mail_group" value="<?echo $mail_group?>"> </td></tr>
<tr><td colspan="2">
	Adresy uПytkownikѓw:<br>
	<textarea name="multi_mail" style="height:100px;width:100%"><?echo $multi_mail?></textarea>
	</td></tr>
</tbody>
<tfoot>
<tr><td colspan="2"><input type="submit" value="Aktualizuj"></td></tr>
</tfoot>
</table>
</form>






