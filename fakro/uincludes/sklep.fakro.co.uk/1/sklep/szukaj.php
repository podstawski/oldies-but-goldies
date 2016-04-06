<?

$LIST[szukaj]=ereg_replace("\"","&quot;",stripslashes($LIST[szukaj]));

$szu=sysmsg("search","system");

if (!strlen($LIST[szukaj])) $LIST[szukaj]=$szu;
$method=$KAMELEON_MODE?"post":"get";

$go=$next;
if ($cos) if (strstr($tree,":997:") || $page==997) $go=$more;

$szukaj_value=


$szukaj = "<form action=\"$go\" method=\"$method\" onSubmit=\"return szukajSubmit(this)\" style=\" margin:0px\">";
$szukaj.= "<table cellpadding=\"0\" cellspacing=\"0\" class=\"inc\" style=\"margin-top: 3px;\"><tr>";
$szukaj.= "<td><input type=\"Text\" name=\"list[szukaj]\" id=\"szukaj\" class=\"in\" value=\"$LIST[szukaj]\" onClick=\"szukajOnClick(this)\" onBlur=\"szukajOnBlur(this)\"></td>";
$szukaj.= "<td><input type=\"image\" src=\"$SKLEP_IMAGES/i_szukaj_but.gif\" class=\"sb\"></td>";
$szukaj.= "</tr></table>";
$szukaj.= "</form>";

echo $szukaj;
if ($LIST[szukaj]==$szu) $LIST[szukaj]="";
$LIST[szukaj]=ereg_replace("&quot;","\"",$LIST[szukaj]);
for ($i=0;$i<10;$i++)
{
	$new_string=ereg_replace("\"([^\" ]+)[ ]+([^\"]+)\"","\"\\1&nbsp;\\2\"",$LIST[szukaj]);
	if ($new_string==$LIST[szukaj]) 
	{
		$i=10;
		$new_string=ereg_replace("\"([^\"]+)\"","\\1",$new_string);
	}
	$LIST[szukaj]=$new_string;
	//echo str_replace("&nbsp;","___",$LIST[szukaj])."<br>";	
}

$LIST[szukaj]=ereg_replace("([^ ])\+","\\1 +",$LIST[szukaj]);

$_REQUEST["list"]=$LIST;


?>

<script>
	function szukajOnClick(obj)
	{
		if (obj.value=='<?echo $szu?>') obj.value='';
	}

	function szukajOnBlur(obj)
	{
		if (obj.value.length == 0) obj.value='<?echo $szu?>';
	}

	function szukajSubmit(obj)
	{
		if (obj.szukaj.value == '<?echo $szu?>' || obj.szukaj.value.length == 0)
			return false;
		return true;
	}
</script>
