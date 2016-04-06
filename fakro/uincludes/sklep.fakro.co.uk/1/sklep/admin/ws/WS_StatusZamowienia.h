<?
	$label="all_order_analysis";
	if ($LIST[za_id]) $label="order_analysis";
	if ($AUTH[p_admin]) $label.="_admin";
	$button=sysmsg($label,"order");
	if ($button==$label) $button="Analiza wszystkich przyjêtych zamówieñ";

	$form="<form method=\"post\" action=\"$self\">
		<table width=\"100%\"><tr>
		<td width=1><input type=\"button\" value=\"$button\" class=\"addbut\"
				onclick=\"synchronizuj(0,0)\">
		 <td><span id=\"syncSpan\"></span>
		</table>
		</form>";
?>

<script id="syncScript" src="" language="JavaScript"></script>
<script id="syncScript" src="<? echo $SKLEP_INCLUDE_PATH ?>/js/statusbar.js" language="JavaScript"></script>

<script language="JavaScript">

<?
	$query="SELECT za_id FROM zamowienia WHERE za_status=1";
	if ($AUTH[p_admin])
	{
		include ($SKLEP_INCLUDE_PATH."/raporty/daty.php");
		if ($od>100) $query.=" AND za_data >= $od";
		if ($do>100) $query.=" AND za_data <= $do";
	}
	else
	{
		$CIACHO[kontrahent_id]=$AUTH[parent];
	}

	if ($CIACHO[kontrahent_id]) $query.=" AND za_su_id=".$CIACHO[kontrahent_id];
	if ($LIST[za_id]) $query.=" AND za_id=".$LIST[za_id];
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
	var total=0;

	function action_execute()
	{
		//return;
		_script.src=href;
	}

	function synchronizuj(i,c)
	{	
		j=i+1;
		if (c>0) total++;
		span=getObject('syncSpan');
		if (i>=ZAM_COUNT) 
		{
			alert('Zmieniono '+total+' pozycji.');
			span.innerHTML='';
			return;
		}

		html='Postêp wykonania: pozycja '+j+' z '+ZAM_COUNT;			
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
		href='<? echo "$self$next_char"?>a='+r+'&action=WS_StatusZamowienia&list[za_id]='+ZAMY[i]+'&js_action=synchronizuj('+j+',$status_changed)';
				
		setTimeout(action_execute,1);
	}

</script>
<?
	if ($res->RecordCount()) echo $form;

?>
