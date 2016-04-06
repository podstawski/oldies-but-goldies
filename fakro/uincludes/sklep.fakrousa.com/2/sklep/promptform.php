<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?echo $_REQUEST[chs]?>">
	<title><?echo $_REQUEST[msg]?>   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  </title>
	<style>
		* {font-family:Tahoma}
		form {padding: 3px}
	</style>

	<script language="JavaScript">
		function zapisz(f)
		{
			window.dialogArguments.cart_prompt_quantity_<?echo $_REQUEST[sid]?>=f.ilosc.value;
			window.close();
			return false;

		}

		function zamknij()
		{
			window.close();
		}

		function foc()
		{
			if (document.prform==null)
			{
				setTimeout(foc,50);
				return;
			}
			document.prform.ilosc.focus();
			document.prform.ilosc.select();
		}
	</script>

</head>
<body>

<form onsubmit="return zapisz(this)" name="prform">
<?echo $_REQUEST[msg]?> 
<input type="text" style="width: 50px" value="<?echo $_REQUEST[def]?>" name="ilosc">
<input type="submit" value=" ok ">
<input type="button" value="<?echo $_REQUEST[cancel]?>" onclick="zamknij()">



</form>

</body>

	<script language="JavaScript">
		
		setTimeout(foc,50);

	</script>

</html>