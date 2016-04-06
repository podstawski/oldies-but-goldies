<?

define("WM_INFINITY",900000000);

include_once("$SKLEP_INCLUDE_PATH/class/db.php");

class WM extends DB
{
	var $sklep;
	var $sklep_nazwa;
	var $system;
	var $stany_magazynowe;
	var $towary;
	var $ws;
	var $include_path;
	var $ufiles;


	function WM($server,$lang)
	{
		if (!$_connectionID) if (!$this->dbconnect()) return;
		$this->now=time();

		$query="SELECT sk_id,sk_nazwa FROM sklep WHERE sk_server=$server";
		parse_str($this->ado_query2url($query));
		$this->sklep=$sk_id;
		$this->sklep_nazwa=$sk_nazwa;
		$this->lang=$lang;
		$this->query_debug=array();
	}



	function update_towar($to_id,$towar)
	{
		if (strlen($towar[to_vat])) 
		{	$to_vat=toFloat($towar[to_vat]);
			if ($to_vat<1) $towar[to_vat]=$to_vat*100;
		}


		if (is_array($towar[producent]))
		{
			$pr_id=$towar[producent][pr_id];
			if (!$pr_id && strlen($towar[producent][pr_ws]) )
			{
				$pr_id=$this->ws2id("producent","pr",$towar[producent]);
			}
			if ($pr_id) $towar[to_pr_id]=$pr_id;
		}

		if (!$this->update_table("towar",array("to_id"=>$to_id),$towar,
									array("to_cena","to_vat"),array("to_indeks"))) return;

		if (is_array($towar[towar_sklep]))
		{
			if (!$towar[towar_sklep][ts_id])
			{
				$towar[towar_sklep][ts_id]=$this->towar_sklep_id($to_id);
			}
			if ($towar[towar_sklep][ts_id])
			{
				$this->update_table("towar_sklep",array(ts_id=>$towar[towar_sklep][ts_id]),
									$towar[towar_sklep],
									array("ts_cena","ts_kwant_zam","ts_magazyn","ts_aktywny"));
			}
		}

		if (is_array($towar[stany_magazynowe]))
		{
			if (isset($towar[stany_magazynowe][sm_ilosc]))
			{
				$this->towar_ruch(0,$to_id,"Wartosc ustalona przez system",0,
									toFloat($towar[stany_magazynowe][sm_ilosc]));
			}
		}

		if ($towar[promocja_towaru][pt_cena])
		{
				$NOW=$this->now;
				$pt_cena=toFloat($towar[promocja_towaru][pt_cena]);
				if ($pt_cena>0)
				{
					$sql="UPDATE promocja_towaru SET pt_cena=$pt_cena 
							WHERE pt_ts_id=".$towar[towar_sklep][ts_id]."
							AND (pt_koniec>$NOW OR pt_koniec IS NULL);";

					$this->execute($sql);
				}
		}

		if (is_array($towar[kategorie]))
		{
			$ka_id=$towar[kategorie][ka_id];
			if (!$ka_id && strlen($towar[kategorie][ka_ws]))
			{
				$ka_id=$this->ws2id("kategorie","ka",$towar[kategorie]);
			}
			if ($ka_id)
			{
				$sql = "SELECT tk_id FROM towar_kategoria WHERE tk_ka_id = $ka_id AND tk_to_id = $to_id";
				parse_str($this->ado_query2url($sql));
				if (!$tk_id)
				{
					$sql = "INSERT INTO towar_kategoria (tk_ka_id, tk_to_id) VALUES ($ka_id,$to_id);
							UPDATE towar SET to_ka_c = wIluKatTow(to_id) WHERE to_id = $to_id;
							UPDATE kategorie SET ka_to_c = ileTowWKat(ka_id) WHERE ka_id = $ka_id;";
					$this->execute($sql);
				}
			}
		}

		if (is_array($towar[towar_parametry]))
		{
			$query="SELECT tp_id FROM towar_parametry WHERE tp_to_id=$to_id";
			parse_str($this->ado_query2url($query));

			if (!$tp_id)
			{
				$sql="INSERT INTO towar_parametry (tp_to_id) VALUES ($to_id);
						SELECT max(tp_id) AS tp_id FROM towar_parametry";
				parse_str($this->ado_query2url($sql));

			}

			$this->update_table("towar_parametry",array(tp_id=>$tp_id),
								$towar[towar_parametry],
								array("tp_a","tp_b","tp_b","tp_d","tp_l",
										"tp_r1","tp_r2","tp_o",
										"tp_m_m","tp_m_szt","tp_m_m2","tp_m_jm"),
								array("tp_to_id"));

		}

	}

