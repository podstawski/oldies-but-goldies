

<?
//55x78","55x98","66x98","66x118","78x98","78x118","78x140","94x118","94x140","114x118","114x140","134x98","78x160"



$wymiary = array("55x80","55x100","66x100","66x120","78x100","78x120","114x120","134x100");

$name= "ARK";
$st = 101;
$end = 102;
$sklep_id = $SKLEP_ID;
$prefix = "00";
//$kat="XLW 40";

$adodb->BeginTrans();
//$adodb->debug=1;


/*
//************obrazki*************
for($j=$st;$j<=$end;$j++)
{
	if($j>9)
		$prefix="0";
	if($j>99)
		$prefix="";
	$obrazek = strtolower($name)."_".$prefix.$j.".jpg";
	$sql = "update towar set to_foto_s='UIMAGES:kat/$obrazek' where to_indeks like '$name%$prefix$j%';";
	$adodb->execute($sql);
	echo $sql."<br>";
}
$adodb->debug=0;
//$adodb->CommitTrans();
$adodb->RollbackTrans();

//************************
return;
*/


for($j=$st;$j<=$end;$j++)
{
//	echo count($wymiary)."<hr>";
	if( $j>9) $prefix="0";
	if( $j>99) $prefix="";
	for($_i=0;$_i<count($wymiary);$_i++)
	{
		//echo $wymiary[$_i]."<br>";
		$kat = $name.'-'.$prefix.$j;
		//$kat = $name.$prefix.$j;
		
		$wym = explode("x",$wymiary[$_i]);
		$index = $name.'-'.$prefix."$j-".$wymiary[$_i];


		$szer = $wym[0];
		$wys = $wym[1];
			
		$sql="SELECT count(*) AS c FROM towar WHERE to_indeks='$index'";
		parse_str(ado_query2url($sql));

		
		if ($c) continue;


		$sql = "SELECT ka_id FROM kategorie WHERE ka_nazwa='$kat'";
		$ka_id = "";
		parse_str(ado_query2url($sql));
		if (!strlen($ka_id))
		{
			echo "brak kategorii ".$kat."<br>";
			continue;
		}

		//echo "$sql; ($kat) <br>";continue;
		
	
		$sql = "INSERT INTO towar (to_indeks) VALUES ('$index'); SELECT MAX(to_id) AS to_id FROM towar";

		
		//echo $sql."<br><br>"; continue;
		
		parse_str(ado_query2url($sql));
		if (!strlen($to_id))
		{
			echo "brak towaru ".$index."<br>";
			continue;
		}
		
		$FORM[to_indeks] = $index;
		$FORM[to_nazwa] = substr($name,0,3);
		$FORM[id] = $to_id;
		$FORM[tp_a] = $wym[0];
		$FORM[tp_b] = $wym[1];
		$FORM[tp_stan]= '*';

		include($SKLEP_INCLUDE_PATH."/action/ZapiszTowar.php");
		include($SKLEP_INCLUDE_PATH."/action/ZapiszTowarParametry.php");
		


		$sql = "INSERT INTO towar_kategoria (tk_to_id,tk_ka_id) VALUES ($to_id,$ka_id)";
//		echo $sql."<hr>";
		$adodb->execute($sql);
		$sql = "INSERT INTO towar_sklep (ts_to_id,ts_sk_id,ts_magazyn,ts_aktywny) VALUES ($to_id,$sklep_id,1,0)";
		$adodb->execute($sql);
//		echo $sql."<hr>";
		$sql = "UPDATE towar SET to_ka_c=1 WHERE to_id=".$FORM[id];
		$adodb->execute($sql);


	}
}
$adodb->debug=0;
//$adodb->RollbackTrans();
$adodb->CommitTrans();


?>