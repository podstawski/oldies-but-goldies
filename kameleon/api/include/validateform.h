<?
if ($VALIDATEFORM==1) return;
else
 $VALIDATEFORM=1;

function validateText($html)
{
  $html=stripslashes($html);
  //usun znaczniki html
  $html=ereg_replace("<[^>]+>","",$html);
  //usun wszystkie niepotrzebne znaki i zostaw te które maj¹ sens
  $html=ereg_replace("[^a-z|A-Z|0-9| |_|\"|\.|\,|\n|\/|¹œ¿Ÿæêñó³|¥Œ¯ÆÊÑÓ£|@|±¶¼¡¦¬:;()[]!$%*&?]","",$html);
  return $html;
}
function validateDate($data)
{
  if ( ereg("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $data, $pola)) 
  {
   $dd=0+$pola[1];
   $mm=0+$pola[2];
   $rr=0+$pola[3];
   return checkdate($mm,$dd,$rr);
  }
  else
   return 0;
}

function validateURL($url)
{
 if (strlen($url)>0)
  return ereg("(http://[a-z|A-Z]+)\.([a-z|A-Z|\.]+)",$url,$pola);
 return 1;
}

function validateEmail($email)
{
 $EREG = "[a-z]|[A-Z]|[0-9]|[._-]";
 if (strlen($email)==0) return 1;
 $arr = explode("@", $email);
 if (sizeof($arr) != 2)
 return 0;
 $login = $arr[0];
 $hostname = $arr[1];
 $reg = "^(".$EREG."){".strlen($login)."}$";
 if (!ereg($reg, $login)) return 0;

 $hostname_parts = explode(".", $hostname);
 if (sizeof($hostname_parts)<2) return 0;

 $reg = "^(".$EREG."){".strlen($hostname)."}$";
 if (!ereg($reg, $hostname)) return 0;

 if (function_exists('getmxrr'))
	 if (!getmxrr($hostname, $tmp))
		if (gethostbyname($hostname)==$hostname) return 0;

 return 1;
}
?>