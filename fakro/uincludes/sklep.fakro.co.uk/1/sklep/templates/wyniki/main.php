<?
	if (!strlen($LIST[sort_f])) $LIST[sort_f]="to_nazwa";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$qs = $LIST[szukaj];

	if (!strlen(trim($qs))) 
	{
		$qs = $CIACHO[szukaj];
		$LIST[szukaj] = $qs;
	}
	$qs = trim($qs);
	$qs = ereg_replace("  ","",$qs);
//	$qs = ereg_replace("+ ","+",$qs);
//	$qs = strtolower($qs);

	function checkForPolish($str)
	{
		if (strpos(" ".$str,"¶")) return true;
		if (strpos(" ".$str,"±")) return true;
		if (strpos(" ".$str,"¼")) return true;
		if (strpos(" ".$str,"¦")) return true;
		if (strpos(" ".$str,"¡")) return true;
		if (strpos(" ".$str,"¬")) return true;
		if (strpos(" ".$str,"ê")) return true;
		if (strpos(" ".$str,"Ê")) return true;
		if (strpos(" ".$str,"ñ")) return true;
		if (strpos(" ".$str,"Ñ")) return true;
		if (strpos(" ".$str,"³")) return true;
		if (strpos(" ".$str,"£")) return true;
		if (strpos(" ".$str,"æ")) return true;
		if (strpos(" ".$str,"Æ")) return true;
		if (strpos(" ".$str,"¿")) return true;
		if (strpos(" ".$str,"¯")) return true;
		if (strpos(" ".$str,"ó")) return true;
		if (strpos(" ".$str,"Ó")) return true;

		return false;
	}

	function checkForEan($str)
	{
		return !is_numeric($str);
		for ($i=0;$i<strlen($str);$i++)
		{
			$c=substr($str,$i,1);
			if ("$c"=="0") continue;
			$c1=$c+0;
			if ("$c1"=="$c") continue;
			return false;
		}
		return true;
	}


	parse_str($costxt);
	if (!strlen($cyfry))
		$cyfry = "210100";

	if (!strlen($alfan))
		$alfan = "212000";

	$slowa = explode(" ",$qs);
	$_slowa = $slowa;
	if (count($slowa)) $add_sql = "AND (";
	for ($i=0; $i < count($slowa); $i++)
	{
		$slowa[$i]=eregi_replace("&nbsp;"," ",$slowa[$i]);
		if (!strlen(trim($slowa[$i]))) continue;
		if (!strlen(trim($slowa[$i])) == "+") continue;
		if (substr($slowa[$i],0,1) == "+")
		{			
			$operator = "AND";
			$slowa[$i] = substr($slowa[$i],1);
		}
		else
			$operator = "OR";
 
		if (!strlen(trim($slowa[$i]))) continue;
		if (!$i) $operator = "";

		$add_stage = "";		
		$check = "";
		$tablica = array();

		if (checkForEan($slowa[$i]))
			$tablica = $alfan;
		else
			$tablica = $cyfry;

		$pole = "";

		for ($k=0; $k < 6; $k++)
		{
			$op = ""; //Operator
			$pr = ""; // Procenty do LIKE

			switch ($k)
			{
				case 0:$pole = "to_nazwa";break;
				case 1:$pole = "to_indeks";break;
				case 2:$pole = "to_klucze";break;
				case 3:$pole = "to_ean";break;
				case 4:$pole = "to_opis_m_i";break;
				case 5:$pole = "to_opis_d_i";break;
			}		
	
			if ($tablica[$k] == "0") continue;
			if ($tablica[$k] == "1") 
				$op = "=";
			if ($tablica[$k] == "2") 
				$op = "~*";
			if ($tablica[$k] == "3")
				$op = "LIKE";


			$dodaj_nawias = 0;

			if (strlen($check)) $check.= "OR ";

			if (checkForPolish($slowa[$i]))
			{
				$check.= "( $pole $op '$pr".strtoupper($slowa[$i])."$pr' OR";
				$dodaj_nawias = 1;
			}

			$check.= " $pole $op '$pr".strtolower($slowa[$i])."$pr' ";
			if ($dodaj_nawias) $check.= ") ";

		}
		$add_sql.= $operator." ($check) ";
	}

	if (count($slowa)) $add_sql.= ")";

	$slowa = $_slowa;
	if (!strlen($qs)) $add_sql = "";
	$FROMWHERE = "FROM towar LEFT JOIN towar_sklep ON ts_to_id = to_id AND ts_sk_id = $SKLEP_ID
				  WHERE ts_aktywny>0
				  $add_sql";


	$producer_comment="<!--";
	$producer_no_comment="-->";


	if (!strlen($add_sql) && !$size) 
	{
		$error=" ";
		return;
	}

	if ($CIACHO[pr_id])
	{
		$producer_comment="";
		$producer_no_comment="";
		$FROMWHERE.=" AND to_pr_id=".$CIACHO[pr_id];
		$to_pr_id=$CIACHO[pr_id];
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}	

	$co="*";
	include("$SKLEP_INCLUDE_PATH/templates/towar_lista_pola.php");
	$sql = "SELECT $co $FROMWHERE ORDER BY ".$sort;

//	echo $sql;
//	exit();

	if (!$LIST[ile])
	{
		$query="SELECT count(to_id) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}
	$navi=$size?navi($self,$LIST,$size):"";
	
	if (strlen($navi))
		$result = $projdb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($sql);	

	$table_display = 'inline';
	if (!is_object($result) || !$result->RecordCount()) 
	{
		$table_display = 'none';
	}

	$n_wymiar=sysmsg("th_name","system");
	$i_wymiar=sysmsg("th_index","system");
	$sysmsg_lp = sysmsg("Lp.","system");
	$sysmsg_th_options = sysmsg("th_options","system");
	
	$cart_next=$self;
	if ($SYSTEM[koszyk]) $cart_next=$KOSZYK_NEXT;
	
	$sort_navi = sort_navi_options($LIST);

	$sysmsg_wrong_value = sysmsg("Wrong value","system");
	$sysmsg_quantity = sysmsg("Quantity","cart");
	$sysmsg_article_added_to_offer = sysmsg("Article added to offer","cart");

	$lp=0;
	$i=0;
	$LIST[sort_d]+=0;
?>
