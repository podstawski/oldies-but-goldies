<?

function api2_koduj_url($query_string,$compress=false) 
{
  //$query_string=base64_encode($query_string);

  $md5=md5($query_string."!hdFSi3%63548dfs^æææ%%8919~~1 2558641d");
  $query_string=$md5."g".$query_string;

  if ($compress) if (function_exists("gzcompress")) $query_string=gzcompress($query_string);
  $query_string=base64_encode($query_string);

  $query_string="QS=".urlencode($query_string);
  return $query_string;
}

function api2_rozkoduj_url($query_string,$compress=false) 
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

global $_STACK_INDEX;
$_STACK_INDEX=0;

function api2_push($a0=null,$a1=null,$a2=null,$a3=null,$a4=null,$a5=null,$a6=null,$a7=null,$a8=null,$a9=null,
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
function api2_pop($a0=null,$a1=null,$a2=null,$a3=null,$a4=null,$a5=null,$a6=null,$a7=null,$a8=null,$a9=null,
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

?>