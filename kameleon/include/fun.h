<?
/*
if ($FUN_H_INCLUDED) // RP. 11-11-2002 15:55
	return;
else
	$FUN_H_INCLUDED=1;
*/



if (function_exists("CreateFormField")) return;


function CreateFormField($field)
{
 
  $label=$field[0];
  $size=$field[1];
  $type=$field[2];
  $name=$field[3];
  $value=$field[4];
  $options=$field[5];
 
  switch ($type) 
  {
   case "text":
    $input="<input type=$type name='$name' class=k_$type value='$value' size='$size'>";
    break;
   case "hidden":
    $input="<input type=$type name='$name' value='$value' size='$size'>$value";
    break;
   case "password":
    $input="<input type=$type name='$name'  class=k_input value='$value' size='$size'>";
    break;
   case "textarea":
    $size=explode(",",$size);
    $rows=$size[0];
    $cols=$size[1];
    $input="<textarea name='$name' class=k_$type rows='$rows' cols='$cols'>$value</textarea>";
    break;

   case "file":
    $input="<input type=$type name='$name' class=k_$type size='$size'>";
    break;

   case "select":
	$input="<select type=$type class=k_$type name='$name' size='$size'>";
	for ($s=0;$s<count($options);$s++)
	{
		$v=$options[$s];
		$selected=($v[0]==$value)?"selected":"";
		$input.="<option $selected value='$v[0]'>$v[1]";
	}
	$input.="</select>";
	
  }

 return("$label$input");
}



function toText($s)
{
	$s=addslashes(stripslashes($s));

	return($s);
}


function FormatujDate ($d, $formatdaty = 'd-m-Y', $time = false )
{
	if (strpos($d, '-') > 0 ) 
	{
		return substr($d,8,2)."-".substr($d,5,2)."-".substr($d,0,4);
	}

	if (empty($formatdaty))
	{
		if ( $time == true )
		{
			$formatdaty = $formatdaty . ', H:i';
		}
	}

	return date ($formatdaty, $d);
	//
	//return substr($d,3,2)."-".substr($d,0,2)."-".substr($d,6,4);
}

function FormatujDateSQL ($d)
{
	//Date format can be: dd-mm-YYYY, or d-m-YYYY like 01-01-2005 or 1-1-2005
	//Date digits separator can be: "-", " ", "/", "."

	$rexp_wtime = '/0?([1-9]|[12][0-9]|3[01])[ -\/.]0?([1-9]|1[012])[ -\/.](19|20\d\d)[ ](\d\d?)[:](\d\d?)/s';
	$rexp_dt = '/0?([1-9]|[12][0-9]|3[01])[ -\/.]0?([1-9]|1[012])[ -\/.](19|20\d\d)/s';
	
	$rexp = (strpos($d, ':') !== false) ? $rexp_wtime : $rexp_dt;

	preg_match_all($rexp, $d, $digits);

	$month = $digits[2][0];
	$day = $digits[1][0];
	$year = $digits[3][0];

	$hour = $digits[4][0]+0;
	$minute = $digits[5][0]+0;
	
	$unixTime = mktime($hour, $minute, 0, $month, $day, $year);
	return $unixTime;
}


function FormatujDatePgSQL ($d)
{
	return substr($d,6,4)."-".substr($d,3,2)."-".substr($d,0,2);
}


function u_Cena ($c,$waluta='zł') 
{
  return number_format($c,2,","," "). " $waluta";
}
  

function u_CenaWaluta ($c,$waluta) 
{
  return u_Cena ($c,$waluta);
}



function key_crypt($str,$key)
{

   $str=str_replace("-","",$str);

   $wynik="";
   for ($i=0;$i<strlen($str);$i+=2)
   {
	$s1=substr($str,$i,2);
	$s2=substr($key,$i,2);

	$i1=hexdec($s1);
	$i2=hexdec($s2);

	$wynik.=sprintf("%02X",$i1 ^ $i2);

   }

   return($wynik);
}

function key_split($key,$step)
{

   $wynik=substr($key,0,$step);
   for ($i=$step;$i<strlen($key);$i+=$step)
   {
	$wynik.="-".substr($key,$i,$step);
   }

   return($wynik);
}


function query2url($query)
{
	global $db;

	$result=pg_Exec($db,$query);
	if ( pg_numRows($result)!=1 ) return "";

	$data=pg_fetch_row($result,0);
	$wynik="";
	for ($i=0;$i<count($data);$i++)
	{	
		if ($i) $wynik.="&";
		$wynik.=pg_fieldname($result,$i)."=".urlencode(trim($data[$i]));
	}
	return $wynik;
}

