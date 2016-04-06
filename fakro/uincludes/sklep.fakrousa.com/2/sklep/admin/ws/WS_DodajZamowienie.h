
<form method="post" action="<?echo $self?>">
	<input type="button" value="Przes³anie wszystkich nowych zamówieñ" class="addbut"
		onclick="synchronizuj(0)">
</form>

<span id="syncSpan"></span>

<script id="syncScript" src="" language="JavaScript"></script>
<script id="syncScript" src="<? echo $SKLEP_INCLUDE_PATH ?>/js/statusbar.js" language="JavaScript"></script>

<script language="JavaScript">

<?
	include ($SKLEP_INCLUDE_PATH."/raporty/daty.php");

	$query="SELECT za_id FROM zamowienia WHERE za_status=0 AND za_data >= $od AND za_data <= $do";
	if ($CIACHO[kontrahent_id]) $query.=" AND za_su_id=".$CIACHO[kontrahent_id];
	$res=$projdb->Execute($query);

	echo "	var ZAM_COUNT=".$res->RecordCount().";\n";
	echo "	ZAMY = new Array(ZAM_COUNT);\n";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

		echo "	ZAMY[$i]=$za_id;\n";
	}


?>
	var _script=getObject('syncScript');
	var href='';

	function action_execute()
	{
		//return;
		_script.src=href;
	}

	function synchronizuj(i)
	{	
		j=i+1;
		if (i>=ZAM_COUNT) return;

		html='Postêp wykonania: pozycja '+j+' z '+ZAM_COUNT;	
		span=getObject('syncSpan');		
		if (i==0 && span.innerHTML.length>0)
		{
			alert('Dzia³a ju¿ jedna synchronizacja');
			return;
		}
		
		statusbar(span,(j/ZAM_COUNT));
		window.status=html;


		if ('<?echo $KAMELEON_MODE?>'=='1')
		{
			alert('Dzia³a tylko poza kameleonem');
			return;
		}

		r=Math.random();
		href='<? echo "$self$next_char"?>a='+r+'&action=WS_DodajZamowienie&list[za_id]='+ZAMY[i]+'&js_action=synchronizuj('+j+')';
				
		setTimeout(action_execute,1);
	}

</script>



