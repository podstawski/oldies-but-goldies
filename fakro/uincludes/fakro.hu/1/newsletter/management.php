<?
if (!$KAMELEON_MODE) return;
global $WEBPAGE;
global $mailing_exclude, $mailing_action, $multi_mail;

include(dirname(__FILE__)."/sendmail.php");
include(dirname(__FILE__)."/winiso.php");



function fetch_file($f)
{
	$plik=fopen($f,'r');
	while (strlen($data=fread($plik,1000))) $wynik.=$data;
	fclose($plik);
	return $wynik;
}

function html_chunk_split($txt)
{
	$tag=0;
	$c=0;
	for ($i=0;$i<strlen($txt);$i++)
	{
		if ($txt[$i]=='<') $tag=1;
		if ($txt[$i]=='>') $tag=0;
		if ($c>40 && $txt[$i]==' ' && !$tag) 
		{
			$txt[$i]="\n";
			$c=0;
		}

		if (!$tag) $c++;
	}

	return $txt;
}


function html2sp2($html,&$img,$dir="")
{

	$delim='askjdhfjkashdfashkdjfjkasdhf';
	$h=eregi_replace('<img[^>]+src=[\'\"]*([^\"\' ]+)',"$delim\\1$delim",$html);
	$h=eregi_replace('<[^>]+background=[\'\"]*([^\"\' ]+)',"$delim\\1$delim",$h);
	$h=eregi_replace('background-image:[ ]*url\(([^\)]+)\)',"$delim\\1$delim",$h);

	while ($pos=strpos($h,$delim))
	{
		$h=substr($h,$pos+strlen($delim));
		$pos=strpos($h,$delim);
		$i=substr($h,0,$pos);
		$k=0;
		$k=explode('.',$i);
		$ext=strtolower($k[count($k)-1]);
		if ($ext=='jpg') $ext='jpeg';
		//echo "<br><b>$i</b>";
		$f=fetch_file($i);
		$obraz=chunk_split(base64_encode($f));
		$name=basename($i);
		if (strlen($dir) && file_exists($dir))
		{
			$file=fopen("$dir/$name","w");
			fwrite($file,$f);
			fclose($file);
		}
		$imd5=md5($i);
		$img[$imd5]="Content-Type: image/$ext;\n  name=\"$name\"\nContent-Transfer-Encoding: base64\n";
		$img[$imd5].="Content-ID: <$imd5>\n\n".$obraz;

		if (strlen($dir) && file_exists($dir))
			$html=str_replace($i,$name,$html);
		else
			$html=str_replace($i,"cid:$imd5",$html);
		$h=substr($h,$pos+strlen($delim));
	}

	if (strlen($dir) && file_exists($dir))
	{
		$file=fopen("$dir/index.html","w");
		fwrite($file,$html);
		fclose($file);
	}

	return $html;
}

push($adodb);
$adodb=$kameleon_adodb;


$query="SELECT costxt AS destination_server
		FROM webtd WHERE page_id=".$WEBPAGE->prev." AND html ~ 'mailmanage.php' 
		AND server=$SERVER_ID AND ver=$ver AND lang='$lang'";
parse_str(ado_query2url($query));

if (!strlen($destination_server))
{
	echo "Nieokreslony serwer wynikowy, <a href='index.php?page=".$WEBPAGE->prev."&seteditmode=1'>wpisz go tu</a> ";
	return;

}

echo "<div style='background-color:#f0f0f0; padding:5px; font-size:12px'>";

$query="SELECT c_email2,count(*) AS ile_osob FROM crm_customer
		WHERE c_email IS NOT NULL AND c_email <>'' 
		AND c_email LIKE '%@%'
		GROUP BY c_email2 ORDER BY c_email2";

$gr_res=$adodb->execute($query);

for ($g=0;$g<$gr_res->recordCount();$g++)
{
	parse_str(ado_ExplodeName($gr_res,$g));
	$sel=$mailing_exclude[$c_email2]?"checked":"";
	$form.="<input $sel type=\"checkbox\" name=\"mailing_exclude[$c_email2]\" value=1>";
	$form.=" wyłącz grupę <b>$c_email2</b> ($ile_osob)<br>";
}

$exclude_warunek='';
if (is_array($mailing_exclude) )
	while ( list( $key, $val ) = each( $mailing_exclude )	)
		$exclude_warunek.=" AND c_email2<>'$key'";

$query="SELECT count(*) AS ile_osob FROM crm_customer
		WHERE c_email IS NOT NULL AND c_email <>'' 
		AND c_email LIKE '%@%' $exclude_warunek";
parse_str(ado_query2url($query));

$from=htmlspecialchars($WEBPAGE->pagekey);
$form.="<br>Od: $from .... do $ile_osob osob.";
?>

<form action="<?echo $self?>" method="post" name="mailing">
<input type=hidden name="page" value="<?echo $page?>">
<?echo $form?>

<br><br>

<input type=hidden name="mailing_action" value=0>
<input type="button" value="Tylko do <? echo $from?>"
	onClick="document.mailing.mailing_action.value=1;submit()">
<input type="button" value="Newsletter do <? echo $ile_osob?> osob" 
	onClick="document.mailing.mailing_action.value=2; potwierdz_mailing()">

<br><br>LUB PROSZĘ WKLEIĆ ADRESY:<br>
<textarea name="multi_mail" style="height:100px;width:100%"><?echo $multi_mail?></textarea>
<input type="button" value="Do wklejonej grupy"
	onClick="document.mailing.mailing_action.value=3;submit()">

</form>

<script>
	function potwierdz_mailing()
	{
		if (confirm("Czy jestes pewny, ze chcesz wysłać do tylu osób ?"))
		{
			document.mailing.submit();
		}
	}
