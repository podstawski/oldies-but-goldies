<?
	$SKLEP_INCLUDE_PATH="..";
	include ("$SKLEP_INCLUDE_PATH/pre.h");

	$sql="SELECT * FROM towar";

	$res=$projdb->execute($sql);
	$c=$res->recordCount();

	ob_implicit_flush (1);

	$pola=array("to_opis_d_i");

	for ($i=0;$i<$res->recordCount();$i++)
	{
		parse_str(ado_explodeName($res,$i));

		$lp=$i+1;
		echo "\r                    \r$lp/$c, id=$to_id, idx=$to_indeks";


		$sql="UPDATE towar SET to_indeks=to_indeks";

		foreach ($pola AS $pole)
		{
			eval("\$val=addslashes(stripslashes(\$$pole));");
			$val=ereg_replace("\n([^\<])","\n<br>\\1",$val);
			$sql.=",$pole='$val'";
		}
		$sql.= " WHERE to_id=$to_id";

		$projdb->execute($sql);
	}

	echo "\n";
?>
