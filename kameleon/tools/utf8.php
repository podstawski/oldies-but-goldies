<?php

	function kameleon_this2iso($f_text)
	{
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		
		return $f_text;
	}


	function kameleon_iso2this($f_text)
	{
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');
		$f_text = strtr($f_text, '�', '�');

		return $f_text;
	}


	function kameleon_utf82iso88592($tekscik) 
	{
		 $tekscik = str_replace("\xC4\x85", "�", $tekscik);
		 $tekscik = str_replace("\xC4\x84", '�', $tekscik);
		 $tekscik = str_replace("\xC4\x87", '�', $tekscik);
		 $tekscik = str_replace("\xC4\x86", '�', $tekscik);
		 $tekscik = str_replace("\xC4\x99", '�', $tekscik);
		 $tekscik = str_replace("\xC4\x98", '�', $tekscik);
		 $tekscik = str_replace("\xC5\x82", '�', $tekscik);
		 $tekscik = str_replace("\xC5\x81", '�', $tekscik);
		 $tekscik = str_replace("\xC5\x84", '�', $tekscik);    
		 $tekscik = str_replace("\xC5\x83", '�', $tekscik);
		 $tekscik = str_replace("\xC3\xB3", '�', $tekscik);
		 $tekscik = str_replace("\xC3\x93", '�', $tekscik);
		 $tekscik = str_replace("\xC5\x9B", '�', $tekscik);
		 $tekscik = str_replace("\xC5\x9A", '�', $tekscik);
		 $tekscik = str_replace("\xC5\xBC", '�', $tekscik);
		 $tekscik = str_replace("\xC5\xBB", '�', $tekscik);
		 $tekscik = str_replace("\xC5\xBA", '�', $tekscik);
		 $tekscik = str_replace("\xC5\xB9", '�', $tekscik);
		 $tekscik = str_replace("”",'&#147;', $tekscik);
		 return $tekscik;
	} 


	function kameleon_iso885922utf8($tekscik) 
	{
		//return unPolish($tekscik);
	  $iso88592 = array(
	   '', '', '', '', '', '', '', '', '', '',
	   '', '', '', '', '', '', '', '', '', '',
	   '', '', '', '', '', '', '', '', '', '',
	   '', '', '� ', 'Ą', '˘', 'Ł', '¤', 'Ľ', 'Ś', '§',
	   '¨', '� ', 'Ş', 'Ť', 'Ź', '­', 'Ž', 'Ż', '°', 'ą',
	   '˛', 'ł', '´', 'ľ', 'ś', 'ˇ', '¸', 'š', 'ş', 'ť',
	   'ź', '˝', 'ž', 'ż', 'Ŕ', 'Á', 'Â', 'Ă', 'Ä', 'Ĺ',
	   'Ć', 'Ç', 'Č', 'É', 'Ę', 'Ë', 'Ě', 'Í', 'Î', 'Ď',
	   'Đ', 'Ń', 'Ň', 'Ó', 'Ô', 'Ő', 'Ö', '×', 'Ř', 'Ů',
	   'Ú', 'Ű', 'Ü', 'Ý', 'Ţ', 'ß', 'ŕ', 'á', 'â', 'ă',
	   'ä', 'ĺ', 'ć', 'ç', 'č', 'é', 'ę', 'ë', 'ě', 'í',
	   'î', 'ď', 'đ', 'ń', 'ň', 'ó', 'ô', 'ő', 'ö', '÷',
	   'ř', 'ů', 'ú', 'ű', 'ü', 'ý', 'ţ', '˙');
	  return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $tekscik);
	} 

	function kameleon_arr2utf8(&$arr)
	{
		if (!is_array($arr)) 
		{
			$arr=kameleon_iso885922utf8($arr);
			return;
		}
		while(list($k,$v)=each($arr))
		{
			kameleon_arr2utf8(&$v);
			$arr[$k]=$v;
		}
	}


	function kameleon_update_chars($table,$index,$where_array)
	{
		global $adodb;
		
		$where='';
		foreach ($where_array AS $k=>$v) $where.=' AND '.$k.'='.$v;
		

		$query="SELECT * FROM $table ".eregi_replace('^AND','WHERE',trim($where));

		$res=$adodb->execute($query);

		for($i=0;$i<$res->recordcount();$i++)
		{
			$url=ado_explodename($res,$i);

			$set='';
			$where="WHERE $index=";

			foreach (explode('&',$url) AS $u)
			{
				$_u=explode('=',$u);
				$k=stripslashes(urldecode($_u[0]));
				$v=stripslashes(urldecode($_u[1]));

				if ($k==$index)
				{
					$where.=$v;
					continue;
				}
	
				if (strlen($v)<3) continue;
				if (is_integer($v)) continue;

				$newv=kameleon_iso885922utf8($v);

				if ($newv==$v) continue;


				if (strlen($set)) $set.=',';
				$set.=$k.'='."'".addslashes($newv)."'";


			}
			if (!strlen($set)) continue;

			$sql="UPDATE $table SET $set $where";
			$adodb->execute($sql);
		}

	}




	function usage($me)
	{
		echo "$me  lang=yy [server=xx ver=vv]\n";
		exit ();
	}




	error_reporting(7);

	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');
	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");

	for ($i=1;$i<$argc;$i++)
	{
		parse_str($argv[$i]);
	}


	if (strlen($server) && !is_integer($server))
	{
		$query="SELECT id AS server FROM servers WHERE nazwa='$server'";
		parse_str(ado_query2url($query));
	}

	if (!strlen($lang)) usage($argv[0]);

	if (!$server) 
	{
		kameleon_update_chars('label','id',array('lang'=>"'$lang'"));
	}
	else
	{
		if (!strlen($ver)) usage($argv[0]);
		kameleon_update_chars('webpage','sid',array('server'=>$server,'ver'=>$ver,'lang'=>"'$lang'"));
		kameleon_update_chars('webtd','sid',array('server'=>$server,'ver'=>$ver,'lang'=>"'$lang'"));
		kameleon_update_chars('weblink','sid',array('server'=>$server,'ver'=>$ver,'lang'=>"'$lang'"));
	}

	