	function debug($str="")
	{
		if ($this->forget_debug) return false;
		global $REMOTE_ADDR;
		$net=explode(".",$REMOTE_ADDR);
		$neta=$net;
		$net=$net[0].".".$net[1].".".$net[2];

		if ($neta[0]=="10") return true;
		if ($REMOTE_ADDR=="150.254.163.165") return true;
		if ($net=="195.216.106") return true;
		if ($REMOTE_ADDR=="62.21.60.33") return true;
		//if (strlen($str)) echo "<font color=red>$str</font><br>";
		return false;
		
	}



	function kwant_towaru($towar)
	{
		parse_str($this->table_row2url("towar_sklep",
				array("ts_to_id"=>$towar,"ts_sk_id"=>$this->sklep)
				,false));
		if (!$ts_kwant_zam) return 1;
		return $ts_kwant_zam;
	}


	function stan_magazynu($towar)
	{
		if (strlen($this->stany_magazynowe[$towar])) return $this->stany_magazynowe[$towar];

		parse_str($this->table_row2url("towar_sklep",
				array("ts_to_id"=>$towar,"ts_sk_id"=>$this->sklep)
				,false));
		if (!$ts_magazyn) 
		{
			$this->stany_magazynowe[$towar]=WM_INFINITY;
			return WM_INFINITY;
		}

		$magazyny=$this->magazyny_sklepu();
		$query="SELECT sum(sm_ilosc) AS suma FROM stany_magazynowe 
				WHERE sm_to_id=$towar AND sm_ma_id IN ($magazyny)
				AND sm_ilosc IS NOT NULL";
		parse_str($this->ado_query2url($query));

		if (!strlen($suma)) $suma=WM_INFINITY;

		$this->stany_magazynowe[$towar]=$suma;
		return 0+$suma;
	}

	function stan_magazynu_display($towar)
	{
		$wynik=$this->stan_magazynu($towar);
		if ($wynik==WM_INFINITY) return "";
		return $wynik;
	}

	function zamowione_towary($towar,$sklep=0)
	{
		$sklepy=$sklep?$sklep:$this->pozostale_sklepy_magazynu();

		$query="SELECT sum(zp_ilosc) AS ile
				FROM towar_sklep 
				LEFT JOIN zampoz ON ts_id=zp_ts_id
				LEFT JOIN zamowienia ON zp_za_id=za_id	
				WHERE ts_to_id=$towar 
				AND ts_sk_id IN ($sklepy) AND za_status>=0";
		parse_str($this->ado_query2url($query));
		$ile+=0;

		$this->debug("zam: $ile ($query)");

		return $ile;
	}

