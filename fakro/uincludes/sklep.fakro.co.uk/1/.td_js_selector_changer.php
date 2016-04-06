<?
	$query="SELECT page_id AS p FROM webtd WHERE
		server=$SERVER_ID AND ver=$ver AND lang='$lang'
		AND html='td_js_selector.php'";

	$res=$kameleon_adodb->execute($query);

	for ($i=0;$i<$res->recordCount();$i++)
	{
		parse_str(ado_explodeName($res,$i));

		$pgs[]=$p;
		
	}

	
	if (count($pgs)) 
	{
		$walkpages=':'.implode(':',$pgs).":$page:";
		echo "<form method=post action='$self'>
			<input type='hidden' name='td_js_sel_upd'
				value='$walkpages'>
			<input type='submit' value='Rozpocznij aktualizacje selectorow'>
			</form>
			";
	}
	

	
	include("$INCLUDE_PATH/.td_js_selector_walker.php");


?>