</script>

<?

$tmpmsg="/var/tmp/strona_do_wydruku.".time();
$tmpaddr="/var/tmp/ludzie.".time();
$program=dirname(__FILE__)."/run.php";

global $PHP_EXE;

$fname=$WEBPAGE->file_name;


if ($mailing_action)
{
	$img="";
	chdir("tools");
	$cmd="$PHP_EXE page.php SERVER_ID=$SERVER_ID page=$page ver=$ver lang=$lang  szablon=$szablon > $tmpmsg";
//	echo $cmd."<hr>";
	exec($cmd);
	chdir("..");

	ob_start();
	readfile($tmpmsg);
	$MSG=ob_get_contents();
	ob_end_clean();	

	$MSG=ereg_replace("$fname","",$MSG);
	$MSG=eregi_replace("windows-1250","iso-8859-2",$MSG);


	$MSG=ereg_replace("<\?[^\?]*\?>","",$MSG);
	$MSG=eregi_replace("(src|href|background)=([\"']+)([^\.\"']+)\.(gif|jpg|php|jpeg|png)","\\1=\\2$destination_server\\3.\\4",$MSG);;
	$MSG=eregi_replace("(src|href|background)=([^\.\"']+)\.(gif|jpg|php|jpeg|png)","\\1=$destination_server\\2.\\3",$MSG);;
	$MSG=eregi_replace("(background-image:[ ]*)url\(([^\)]+)\)","\\1url($destination_server\\2)",$MSG);;

	//background-image: url(%WEBBODY_IMG_PATH%/nl/lt.gif)


	$MSG=eregi_replace("<script[^<]+</script>","",$MSG);

	$MSG_HTML=$MSG;
	$MSG=html2sp2($MSG,$img);

	$MSG=encode($MSG,0);

	$t=time();
	$TOTAL_BOUNDARY = "_NextPart_000_006F_01C55297.$t";
	$TEXT_BOUNDARY  = "_NextPart_001_006F_01C55297.$t";

	ob_start();
	include(dirname(__FILE__)."/header.txt");
	echo html_chunk_split($MSG);

	//echo "\n--$TEXT_BOUNDARY--\n";

	if (is_array($img)) foreach($img AS $i)
	{
		//echo "\n--$TOTAL_BOUNDARY\n";
		echo "\n--$TEXT_BOUNDARY\n";
		echo $i;
	}
	echo "\n--$TEXT_BOUNDARY--\n";
	echo "\n--$TOTAL_BOUNDARY--\n";
	
	$MAIL=ob_get_contents();
	ob_end_clean();
	

	$MAIL=ereg_replace("\\\$TOTAL_BOUNDARY",$TOTAL_BOUNDARY,$MAIL);
	$MAIL=ereg_replace("\\\$TEXT_BOUNDARY",$TEXT_BOUNDARY,$MAIL);

	$MAIL=ereg_replace("\\\$FROM",$WEBPAGE->pagekey,$MAIL);
	//$MAIL=ereg_replace("\\\$TO","\$EMAIL",$MAIL);
	$MAIL=ereg_replace("\\\$SUBJECT",encode($WEBPAGE->title,0),$MAIL);

	$plik=fopen($tmpmsg,"w");
	fwrite($plik,$MAIL);
	fclose($plik);

	echo '<b>Rozmiar: ...... '.number_format(strlen($MAIL),0,'',' ').' B</b>';

	if ($mailing_action==1)
	{

		$title=$WEBPAGE->title;

/*
		exec("cd /var/tmp; cp $tmpmsg '$title.eml'; /usr/local/bin/zip '$title.zip' '$title.eml'; rm '$title.eml'");
		$sm = new sendmail_obj ;
		$sm->from=$WEBPAGE->pagekey;
		$sm->to=$WEBPAGE->pagekey;
		$sm->subject="$title.zip";
		$sm->msg="tytul: $title\nrozmiar: ".number_format(strlen($MAIL),0,'',' ')." B";

		$zip=fetch_file("/var/tmp/$title.zip");
		exec("rm /var/tmp/$title.zip");
		
		$dir="/var/tmp/tui_newsletter_".time();
		exec("mkdir $dir");
		html2sp2($MSG_HTML,$img,$dir);
		exec("cd $dir; /usr/local/bin/zip '../${title}_html.zip' *; cd ..; rm -rf $dir");

		$zip2=fetch_file("/var/tmp/${title}_html.zip");
		exec("rm /var/tmp/${title}_html.zip");
		$sm->att=array(
						array($zip,'application/zip',$title.'_eml.zip'),
						array($zip2,'application/zip',$title.'_html.zip')
					);

		sendmail2($sm);
*/
		$mailbcc=array($WEBPAGE->pagekey);

	}
	if ($mailing_action==2)
	{
		include(dirname(__FILE__)."/mailing.php");
	}
	if ($mailing_action==3)
	{
		$mailbcc=array();
		foreach (explode("\n",$multi_mail) AS $mail)
		{
			$mail=trim($mail);
			if (!strlen($mail)) continue;
			$mailbcc[]=$mail;
		}
		$sysinfo="Mail został wysłany do ".count($mailbcc)." osób.";
	}

	$plik=fopen($tmpaddr,"w");
	fwrite($plik,implode("\n",$mailbcc));
	fclose($plik);

	$fromaddr=eregi_replace("http[s]*://([^/]+).*","webadmin@\\1",$destination_server);
	$cmd="$PHP_EXE $program $tmpaddr $tmpmsg $fromaddr";
	//echo $cmd;
	system("$cmd >/dev/null 2>/dev/null &");

}


echo "</div>";

$adodb=pop();

?>

<hr size=1>