function pg_ExplodeName ($result,$row)
{
 $text="";
 $cols=pg_NumFields($result);
 for ($i=0;$i<$cols;$i++)
 {
  $name=pg_FieldName($result,$i);
  $data=pg_fetch_row($result,$row);
  $value=urlencode(trim($data[$i]));
  $text.="$name=$value";
  if ($i!=$cols-1)
   $text.="&";
 }
 return $text;
}


function pg_ObjectArray($db,$query)
{
	$wynik="";
	$result=pg_Exec($db,$query);
	
	$cols=pg_NumFields($result);
	for ($j=0;$j<$cols;$j++) $pola[]=pg_FieldName($result,$j);
	for ($i=0;$i<pg_NumRows($result);$i++)
	{
		$obj=pg_Fetch_Object($result,$i);
		for ($j=0;$j<$cols;$j++) $obj->$pola[$j]=trim($obj->$pola[$j]);
		$wynik[]=$obj;
	}
	return($wynik);
}

function koduj_url($query_string,$compress=false) 
{
  //$query_string=base64_encode($query_string);

  $md5=md5($query_string."!hdFSi3%63548dfs^æææ%%8919~~1 2558641d");
  $query_string=$md5."g".$query_string;

  if ($compress) if (function_exists("gzcompress")) $query_string=gzcompress($query_string);
  $query_string=base64_encode($query_string);

  $query_string="QS=".urlencode($query_string);
  return $query_string;
}

function rozkoduj_url($query_string,$compress=false) 
{
  $query_string=urldecode($query_string);


  $query_string=base64_decode($query_string);
  	
  if ($compress) if (function_exists("gzuncompress")) $query_string=gzuncompress($query_string);

  $md5=substr($query_string,0, strpos($query_string,"g"));
  $query_string=substr($query_string, strpos($query_string,"g")+1, strlen($query_string));
  $rmd5=md5($query_string."!hdFSi3%63548dfs^æææ%%8919~~1 2558641d");
  if ($md5<>$rmd5)
  {
    return "";
  }

  
  
  return $query_string;
}

if (isset($QS)) 
{
  $p=rozkoduj_url($QS);
  if (strlen($p)>0) parse_str($p);
  else $URLERR=1;
}  





function chkEmail($email)
{
        $EREG = "[a-z]|[A-Z]|[0-9]|[._-]";

        if (!strlen($email))
                return 0;

        $arr = explode("@", $email);

        if (sizeof($arr) != 2)
                return 0;

        $login = $arr[0];
        $hostname = $arr[1];

        $reg = "^(".$EREG."){".strlen($login)."}$";
        if (!ereg($reg, $login))
                return 0;

        $hostname_parts = explode(".", $hostname);
        if (sizeof($hostname_parts)<2)
                return 0;

	$reg = "^(".$EREG."){".strlen($hostname)."}$";
        if (!ereg($reg, $hostname))
                return 0;

        if (!getmxrr($hostname, $tmp))
		if (gethostbyname($hostname)==$hostname)
                	return 0;

        return 1;
}

global $_STACK_INDEX;
$_STACK_INDEX=0;

function push($a0=null,$a1=null,$a2=null,$a3=null,$a4=null,$a5=null,$a6=null,$a7=null,$a8=null,$a9=null,
		$a10=null,$a11=null,$a12=null,$a13=null,$a14=null,$a15=null,$a16=null,$a17=null,$a18=null,$a19=null)
{
	global $_STACK_INDEX,$_STACK_ARR;
	$_STACK_INDEX+=0;

	for ($i=0;$i<func_num_args();$i++)
	{
		eval(" \$_STACK_ARR[\$_STACK_INDEX] = \$a$i ; "); 
		$_STACK_INDEX++;
	}
}
function pop($a0=null,$a1=null,$a2=null,$a3=null,$a4=null,$a5=null,$a6=null,$a7=null,$a8=null,$a9=null,
		$a10=null,$a11=null,$a12=null,$a13=null,$a14=null,$a15=null,$a16=null,$a17=null,$a18=null,$a19=null)
{
	global $_STACK_INDEX,$_STACK_ARR;
	$_STACK_INDEX+=0;

	
	for ($i=func_num_args()-1;$i>=0;$i--)
	{
		$_STACK_INDEX--;
		eval(" \$a$i = \$_STACK_ARR[\$_STACK_INDEX] ; "); 

	}
	if (!func_num_args()) $_STACK_INDEX--;

	return $_STACK_ARR[$_STACK_INDEX];
}
