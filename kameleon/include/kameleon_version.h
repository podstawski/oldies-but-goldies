<?
$xy=20;

for ($v=$k_version+0.01;$v<=$KAMELEON_VERSION;$v+=0.01)
{	

	
	$plik="changes/v_$v.txt";

	if (!file_exists("changes/v_$v.txt")) continue;

	$zmiany="";
	$pole="null";
	$sql="";
	$opis="";
	

	$plik=file("changes/v_$v.txt");
	
	$ile = count($plik);

	for ($i=0;$i<$ile;$i++)
	{
	  $linia=addslashes($plik[$i]);
		$linia=ereg_replace("\\$","\\\$",$linia);

		if ($linia[0]=="[")
		{
		  
			$pos=strpos($linia,"]");
			if ($pos)
			{
				$pole=substr($linia,1,$pos-1);
				continue;
			}
		}
		eval("\$zmiany[$pole].=\"$linia\";");
		
	}


	// UWAGA!
	// $adodb->dbType mimo tej samej wartosci nie jest nazw¹ z klasy adoDB do po³¹czenia
	// z baz¹ danych z pliku const.h
	// Jest natomiast Kameleonow¹ nazwy po³¹zcenia z baza danych zdefiniowan¹
	// w pliku: include/class/kdb.php


	$v100=ereg_replace("[^0-9]","",$v);
	if (strlen($v100)==2) $v100.='0';
	if (strlen($v100)==1) $v100.='00';
	$v100+=0;
	
	if ($v100<415) $sql=trim(stripslashes($zmiany[sql]));
	else $sql=trim(stripslashes($zmiany[$adodb->dbType]));
	$opis=trim(addslashes(stripslashes($zmiany[opis])));

	$ssql=addslashes($sql);

	$opis=toText($opis);
	if ($v100<415)
		$query="$sql 
			INSERT INTO kameleon (version,sql,opis,d_issue)
			VALUES ($v,'$ssql','$opis',CURRENT_DATE);
		";
	else
		$query="$sql 
			INSERT INTO kameleon (version,sql,opis,nd_issue)
			VALUES ($v,'$ssql','$opis',".time().");
		";

	//$adodb->debug=1;
	//echo nl2br($query);
	if (!$adodb->Execute($query)) 
	{
		push($SERVER_ID);
		$SERVER_ID="err";
		logquery($adodb->adodb->ErrorMsg()."\n\n".$query);
		$SERVER_ID=pop();
		$KAMELEON_VERSION.="/".($v-0.01);

		break;
	}

	ob_start();
	if (file_exists("changes/v_$v.php")) include ("changes/v_$v.php");
	$czyError=ob_get_contents();
	ob_end_clean();

	$o=addslashes(ereg_replace("[\r\n]","",nl2br(stripslashes("$opis<hr size=1>$czyError"))));

	echo "<script language='javascript'>
		param='width=300,height=250,top=$xy,left=$xy';
		okno$v100=open('Version $v','v$v100',param);
		okno$v100.document.write('$o');
		okno$v100.document.close();
		</script>";
	$xy+=20;

	ob_flush();flush();
}


/*
if (file_exists("changes/label.txt") || file_exists("log/label.txt"))
{
	if (file_exists("changes/label.txt")) $fl=file("changes/label.txt");
	if (file_exists("log/label.txt")) $fl=file("log/label.txt");


	for ($i=0;$i<count($fl);$i++)
	{
		$linia=$fl[$i];
		$linia=ereg_replace("\n","",$linia);
		$linia=ereg_replace("\r","",$linia);
		$linia=explode(";",$linia);

		$lab_label=addslashes(trim($linia[1]));
		$lab_value=addslashes(trim($linia[2]));
		$lab_lang=substr(trim($linia[4]),0,2);
	

		$query="INSERT INTO label (label,value,lang)
			SELECT '$lab_label','$lab_value','$lab_lang'
			WHERE 1 NOT IN (SELECT 1 FROM label 
					WHERE label='$lab_label'
					AND lang='$lab_lang')";

		if (!$adodb->Execute($query))
		{
			$adodb->debug=1;
			$adodb->Execute($query);
			$adodb->debug=0;
			break;
		}
		
	}
}
*/
