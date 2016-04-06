<style>
	.statusb {
		border: 2px ridge white;
		height: 15px;
		margin: 5px 5px 5px 0;
	}
	.statusb .sbi {
		background-color:red;
		height:15px;
	}
</style>
<form method="post" action="<?echo $self?>">
	<input type="text" value="0" size=4 title="Zacznij od pozycji" name="zacznij_od">
	<input type="button" value="Synchronizuj ceny z programem magazynowym" class="addbut"
		onclick="synchronizuj(zacznij_od.value)">
</form>

<span id="syncSpan"></span>

<script id="syncScript" src="" language="JavaScript"></script>
<script id="syncScript" src="<? echo $SKLEP_INCLUDE_PATH ?>/js/statusbar.js" language="JavaScript"></script>

<script language="JavaScript">

<?
	$query="SELECT to_id FROM towar";
	$res=$projdb->Execute($query);

	echo "	var TOWAR_COUNT=".$res->RecordCount().";\n";
	echo "	TOWARY = new Array(TOWAR_COUNT);\n";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

		echo "	TOWARY[$i]=$to_id;\n";
	}


?>
	var _script=getObject('syncScript');
	var href='';

	function action_execute()
	{
		_script.src=href;
	}

	function synchronizuj(i)
	{	
		i=i*1;
		j=i+1;
		if (i>=TOWAR_COUNT) return;

		html='Postêp wykonania: pozycja '+j+' z '+TOWAR_COUNT;	
		span=getObject('syncSpan');		
		if (i==0 && span.innerHTML.length>0)
		{
			alert('Dzia³a ju¿ jedna synchronizacja');
			return;
		}
		
		statusbar(span,(j/TOWAR_COUNT));

		//span.innerHTML=html;
		window.status=html;


		if ('<?echo $KAMELEON_MODE?>'=='1')
		{
			alert('Dzia³a tylko poza kameleonem');
			return;
		}

		r=Math.random();
		href='<? echo "$self$next_char"?>a='+r+'&action=WS_AktualizacjaCeny&list[to_id]='+TOWARY[i]+'&js_action=synchronizuj('+j+')';
				
		//alert(href+'...'+i+'/'+TOWAR_COUNT);
		setTimeout(action_execute,1);
	}


</script>
