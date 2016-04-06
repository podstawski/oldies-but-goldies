<?

// GENERATOR PLIKÓW JĘZYKOWYCH by CARTMAN

$ed_lang["pl"]="Polish";
$ed_lang["en"]="English";

$lang_directory="../editor/lang/";

include "../include/const.h";


function unpolish ($str)
{
  return str_replace(array('ą','ś','ę','ć','ł','ó','ż','ź','ń','Ń','Ą','Ś','Ę','Ć','Ł','Ó','Ż','Ź'),array('a','s','e','c','l','o','z','z','n','N','A','S','E','C','L','O','Z','Z'),$str);
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$charsets=array();
foreach ($CHARSET_TAB as $key=>$charset)
{
  if (!in_array($charset,$charsets)) $charsets[]=$charset;
}

$_langi = "";
$_slangi = "";
foreach ($ed_lang as $kie=>$value)
{
  $txt = "";
  if (file_exists($lang_directory."togenerate/".$kie.".js"))
  {
    //$fh = fopen($lang_directory."togenerate/".$kie.".js", 'r');
    $txt = file_get_contents($lang_directory."togenerate/".$kie.".js");
    echo $kie." - ".strlen($txt)."<hr />";
  }
  for ($i=0;$i<sizeof($charsets);$i++)
  {
    if ($kie=="pl" && ($charsets[$i]!="UTF-8" && $charsets[$i]!="ISO-8859-2"))
    {
      //echo unpolish($txt);
      $newtxt = iconv("UTF-8",$charsets[$i],str_replace("CKEDITOR.lang['".$kie."']","CKEDITOR.lang['".$kie."_".strtolower($charsets[$i])."']",unpolish($txt)));    
    }  
    else
      $newtxt = iconv("UTF-8",$charsets[$i],str_replace("CKEDITOR.lang['".$kie."']","CKEDITOR.lang['".$kie."_".strtolower($charsets[$i])."']",$txt));
    $_slangi.="'".$kie."_".strtolower($charsets[$i])."':1,";
    echo $charsets[$i]." : ".strlen($newtxt)."<br />";
    $log = fopen($lang_directory.$kie."_".strtolower($charsets[$i]).".js","w+");
    fwrite($log, $newtxt);
    fclose($log); 
    $_langi.=$kie.":'".$value."(".$charsets[$i].")',";
  }
  echo "<br />";
}

// generowanie pliku
$_langi = substr($_langi,0,-1);
$_langi = "var CKEDITOR_LANGS=(function(){var b={".$_langi."},c=[];for(var d in b)c.push({code:d,name:b[d]});c.sort(function(e,f){return e.name<f.name?-1:1;});return c;})();";
$log = fopen($lang_directory."_languages.js","w+");
fwrite($log, $_langi);
fclose($log); 
echo "ckeditor.js:<br />".$_slangi;
?>
