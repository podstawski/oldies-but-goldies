<?php
	include_once('../const.iso2utf.php');

	if ($argc<2) die("Usage: $argv[0] dest_db_name[@[dest_db_host]:[dest_db_port]] [table] [table] ...\n");


	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	
	if (isset($CONST_SET_CLIENT_ENCOGING)) $adodb->adodb->SetCharSet($CONST_SET_CLIENT_ENCOGING);
	
	$name=explode('@',$argv[1]);

	$C_DB_CONNECT_DBNAME=$name[0];

	$host=explode(':',$name[1]);

	$org_host=explode(':',$C_DB_CONNECT_HOST);

	if (!strlen($host[0])) $host[0]=$org_host[0];
	if (!strlen($host[1])) $host[1]=$org_host[1];

	$C_DB_CONNECT_HOST=$host[0];
	if (strlen($host[1])) $C_DB_CONNECT_HOST.=':'.$host[1];


	$adodb2=new KDB($C_DB_CONNECT_DBTYPE,$persistant_connection, 
				$C_DB_CONNECT_HOST, $C_DB_CONNECT_USER, $C_DB_CONNECT_PASSWORD, $C_DB_CONNECT_DBNAME,$DEBUG_IP);


	$adodb2->adodb->SetCharSet('UTF8');

	function _iconv($from,$to,$txt)
	{
		if (is_array($txt))
		{
			$ret=array();
			foreach($txt AS $k=>$v) $ret[iconv($from,$to,$k)]=_iconv($from,$to,$v);
		}
		if (is_object($txt))
		{
			$ret=new stdClass;
			foreach($txt AS $k=>$v)
			{
				$kk=iconv($from,$to,$k);
				$ret->$kk=_iconv($from,$to,$v);
			}
		}
		
		if(!isset($ret)) $ret=iconv($from,$to,$txt);
		
		return $ret;
	}


	function url2set($url,$____table,$charset=null)
	{
		$set='';
		foreach (explode('&',$url) AS $u)
		{
			$_u=explode('=',$u);
			$k=stripslashes(urldecode($_u[0]));
			$v=stripslashes(urldecode($_u[1]));


			if ($charset)
			{
			
				if ($____table=='webtd' && $k=='xml')
				{
					$_v=unserialize($v);
					if (is_array($_v) || is_object($v))
					{
						$newv=serialize(_iconv($charset,"UTF-8",$_v));
					}
					else
						$newv=iconv($charset,"UTF-8",$v);
				}
				else
					$newv=iconv($charset,"UTF-8",$v);

			}
			else $newv=$v;


			if (strlen($set)) $set.=',';
			if (strlen($newv) || $k=='a_afiliacja_c') $set.="'".addslashes($newv)."'";
			else $set.="NULL";
		}
	
		return $set;	
	}

	function kameleon_conv_update_chars($table,$url)
	{
		global $adodb,$adodb2;
		

		$CHARSET_TAB=array(	"p"=>"Windows-1250",
					"i"=>"ISO-8859-2",
					"r"=>"ISO-8859-5",
					"e"=>"ISO-8859-1",
					"f"=>"ISO-8859-1",
					"d"=>"ISO-8859-1",
					"s"=>"ISO-8859-1",
					"l"=>"ISO-8859-13",
					"y"=>"ISO-8859-2",
					"h"=>"ISO-8859-2",
					"t"=>"ISO-8859-2",
					"bu"=>"ISO-8859-5",
					"g"=>"ISO-8859-7",
					"t"=>"ISO-8859-9",
					"fr"=>"ISO-8859-1",
					"pl"=>"ISO-8859-2",
					"cz"=>"ISO-8859-2",
					"hu"=>"ISO-8859-2",
					"lt"=>"ISO-8859-2",
					"by"=>"ISO-8859-5",
					"bg"=>"ISO-8859-5",
					"nl"=>"ISO-8859-1",
					"en"=>"ISO-8859-1",
					"ru"=>"ISO-8859-5",
					"de"=>"ISO-8859-1",
					"no"=>"ISO-8859-1",
					"ro"=>"ISO-8859-2",
					""=>"ISO-8859-2");

		
		echo "Zmiana $table ... ";
		flush();
		$licznik=0;
		$_errors=0;

		if (!strlen($url))
		{
			echo "pomiajam - pusta\n";
			return;
		}

		$sql="SELECT count(*) AS c FROM $table";
		$res=$adodb2->execute($sql);

		parse_str($adodb2->ado_explodename($res,0));

		
		//$adodb2->BeginTrans();
		
		if ($c)
		{
			$sql="TRUNCATE $table CASCADE";
			$adodb2->execute($sql);

			//echo "pomiajam - dane sa w tablicy dest\n";
			//return;
		}

		$skhdfjkdgfjghsdjfhgakdfgdagfjhgsdjfg=$table;
		


		$query="SELECT * FROM $table ";
		$res=$adodb->execute($query);

		
		

		$________ilosc=$res->recordcount();
		for($i=0;$i<$________ilosc;$i++)
		{
			
			$url=$adodb->ado_explodename($res,$i);
			$lang='';
			parse_str($url);


			$set=url2set($url,$skhdfjkdgfjghsdjfhgakdfgdagfjhgsdjfg);
			$sql="INSERT INTO $skhdfjkdgfjghsdjfhgakdfgdagfjhgsdjfg \nVALUES ($set)";
			
			if (!$adodb2->execute($sql))
			{
				$charset=$CHARSET_TAB[$lang];
				if (!strlen($charset)) $charset="ISO-8859-2";				
				$set=url2set($url,$skhdfjkdgfjghsdjfhgakdfgdagfjhgsdjfg,$charset);
				$sql="INSERT INTO $skhdfjkdgfjghsdjfhgakdfgdagfjhgsdjfg \nVALUES ($set)";
			}
			else
			{
				$_ilosc2++;
				continue;
			}
			
			if (!$adodb2->execute($sql))
			{
				//$adodb2->RollbackTrans();
				$adodb2->debug=true;
				$adodb2->execute($sql);
				$adodb2->debug=false;
				$_errors++;
				continue;
			}
			$licznik++;
			
		}


		$licznik=number_format($licznik,0,",",".");
		$_ilosc2=number_format($_ilosc2,0,",",".");
		echo "$_ilosc2/$licznik/$_errors rek.\n";

		$adodb2->CommitTrans();
	}

	$tables=array();
	$exclude=array();
	for ($i=2;$i<$argc;$i++) 
	{
		if (substr($argv[$i],0,1)=='!' || substr($argv[$i],0,1)=='~') $exclude[]=substr($argv[$i],1);
		else $tables[]=$argv[$i];
	}


	

	$query="SELECT * FROM pg_tables WHERE tablename NOT LIKE 'pg_%' AND tablename NOT LIKE 'sql_%' ORDER BY tablename";
	$res=$adodb->execute($query);

	$ilosc=$res->recordcount();
	for($i=0;$i<$ilosc;$i++)	
	{
		$url=$adodb->ado_explodename($res,$i);
		parse_str($url);

		if (in_array($tablename,$exclude)) 
		{
			continue;
		}


		if (count($tables) && !in_array($tablename,$tables)) 
		{
			continue;
		}

		$sql="SELECT * FROM $tablename LIMIT 1";
		$url=ado_query2url($sql);

	
		kameleon_conv_update_chars($tablename,$url);
	}

	
	$sql="
                update webpage set lang='pl' where lang='i';
                update webpage set lang='en' where lang='e';
                update weblink set lang='pl' where lang='i';
                update weblink set lang='en' where lang='e';
                update weblink set lang_target='pl' where lang_target='i';
                update weblink set lang_target='en' where lang_target='e';
                update webtd set lang='pl' where lang='i';
                update webtd set lang='en' where lang='e';
                update servers set lang='pl' where lang='i';
                update servers set lang='en' where lang='e';
	
	";
	$adodb2->execute($sql);
	
	


	
