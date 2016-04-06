<script language="JScript">
	var opisHtml='';
	<?
		$LIST[id]=$CIACHO[admin_ka_id];

		$query="SELECT ka_opis_d_$lang AS opis FROM kategorie WHERE ka_id=".$LIST[id];
		parse_str(ado_query2url($query));
		$opis=ereg_replace("[\r\n]+","\n",$opis);
		$opis=explode("\n",$opis);

		for ($i=0;$i<count($opis);$i++)
		{
			$o=addslashes(stripslashes($opis[$i]));
			echo "	opisHtml+='$o';\n";
		}

	?>

    function eweLoad()
	{
		var ewe = new EWE('<?echo $SKLEP_INCLUDE_PATH; ?>/ewe/source/ewe_langPL.xml');

		ewe.load(document.all.eweContainer,opisHtml);
		//document.all.eweContainer.parentNode.style.overflow = 'hidden';
	}
    var editorPath = '<?echo $SKLEP_INCLUDE_PATH; ?>/ewe/';
</script>
<script language="JScript" src="<?echo $SKLEP_INCLUDE_PATH; ?>/ewe/source/ewe.js"></script>


<div id="eweContainer" unselectable="on"></div>

<script language="JScript">

	function sampleSave()
	{
		if (ViewCurrent == 2) toggleView();
		document.forms['opisSave'].HTMLContent.value = cleanup(document.all.ewe.innerHTML);
		document.forms['opisSave'].submit();
  }
</script>

<script language="JScript" src="<?echo $SKLEP_INCLUDE_PATH?>/js/eweload.js" defer="defer"></script>

<form method="post" id="opisSave" action="<?echo $self?>">
<input type="hidden" name="action" value="ZapiszKategoriaOpis">
<input type="hidden" name="form[id]" value="<?echo $LIST[id]?>">
<textarea id="HTMLContent" name="form[ka_opis_d_<?echo $lang?>]" style="visibility:hidden"></textarea>
</form>
