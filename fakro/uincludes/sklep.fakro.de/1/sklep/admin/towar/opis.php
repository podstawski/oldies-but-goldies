<script language="JScript">
	var opisHtml='';
	<?
		$LIST[id]=$CIACHO[admin_to_id];

		$query="SELECT to_opis_d_$lang AS opis,to_att FROM towar WHERE to_id=".$LIST[id];
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

<form method="post" id="opisSave" action="<?echo $self?>" 
	style="margin-bottom:-25px; position:relative; top: -30px">
<input type="hidden" name="action" value="ZapiszTowarOpis">
<input type="hidden" name="form[id]" value="<?echo $LIST[id]?>">
<textarea id="HTMLContent" name="form[to_opis_d_<?echo $lang?>]" style="visibility:hidden"></textarea>


<div>
Plik z opisem: <INPUT TYPE="text" 
				id=att NAME="form[to_att]" value="<?echo $to_att ?>" style="width:390px">
				<img src=<?echo $SKLEP_IMAGES?>/sb/i_image_n.gif align=absmiddle 
					onClick="galeria_input='att';kartoteka_popup('<?echo "$next$next_char" ?>form[img]='+document.all[galeria_input].value,'galeria')" style="cursor:hand;" 
					onmouseover="this.src='<?echo $SKLEP_IMAGES?>/sb/i_image_a.gif'" 
					onmouseout="this.src='<?echo $SKLEP_IMAGES?>/sb/i_image_n.gif'" 
					border=0 alt='Wstaw lub modyfikuj plik' width=23 height=22>			
</div>
</form>


<div id="eweContainer" unselectable="on"></div>

<script language="JScript" src="<?echo $SKLEP_INCLUDE_PATH?>/js/eweload.js" defer="defer"></script>

<script language="JScript">

	function sampleSave()
	{
		if (ViewCurrent == 2) toggleView();
		document.forms['opisSave'].HTMLContent.value = cleanup(document.all.ewe.innerHTML);
		document.forms['opisSave'].submit();
  }
</script>