	function magazyny_sklepu($sklep=0)
	{
		if (!$sklep) $sklep=$this->sklep;

		$magazyny=$this->session["MAGAZYNY"];
		if (strlen($magazyny)) return $magazyny;

		$query="SELECT ms_ma_id FROM magazyn_sklep WHERE ms_sk_id=$sklep";
		$res=$this->projdb->Execute($query);

		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_explodeName($res,$i));
			if (strlen($magazyny)) $magazyny.=",";
			$magazyny.=$ms_ma_id;
		}
		$this->session["MAGAZYNY"]=$magazyny;
		return $magazyny;
	}

	function pozostale_sklepy_magazynu()
	{
		$sklepy=$this->session["SKLEPY"];

		if (strlen($sklepy)) return $sklepy;

		$magazyny=$this->magazyny_sklepu();
		if (!strlen($magazyny)) return "0";


		$query="SELECT ms_sk_id FROM magazyn_sklep WHERE ms_ma_id IN ($magazyny)";
		$res=$this->projdb->Execute($query);

		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_explodeName($res,$i));
			if (strlen($sklepy)) $sklepy.=",";
			$sklepy.=$ms_sk_id;
		}
		$this->session["SKLEPY"]=$sklepy;

		return $sklepy;
	}

	function dostep_magazynu($towar,$koszyk_id=0)
	{
		$sklepy=$this->pozostale_sklepy_magazynu();
		
		$query="SELECT sum(ko_ilosc) AS ile
				FROM towar_sklep LEFT JOIN koszyk ON  ts_id=ko_ts_id
				WHERE ts_to_id=$towar
				AND ts_sk_id IN ($sklepy) AND ko_id<>$koszyk_id
				AND (ko_deadline > $this->now OR ko_deadline IS NULL)";
		parse_str(ado_query2url($query));

		$ile+=0;

		$this->debug("kosz: $ile ($query) ");
		$ile+=$this->zamowione_towary($towar);
		$this->debug("ile: $ile ");

		$wynik=$this->stan_magazynu($towar)-$ile;
		return $wynik;
	}

	function dostep_magazynu_display($towar)
	{
		$stan=$this->stan_magazynu($towar);
		if ($stan==WM_INFINITY) return "";
		$wynik=$this->dostep_magazynu($towar);
		if ($wynik<0) return 0;
		return $wynik;
	}

	function procent_ceny_ilosciowo($towar,$ilosc)
	{
		$proc=100; //100%

		$query="SELECT tk_ka_id AS ka_id
				FROM towar_kategoria
				WHERE tk_to_id=$towar ";
		$kres=$this->Execute($query);

		for ($k=0;$k<$kres->RecordCount();$k++)
		{
			parse_str(ado_explodeName($kres,$k));

			$kat=$ka_id;
			while($kat)
			{
				$query="SELECT min(ri_procent) AS ri_procent
						FROM rabat_ilosciowy
						WHERE ri_ka_id=$kat AND ri_minmum<=$ilosc";
				parse_str($this->ado_query2url($query,true));

				$sql="SELECT ka_parent,ka_nazwa FROM kategorie WHERE ka_id=$kat";
				parse_str($this->ado_query2url($sql,true));
				$this->debug("$ka_nazwa: ($query )");

				if ($ri_procent)
				{
					$this->debug("rabat ilosciowy: $ri_procent %");
					if ($ri_procent<$proc) $proc=$ri_procent;
					break;
				}
				$kat=$ka_parent;
			}
		}


		return $proc;
	}


	function procent_ceny_kontrahenta($towar,$kontrahent)
	{
		parse_str($this->table_row2url("kontrahent_sklep",
					array("ks_sk_id"=>$this->sklep,
						"ks_su_id"=>$kontrahent),
					true));

		if (!$ks_id) return 100; // 100%

		$proc=100;

		$query="SELECT tk_ka_id AS ka_id
				FROM towar_kategoria
				WHERE tk_to_id=$towar";
		$kres=$this->Execute($query);

		for ($k=0;$k<$kres->RecordCount();$k++)
		{
			parse_str(ado_explodeName($kres,$k));

			$this->debug("<B>$ka_nazwa:</B>");

			$kat=$ka_id;
			while($kat)
			{
				$query="SELECT rk_procent FROM rabat_kontrahenta
						WHERE rk_ka_id=$kat AND rk_ks_id=$ks_id";
				parse_str($this->ado_query2url($query,true));

				$query="SELECT ka_parent,ka_nazwa FROM kategorie WHERE ka_id=$kat";
				parse_str($this->ado_query2url($query,true));

				if ($rk_procent)
				{
					$this->debug("procent kontrahenta: $rk_procent %");
					if ($rk_procent<$proc) $proc=$rk_procent;
					break;
				}
				$kat=$ka_parent;
			}
		}

		return $proc;
	}

	function kategorie($towar)
	{
		if (strlen($this->session[kategorie]["$towar"])) 
			return explode(',',$this->session[kategorie]["$towar"]);

		$query="SELECT tk_ka_id AS ka_id
				FROM towar_kategoria
				WHERE tk_to_id=$towar ";
		$kres=$this->Execute($query);

		for ($k=0;$k<$kres->RecordCount();$k++)
		{
			parse_str(ado_explodeName($kres,$k));
			$kats[]=$ka_id;
		}
		$this->session[kategorie]["$towar"]=implode(',',$kats);
		return $kats;
	}


	function system_rabat($towar,$ilosc,$kontrahent)
	{
		$proc1=100;
		$proc2=100;

		if (strlen($this->session[rabat]["$towar-$kontrahent-$ilosc"])) 
			return $this->session[rabat]["$towar-$kontrahent-$ilosc"];
	
		if ($ilosc) $proc1=$this->procent_ceny_ilosciowo($towar,$ilosc);
		if ($kontrahent) $proc2=$this->procent_ceny_kontrahenta($towar,$kontrahent);

		$proc=($proc1<$proc2)?$proc1:$proc2;
		$this->session[rabat]["$towar-$kontrahent-$ilosc"]=100-$proc;
		return (100-$proc);
	}

	function towar_sklep_id($towar)
	{
		if ($this->towary[$towar]->ts_id) return $this->towary[$towar]->ts_id;
		parse_str($this->table_row2url("towar_sklep",
				array("ts_to_id"=>$towar,"ts_sk_id"=>$this->sklep)
				,false));
		$this->towary[$towar]->ts_id=$ts_id;

		if (!$ts_id)
		{
				$query="INSERT INTO towar_sklep (ts_to_id, ts_sk_id) VALUES ($towar, ".$this->sklep.");
						SELECT ts_id FROM towar_sklep WHERE ts_to_id=$towar AND ts_sk_id=".$this->sklep;
				parse_str($this->ado_query2url($query));
				$this->towary[$towar]->ts_id=$ts_id;
		}

		return $ts_id;
	}


	function oryginalna_cena($towar)
	{
		if ($this->towary[$towar]->ts_cena) return $this->towary[$towar]->ts_cena;

		parse_str($this->table_row2url("towar_sklep",
				array("ts_to_id"=>$towar,"ts_sk_id"=>$this->sklep)
				,false));

		$this->towary[$towar]->ts_cena=$ts_cena;
		$this->towary[$towar]->ts_id=$ts_id;
		return $ts_cena;
	}


	function system_cena($towar,$ilosc=0,$kontrahent=0)
	{
		$NOW=$this->now;

		$ts_cena=$this->oryginalna_cena($towar);
		$ts_id=$this->towary[$towar]->ts_id;

		if (!$ilosc) $ilosc=$this->kwant_towaru($towar);
		if (!$kontrahent && $this->auth[parent]>0) $kontrahent=$this->auth[parent];

		$query="SELECT min(pt_cena) AS pt_cena 
				FROM promocja_towaru LEFT JOIN promocja ON pt_pm_id=pm_id
				WHERE pt_ts_id=$ts_id
				AND (pm_poczatek<=$NOW OR pm_poczatek IS NULL)
				AND (pm_koniec>=$NOW OR pm_koniec IS NULL)
				AND (pt_poczatek<=$NOW OR pt_poczatek IS NULL)
				AND (pt_koniec>=$NOW OR pt_koniec IS NULL)
				";

		parse_str($this->ado_query2url($query));

		if ($pt_cena) $ts_cena=$pt_cena;

		if (!strlen($ts_cena)) $ts_cena=0;
		//if (!$kontrahent) return $ts_cena;

		$rabat=$this->system_rabat($towar,$ilosc,$kontrahent);
		$proc=100-$rabat;
		$this->debug("<B>procent ceny: $proc %</B>");
		return round($ts_cena*$proc)/100;
	}

	function towar_czas_zycia($towar)
	{
		parse_str($this->table_row2url("towar_sklep",
				array("ts_to_id"=>$towar,"ts_sk_id"=>$this->sklep)
				,false));
		if (!$ts_czas_koszyk) return 24*3600;
		return $ts_czas_koszyk;
	}

	function towar_wymiary($towar,$sep=" x ")
	{
		$param=array("a","b","c","d","l","r1","r2");


		parse_str($this->table_row2url("towar_parametry",
						array("tp_to_id"=>$towar),true));

		foreach ($param AS $p)
		{
			eval("\$w = \$tp_$p ;");
			if (!strlen($w)) continue;
			if (strlen($wynik)) $wynik.=$sep;
			$wynik.=$w;
		}
		return $wynik;
	}	

	function towar_ruch($sklep,$towar,$uwagi,$ruch,$stan=0,$magazyn=0)
	{
		global $AUTH;

		$NOW=$this->now;
		if (!$sklep) $sklep=$this->sklep;

		if (!$magazyn)
		{
			$mag=explode(",",$this->magazyny_sklepu($sklep));
			$magazyn=$mag[0];
		}
		

		$query="SELECT * FROM stany_magazynowe WHERE sm_to_id=$towar AND sm_ma_id=$magazyn";
		parse_str($this->ado_query2url($query));

		if (!$sm_id)
		{
			$sql="INSERT INTO stany_magazynowe (sm_to_id,sm_ma_id) VALUES ($towar,$magazyn)";
			$this->projdb->execute($sql);
			parse_str($this->ado_query2url($query));
		}
		

		if (!$sm_id) return;

		
		
		$zmiana=$stan-$sm_ilosc;

		if ($stan)
		{
			$zmiana=$stan-$sm_ilosc;
			
		}
		elseif ($ruch)
		{
			$zmiana=$ruch;
			$stan=$sm_ilosc+$zmiana;
		}

		if (!$zmiana) return;

		$ts_id=$this->towar_sklep_id($towar);

		$kto=$AUTH[id];
		if (!$kto) $kto=$this->system[master];


		$query="UPDATE stany_magazynowe SET sm_ilosc=$stan WHERE sm_id=$sm_id;
				INSERT INTO ruchy (ru_su_id,ru_ts_id,ru_ma_id,ru_zmiana,ru_stan,ru_data,ru_uwagi)
				VALUES ($kto,$ts_id,$magazyn,$zmiana,$stan,$NOW,'$uwagi')";
		$this->execute($query);
	}


	function ruch_mag_zam($za_id,$uwagi,$razy)
	{
		$query="SELECT * FROM zamowienia WHERE za_id=$za_id";
		parse_str($this->ado_query2url($query));

		$query="SELECT * FROM zampoz,towar_sklep WHERE zp_za_id=$za_id AND ts_id=zp_ts_id";

		$res=$this->projdb->Execute($query);
		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($res,$i));
			$this->towar_ruch($ts_sk_id,$ts_to_id,$uwagi." $za_id ($za_numer)",$zp_ilosc*$razy);
		}
	}




	function osoba($su_id)
	{
		if (!$su_id) return "";

		$query="SELECT * FROM system_user WHERE su_id=$su_id";
		$wynik=urlEncodedStr2arr($this->ado_query2url($query));

		if ($wynik[su_parent])
		{
			$query="SELECT * FROM system_user WHERE su_id=".$wynik[su_parent];
			$wynik[parent]=urlEncodedStr2arr($this->ado_query2url($query));

			$wynik[parent][su_nazwa]=$wynik[parent][su_nazwisko];
		}
		else
		{
			$wynik[su_nazwa]=$wynik[su_nazwisko];
		}

		return $wynik;
	}




	function ws2id($table,$prefix,$data,$nazwa="nazwa")
	{
		$ws=$data["${prefix}_ws"];
		$id=$this->ws[$table][$ws];
		if ($id) return $id;

		$query="SELECT ${prefix}_id AS id FROM $table WHERE ${prefix}_ws='$ws'";
		parse_str($this->ado_query2url($query));

		$_nazwa=addslashes(stripslashes($data["${prefix}_$nazwa"]));
		if (!$id && strlen($_nazwa))
		{
			$sql="INSERT INTO $table (${prefix}_ws,${prefix}_$nazwa)
					VALUES ('$ws','$_nazwa');
					SELECT max(${prefix}_id) AS id FROM $table";
			parse_str($this->ado_query2url($sql));	
		}
		
		$this->ws[$table][$ws]=$id;
		return $id;
	}

	function to_ids($fromwhere,$handle,$size,$start,$order)
	{
		$wynik=$this->poptemp($handle);
		if (strlen($wynik)) return $wynik;
		$sql = "SELECT to_id $fromwhere $order";
		$res = $this->SelectLimit($sql,$size,$start);
		for ($i=0; $i< $res->RecordCount(); $i++)
		{
			parse_str($this->ado_explodename($res,$i));
			$wynik.= ",$to_id";
		}
		$wynik = substr($wynik,1);
		$this->pushtemp($handle,$wynik);
		return $wynik;

	}

	function saldo_kontrahenta($su_id)
	{
		if (!strlen($su_id)) return false;

		$sql = "SELECT su_saldo FROM system_user WHERE
				su_id = $su_id";
		parse_str($this->ado_query2url($sql));
		return $su_saldo;
	}

	function towary_powiazane($to_id,$pole='')
	{
		if (!strlen($to_id)) return "";
	
		if (strlen($this->session[towary_powiazane][$to_id.$pole])) 
			return $this->session[towary_powiazane][$to_id.$pole];		

		$NOW = time();

		$sql = "SELECT pm_symbol,pm_id FROM towar_sklep
				LEFT JOIN promocja_towaru ON pt_ts_id = ts_id
				LEFT JOIN promocja ON pt_pm_id = pm_id
				WHERE ts_to_id = $to_id AND ts_aktywny = 1 
				AND ((pm_koniec > $NOW AND pm_poczatek < $NOW)
				OR (pm_koniec IS NULL AND pm_poczatek < $NOW)
				OR (pm_koniec > $NOW AND pm_poczatek IS NULL)
				OR (pm_koniec IS NULL AND pm_poczatek IS NULL))";


		$res = $this->execute($sql);
		$ret = '';
		$retpole = '';
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str($this->ado_explodename($res,$i));
			if (!$pm_id) continue;

			$sql = "SELECT * FROM promocja
					LEFT JOIN promocja_towaru ON pt_pm_id = pm_id
					LEFT JOIN towar_sklep ON pt_ts_id = ts_id 
					WHERE pm_id = $pm_id
					AND ts_aktywny <> 1";
			

			$_res = $this->execute($sql);
			for ($_i=0; $_i < $_res->RecordCount(); $_i++)
			{
				parse_str($this->ado_explodename($_res,$_i));
				if ($ts_to_id!=$to_id) 
				{
					$ret.= ",$ts_to_id";
					if (strlen($pole)) eval("\$retpole.=\",\$$pole\";");
				}
			}			
		}


		$sql = "SELECT pm_symbol,pm_id FROM promocja
				WHERE 0 IN 
					(SELECT count(pt_pm_id) 
					 FROM promocja_towaru 
					 LEFT JOIN towar_sklep ON pt_ts_id = ts_id
					 WHERE pt_pm_id=pm_id AND ts_aktywny>0)
				AND ((pm_koniec > $NOW AND pm_poczatek < $NOW)
				OR (pm_koniec IS NULL AND pm_poczatek < $NOW)
				OR (pm_koniec > $NOW AND pm_poczatek IS NULL)
				OR (pm_koniec IS NULL AND pm_poczatek IS NULL))";

		$res = $this->execute($sql);
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str($this->ado_explodename($res,$i));
			$pm_ids[]=$pm_id;

			$symbole.=":$pm_symbol=$pm_id";
		}

		if (count($pm_ids)) if (count($kats=$this->kategorie($to_id)))
		{
			
			$sql="SELECT * FROM promocja
					LEFT JOIN promocja_towaru ON pt_pm_id=pm_id
					LEFT JOIN towar_sklep ON pt_ts_id=ts_id
					LEFT JOIN towar_kategoria ON tk_to_id=ts_to_id
					WHERE pm_id IN (".implode(',',$pm_ids).")
					AND tk_ka_id IN (".implode(',',$kats).")";

			$res = $this->execute($sql);
			for ($i=0; $i < $res->RecordCount(); $i++)
			{
				parse_str($this->ado_explodename($res,$i));
				if ($ts_to_id!=$to_id) 
				{
					$ret.= ",$ts_to_id";
					if (strlen($pole)) eval("\$retpole.=\",\$$pole\";");
				}
			}

		}
		$ret = substr($ret,1);
		if (strlen($pole)) $ret = substr($retpole,1);
		
		$this->session[towary_powiazane][$to_id.$pole]=$ret;
		


		return $ret;
	}


	function towar($to_id,$pole='')
	{
		if (!$to_id) return;
		$query="SELECT * FROM towar
				LEFT JOIN towar_sklep ON ts_to_id=to_id
				WHERE to_id=$to_id";
		
		$url=$this->ado_query2url($query);
		$a=$this->url2array($url);

		if (strlen($pole)) return $a[$pole];
		return $a;
	}

	function system_user($su_id,$pole='')
	{
		if (!$su_id) return;
		$query="SELECT * FROM system_user WHERE su_id=$su_id";
		
		$url=$this->ado_query2url($query);
		$a=$this->url2array($url);

		if (strlen($pole)) return $a[$pole];
		return $a;
	}



	function zamowienie($za_id)
	{
		if (!$za_id) return;


		//$this->debug=1;
		$sql = "SELECT zamowienia.*,poczta.*,system_user.*,
				system_user_osoba.su_imiona, system_user_osoba.su_nazwisko AS nazwisko
				FROM zamowienia 
				LEFT JOIN poczta ON za_poczta=po_id
				LEFT JOIN system_user ON za_su_id=system_user.su_id
				LEFT JOIN system_user AS system_user_osoba ON za_osoba=system_user_osoba.su_id
				WHERE za_id = $za_id";
		parse_str($this->ado_query2url($sql));


		$ret.="<div class=\"zamowienie_deliver\">";

		if ($za_data_przyjecia)
			$data_przyjecia = date('d-m-Y H:i',$za_data_przyjecia);
		else
			$data_przyjecia = "";
		
		parse_str($za_parametry);

		$ret.="<table cellpadding=\"0\" cellspacing=\"0\" class=\"zamowienie\">\n";
		$ret.="<tbody>";
		$ret.="
			<tr><td class=\"lnr\">".sysmsg("No","order")." </td><td class=\"rnr\">$za_numer ($za_numer_obcy)</td></tr>
			<tr><td class=\"lda\">".sysmsg("Date","order")." </td><td class=\"rda\">".date('d-m-Y H:i',$za_data)."</td></tr>
			<tr><td class=\"lda\">".sysmsg("Accept date","order")."</td><td class=\"rda\">".$data_przyjecia."</td></tr>
			<tr><td class=\"lde\">".sysmsg("Delivery","order")." </td><td class=\"rde\">".nl2br($za_adres)."</td></tr>
			<tr><td class=\"ldt\">".sysmsg("Delivery type","order")."</td><td class=\"rdt\">".$po_nazwa."</td></tr>
			<tr><td class=\"ldp\">".sysmsg("Payment type","cart")." </td><td class=\"rdp\">".$platnosc."</td></tr>";
		$ret.="\n</tbody>\n";
		$ret.="\n</table>\n";
		$ret.="</div>";

		$ret.="<div class=\"zamowienie_payer\">";
		$ret.="<table cellpadding=\"0\" cellspacing=\"0\" class=\"zamowienie\">\n";
		$ret.="<tbody>";
		$ret.="
			<tr><td class=\"lpa\">".sysmsg("Payer","order")."</td><td class=\"rpa\">$su_nazwisko<br>$su_ulica<br>$su_kod_pocztowy $su_miasto<br>
			<tr><td class=\"lni\">".sysmsg("Tax Id","order")."</td><td class=\"rni\">".$su_nip."</td></tr>
			<tr><td class=\"lte\">".sysmsg("Telephone","order")."</td><td class=\"rte\">".$su_telefon."</td></tr>
			<tr><td class=\"lnia\">".sysmsg("Invoice","order")."</td><td class=\"rnia\">".( strlen($su_nip) ? sysmsg('Answer_yes','order') : sysmsg('Answer_no','order') )."</td></tr>";
		$ret.="\n</tbody>\n";
		$ret.="\n</table>\n";
		$ret.="</div>";
		$ret.="<br clear=\"all\"><br>";

		
		
		return $ret;
	}



	function produkty_zamowienia($za_id, $za_notpola='')
	{
		$sql = "SELECT * FROM zampoz
				LEFT JOIN towar_sklep ON zp_ts_id = ts_id
				LEFT JOIN towar ON to_id=ts_to_id 
				LEFT JOIN towar_parametry ON tp_to_id = to_id
				WHERE zp_za_id = $za_id  
				";
	
		if (!$za_id) return;
		
		$res = $this->execute($sql);
		if (!$res->RecordCount()) return;
		$total_quant = 0;
		$total_value = 0;
		
		#$sql = "SELECT * FROM zamowienia LEFT JOIN poczta ON za_poczta=po_id WHERE za_id = $za_id";
		$sql = "SELECT * FROM zamowienia LEFT JOIN tr_ceny ON za_poczta=tr_ceny_id WHERE za_id = $za_id";
		parse_str($this->ado_query2url($sql));
		
		#FAKRO
		#pobranie nazwy transportu przesylki
		if($tr_typ_id) {
			$sql = "SELECT * FROM tr_typ WHERE tr_typ_id = $tr_typ_id";
			parse_str($this->ado_query2url($sql));
			}
		
		if (strlen($za_data_przyjecia)) $data_sts = date("d-m-Y H:i",$za_data_przyjecia);
		if (strlen($za_data_realizacji)) $data_sts = date("d-m-Y",$za_data_realizacji);
		$total_value = 0;
		
		$ret.="<table cellpadding=\"1\" cellspacing=\"2\" class=\"zampoz\">\n";
		$ret.="<thead><tr>";
		if (!strstr($za_notpola,'lp')) $ret.="<td class=\"lp\">".sysmsg("Lp.","cart")."</td>";
		if (!strstr($za_notpola,'an')) $ret.="<td class=\"an\">".sysmsg("Article name","cart")."</td>";
		if (!strstr($za_notpola,'qu')) $ret.="<td class=\"qu\">".sysmsg("Quantity","cart")."</td>";
		if (!strstr($za_notpola,'pn')) $ret.="<td class=\"pn\">".sysmsg("Price netto","cart")."</td>";
		if (!strstr($za_notpola,'qu')) $ret.="<td class=\"pb\">".sysmsg("Gross price","cart")."</td>";
		if (!strstr($za_notpola,'vn')) $ret.="<td class=\"vn\">".sysmsg("Value netto","cart")."</td>";
		if (!strstr($za_notpola,'vb')) $ret.="<td class=\"vb\">".sysmsg("Gross value","cart")."</td>";
		$ret.="</tr>\n</thead>\n<tbody>";

		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$_zp_rabat = $zp_rabat;
			if ($zp_rabat)
			{
				$cena_o=$zp_cena/(1- $zp_rabat/100);
				$zp_rabat=round(($zp_rabat*100)/100,2)."%";
			}
			else
			{
				$zp_rabat = "0%";
				$cena_o = $zp_cena;
			}

			$parity=($i%2)?'odd':'even';

			$ret.="<tr class=\"$parity\">";
			if (strlen($to_ean)) $to_ean=" ($to_ean)";

			if (!strstr($za_notpola,'lp')) $ret.="<td class=\"lp\">".($i+1)." </td>";
			if (!strstr($za_notpola,'an')) $ret.="<td class=\"an\">".$to_indeks.$to_ean."</td>";
			if (!strstr($za_notpola,'qu')) $ret.="<td class=\"qu\">$zp_ilosc </td>";
			if (!strstr($za_notpola,"pn")) $ret.="<td class=\"pn\">".u_cena($zp_cena)." </td>";
			if (!strstr($za_notpola,'pb')) $ret.="<td class=\"pb\">".u_cena($zp_cena*(100+$to_vat)/100)." </td>";
			if (!strstr($za_notpola,'vn')) $ret.="<td class=\"vn\">".u_cena($zp_cena*$zp_ilosc)." </td>";
			if (!strstr($za_notpola,'vb')) $ret.="<td class=\"vb\">".u_cena($zp_cena*$zp_ilosc*(100+$to_vat)/100)." </td>";
			$ret.="</tr>";
			$total_value_n+= ($zp_cena*$zp_ilosc);
			$total_value_b+= ($zp_cena*$zp_ilosc*(100+$to_vat)/100);
		}
		# podatek
			$ret.="<tr>";
			if (!strstr($za_notpola,'lp')) $ret.="<td class=\"lp\">&nbsp;</td>";
			if (!strstr($za_notpola,'an')) $ret.="<td class=\"an\">".sysmsg("Tax","cart")."</td>";
			if (!strstr($za_notpola,'qu')) $ret.="<td class=\"qu\">&nbsp;</td>";
			if (!strstr($za_notpola,'pn')) $ret.="<td class=\"pn\">&nbsp;</td>";
			if (!strstr($za_notpola,'pb')) $ret.="<td class=\"pb\">&nbsp;</td>";
			if (!strstr($za_notpola,'vn')) $ret.="<td class=\"vn\">&nbsp;</td>";
			if (!strstr($za_notpola,'vb')) $ret.="<td class=\"vb\">".u_cena(($za_wart_br*$tr_strefa_vat)/100)."</td>";
			$ret.="</tr>";
		
		# transport
		if ($za_poczta_nt || true)
		{
			$ret.="<tr>";
			if (!strstr($za_notpola,'lp')) $ret.="<td class=\"lp\">&nbsp;</td>";
			#if (!strstr($za_notpola,'an')) $ret.="<td class=\"an\">".stripslashes($tr_typ_name)."</td>";
			if (!strstr($za_notpola,'an')) $ret.="<td class=\"an\">".sysmsg("Delivery cost","cart")."</td>";
			if (!strstr($za_notpola,'qu')) $ret.="<td class=\"qu\">&nbsp;</td>";
			if (!strstr($za_notpola,'pn')) $ret.="<td class=\"pn\">&nbsp;</td>";
			if (!strstr($za_notpola,'pb')) $ret.="<td class=\"pb\">&nbsp;</td>";
			if (!strstr($za_notpola,'vn')) $ret.="<td class=\"vn\">&nbsp;</td>";
			if (!strstr($za_notpola,'vb')) $ret.="<td class=\"vb\">".u_cena($za_poczta_br)."</td>";
			$ret.="</tr>";
			
			$total_value_n+= $za_poczta_nt;
			$total_value_b+= $za_poczta_br;
		}

		$ret.="\n</tbody>\n";
		$ret.="<tfoot><tr>";
		
		$total_value_n = $total_value_n+(($za_wart_nt*$tr_strefa_vat)/100);
		$total_value_b = $total_value_b+(($za_wart_br*$tr_strefa_vat)/100);
		
		if (!strstr($za_notpola,'lp')) $ret.="<td class=\"lp\">&nbsp;</td>";
		if (!strstr($za_notpola,'an')) $ret.="<td class=\"an\">".sysmsg("Summary","cart")."</td>";
		if (!strstr($za_notpola,'qu')) $ret.="<td class=\"qu\">&nbsp;</td>";
		if (!strstr($za_notpola,'pn')) $ret.="<td class=\"pn\">&nbsp;</td>";
		if (!strstr($za_notpola,'pb')) $ret.="<td class=\"pb\">&nbsp;</td>";
		if (!strstr($za_notpola,'vn')) $ret.="<td class=\"vn\">".u_cena($total_value_n)."</td>";
		if (!strstr($za_notpola,'vb')) $ret.="<td class=\"vb\">".u_cena($total_value_b)."</td>";

		$ret.="</tr>\n</tfoot>";
		$ret.="\n</table>";

		return $ret;
	}

}
?>