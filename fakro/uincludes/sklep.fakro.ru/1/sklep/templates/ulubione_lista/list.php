<?
//		if (!is_object($res)) return;
		if ($i>=$res->RecordCount()) 
		{
			$template_loop=0;
			return;
		}
		
		parse_str(ado_explodename($res,$i));
		$i++;
		$lp=$i;

		$_tr = " class=t1";
		if (($i)%2) $_tr = " class=t2";

		
		
		$data_rez = "";
		
//		parse_str(ado_query2url($sql));

		$_nazwa=urlencode($ul_nazwa);
		include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
?>
