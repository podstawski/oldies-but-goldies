<script>
	OPIS_LINK = '<? echo $more ?>';
	function opisProduktuClick(to_id)
	{
		document.opisProduktuClickForm.opis_to_id.value=to_id;
		document.opisProduktuClickForm.submit();
	}

</script>
<form name="opisProduktuClickForm" method="<?echo $KAMELEON_MODE?"POST":"GET";?>" action="<? echo $next ?>">
<input type="hidden" name="list[to_id]" id="opis_to_id">
</form>
