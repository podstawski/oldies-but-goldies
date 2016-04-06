<div align="center">
<table width="100%">
<tr>
	<td colspan="2">
	Zamówienie zostało złożone i oczekuje na akceptację.
	Prosimy czekać na potwierdzenie pozytywnej weryfikacji zamówienia.
	Zostanie ono wysłane pocztą email.
	W przypadku kontaktu w sprawie złożonego zamówienia	z Serwisem Fakro prosimy o podanie następującego numeru zamówienia:
	<strong><? echo $_SESSION['done']['nr_ewidencyjny_klient']; ?></strong>.
	<br><br>
	</td>
</tr>
<tr>
<form action="<? echo $platnosci_page_1; ?>" method="post">
	<td align="center">
	<input class="button" type="submit" size="22" value="Płatność elektroniczna" />
	<input type="hidden" name="order_number" value="<? echo $_SESSION['done']['nr_ewidencyjny_klient']; ?>" />
	<input type="hidden" name="za_id" value="<? echo $_SESSION['done']['nr_ewidencyjny_klient']; ?>" />
	<input type="hidden" name="brutto" value="<? echo $_SESSION['done']['suma'] ;?>" />
	</td>
</form>
<form action="<? echo $platnosci_page_2; ?>" method="post">
	<td align="center">
	<input class="button" type="submit" size="22" value="Płatność przy odbiorze" />
	<input type="hidden" name="order_number" value="<? echo $_SESSION['done']['nr_ewidencyjny_klient']; ?>" />
	</td>
</tr>
</form>
</table>

</div>