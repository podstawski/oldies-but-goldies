<?
if (!strlen($page)) return;

$api_mode+=0;

$numdigits=5;
$IMAGES="$API_SERVER/images/liczniki";

if ($api_em)
{

	$sql="SELECT * FROM counter WHERE servername='$KEY' AND page=$page";
	$result=$adodb->Execute($sql);
	if ($result->RecordCount())
		parse_str(ado_ExplodeName($result,0));
	$imgdir="";$numdigits="";
	parse_str($params);

	$katalog="images/liczniki";
	$handle=opendir("$katalog");
	while (($file = readdir($handle)) !== false) 
	{
		if ($file=="." || $file=="..") continue;
		clearstatcache();
		if (is_file("$katalog/$file"))
			$pliki[]=$file;
		else
			$katalogi[]=$file;
	}
	closedir($handle); 

	$dirlen=count($katalogi);
	if ($dirlen>1) sort($katalogi);

	$digits=0+$numdigits;
	$count=1234567890;
	$wynik="
		<fieldset style=\"width:99%; margin-left:2px; \">
		<legend style='cursor:pointer' onclick=\"document.getElementById('counter_settings').style.display=''\" >".label('Counter settings')."</legend>
		<table bgcolor='orange' cellspacing=1 style='display:none' id='counter_settings'>
		<form method=post name=api_counter  action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>";
	for ($d=0;$d<$dirlen;$d++)
	{
		$img="$IMAGES/".$katalogi[$d];
		if ($imgdir==$katalogi[$d])
			$checked="checked";
		else
			$checked="";
		$dig=1+floor(Log10($count));
		if ($digits<$dig) $digits=$dig;
		$licznik="";
		for($i=$digits;$i;$i--)
		{
			$digit=floor(($count%pow(10,$i))/pow(10,$i-1));
			$licznik.="<img border=0 src=\"$img/$digit.gif\">";
		}
		$wynik.="
			<tr>
				<td align='center' bgcolor='white'><input type='radio' name='imgdir' value='$katalogi[$d]' $checked></td>
				<td bgcolor='white'>$licznik</td>
			</tr>";
	}
	if ($imgdir=="")
		$checked="checked";
	else
		$checked="";
	$wynik.="
		<tr>
			<td align='center' bgcolor='white'><input type='radio' name='imgdir' value='' $checked></td>
			<td bgcolor='white'>$count</td>
		</tr>
		<tr>
			<td align='center' bgcolor='white'><input type='checkbox' name='counter_reset' value='1'></td>
			<td bgcolor='white'>".label("api_reset_counter")."</td>
		</tr>
		<tr>
			<td align='center' bgcolor='white'><input class='api_counter_input' type='text' name='numdigits' value='$numdigits' size='2'></td>
			<td bgcolor='white'>".label("api_digits")."</td>
		</tr>
		<tr>
			<td colspan=2 align=right bgcolor='white'>
			<input type='submit' class='k_button' value='".label("Save")."' onClick=\"document.api_counter.api_action.value='apiZapiszCounter'\">
			</td>
		</tr>
		</table></form>
		</fieldset>
		<br/>&nbsp;			
		";
	echo $wynik;
}
else
{
	$sql="SELECT * FROM counter WHERE servername='$KEY' AND page=$page";
	$result=$adodb->Execute($sql);
	if ($result->RecordCount())
	{
		parse_str(ado_ExplodeName($result,0));
		$imgdir="";$numdigits="";
		parse_str($params);
		$count++;
		$sql="UPDATE counter SET count=$count WHERE servername='$KEY' AND page=$page";
	}
	else
	{
		$count=1;
		$sql="INSERT INTO counter (page,count,servername) VALUES ($page,1,'$KEY')";
	}

	$adodb->Execute($sql);

	if (strlen($imgdir))
	{
		$img="$IMAGES/$imgdir";
		$wynik="";
		$dig=1+floor(Log10($count));
		if ($numdigits<$dig) $numdigits=$dig;
		for($i=$numdigits;$i;$i--)
		{
			$digit=floor(($count%pow(10,$i))/pow(10,$i-1));
			$wynik.="<img border=0 src=\"$img/$digit.gif\">";
		}
	}
	else
	{
		if ( 0+$numdigits>0 )
			$patern="%0".$numdigits."d";
		else
			$patern="%d";
		$wynik=sprintf($patern,$count);
	}
	echo $wynik;
}
?>
