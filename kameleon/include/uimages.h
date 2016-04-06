<?
if (!strlen($page)) $page=$page_id;

//$rootdir="uimages/$SERVER_ID/$ver";
$rootdir=$UIMAGES;
/*
if (!file_exists("$rootdir")) 
	mkdir("$rootdir",0755);
*/

if ($BASIC_RIGHTS)
{
	$user=$USERNAME;
	if (!file_exists("$rootdir/$user")) 
		mkdir("$rootdir/$user",0755);
	$rootdir.="/$user";	
}

if (!strlen($curdir))  $curdir="";

if (!strlen($path)) $path=urldecode($cookieipath);

$path=urldecode($path);

if (strlen($curdir)) 
	if (strlen($path))
		$path.="/".$curdir;
	else
		$path.=$curdir;
else
{
	if (strlen($path))
	{
		$pp=explode("/",$path);
		$path="";
		for ($pi=0;$pi<count($pp)-1;$pi++)
		{
			if ($pi)
				$path.="/".$pp[$pi];
			else
				$path=$pp[$pi];
		}
	}
	else
		$path="";
}
if (strlen($curdir))
	$curpath="$path/$curdir";
else
	$curpath="$path";
SetCookie("cookieipath",$curpath);


if (strlen($path)>0) 
	$katalog=$rootdir."/$path";
else
	$katalog=$rootdir;

$handle=opendir("$katalog");
$lp=0;
$wgrany="";
while (($file = readdir($handle)) !== false) 
{
 if ($file=="." || $file=="..") continue;
 if ($file=="$DEFAULT_TEXTFILE_CSS") continue;
 clearstatcache();
 if (is_file("$katalog/$file"))
	$obrazki[]=$file;
 else
 	$katalogi[]=$file;
}
closedir($handle); 

$jsrozmiar="";
$dirlen=count($katalogi);
if ($dirlen>1) sort($katalogi);
$optiondir="";
for ($i=0;$i<$dirlen;$i++)
{
  $dir=$katalogi[$i];
  if ($curdir==$dir)
    $selected="selected";
  else
    $selected="";
  $optiondir.="<option $selected value='$path/$dir' style='font-weight:bold;'>[$dir]\n";
  if (!$i)
    $jsrozmiar.="new Array('$dir',1)";
  else
    $jsrozmiar.=",new Array('$dir',1)";
}


$len=count($obrazki);
if ($len>1) sort($obrazki);
$optionfile="";
for ($i=0;$i<$len;$i++)
{
 $file=$obrazki[$i];
 $value="$katalog/$file";
 $rozmiar=filesize($value);
 if ($obrazek_name==$file)
 {
  $selected="selected";
  $wgrany=$value;
 }
 else
  $selected="";
 if (strlen($jsrozmiar))
	 $jsrozmiar.=",new Array('$rozmiar',0)";
 else
 {
  if (!$i)
    $jsrozmiar.="new Array('$rozmiar',0)";
  else
    $jsrozmiar.=",new Array('$rozmiar',0)";
 }
 if (strlen($path)>0) 
 	$pa="$path/";
 else
 	$pa="";
 $optionfile.="<option $selected value='$pa$file'>$file\n";
}

if (strlen($jsrozmiar))
	$jsrozmiar="rozmiary = new Array($jsrozmiar);\n";

?>
<html>
<head>
    <title>KAMELEON: <? echo label("Images"); ?></title>
    <SCRIPT language="JavaScript"><?echo $jsrozmiar?></SCRIPT>
    <SCRIPT language="JavaScript">var UIMAGES='<? echo "$rootdir"?>'</SCRIPT>
<?php
	include_js("uimages");
?>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type"
	content="text/html; charset=<?echo $CHARSET?>">
</head>

<body bgcolor="#c0c0c0" topmargin="0">

<form method=post action=uimages.<?echo $KAMELEON_EXT?> name=galeria ENCTYPE="multipart/form-data" >
<input type=hidden name=page_id value="<?echo $page_id?>">
<input type=hidden name=page value="<?echo $page?>">
<input type=hidden name=pri value="<?echo $pri?>">
<input type=hidden name=action value="">
<input type=hidden name=newdir value="">
<input type=hidden name=curdir value="">
<input type=hidden name=remotefile value="">
<input type=hidden name=path value="<?echo urlencode($path)?>">

<table width=100% id="tabela" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2> 
<tr class=k_form>
 <td valign=middle align=left colspan=2 nowrap>
 <span class=k_text>
 <?echo label("First click browse button and select image from your disk and next click Save")?>
 </span><br>
  <input type=file name=obrazek class=k_button size=35>
  <input type=submit value="<?echo label("Save")?>" onClick="remotefile.value=obrazek.value;action.value='WgrajNowyObrazek'" class=k_button>
  <? if ($UIMAGES_VER==$ver) {?>
  <input type=checkbox name=overwrite value=1> <?echo label("Overwrite")?> 
  <? } ?>
 </td>
</tr>
<tr class=k_form>
 <td valign=top align=center>
 
  <table cellpadding=0 cellspacing=0 border=0>
 <tr>
 <td valign=middle>
 	<script>
		label_newdir='<?echo label("Issue directory name")?> ?'
		label1='<?echo label("Select file or directory !")?>'
		label2='<?echo label("Are You sure")?> ?'
	</script>
	<?
	if (strlen($path))
	{?>
		<img src=img/i_dirup_n.gif onClick="dirUp()" style="cursor:hand;" onmouseover="this.src='img/i_dirup_a.gif'" onmouseout="this.src='img/i_dirup_n.gif'" border=0 alt='<?echo label("..Up")?>' width=23 height=22 vspace=2>  
	<?}?>
	<img src=img/i_new_n.gif onClick="newDir(label_newdir)" style="cursor:hand;" onmouseover="this.src='img/i_new_a.gif'" onmouseout="this.src='img/i_new_n.gif'" border=0 alt='<?echo label("Create new directory")?>' width=23 height=22 vspace=2>  

	<? if ($UIMAGES_VER==$ver) {?>
   	<img src=img/i_delete_n.gif onClick="deleteFile(document.galeria.lista,label1,label2)" style="cursor:hand;" onmouseover="this.src='img/i_delete_a.gif'" onmouseout="this.src='img/i_delete_n.gif'" border=0 alt='<?echo label("Delete selected file or directory")?>' width=23 height=22 vspace=2>
	<? } ?>
  </td>
  </tr>
  <tr>
  <td class=k_td>
   <?echo "&nbsp;/$path<br>";?>
    <select class=k_select style="width:200;" name=lista size=18 onChange='preview(this)' ondblclick='SetDir(this)'>
     <?echo $optiondir?>	
     <?echo $optionfile?>
    </select>
 </td>
 </table>
 </td>
 <td valign=top align=center>
  <table border=0 cellpadding=0 cellspacing=0 width=200>
  <tr>
   <td><img src=img/spacer.gif width=1 height=200 border=0></td>
   <td align=center>
 		<img id=view src="img/spacer.gif" border=0 onLoad="resize(lista)" onClick="wstawObrazek(lista)" style="cursor:hand;">
   </td>
  </tr>
  <tr>
    <td></td>
    <td align=center>
		<input class=k_button type=button value="<?echo label("Insert image")?>" onClick="wstawObrazek(lista)">
 		<p id=p_opis class=k_text>&nbsp;</p>
    </td>
  </tr>    
  </table>
 </td>
</tr>
</table>
</form>

<?
 if (strlen($wgrany))
  echo "
  <SCRIPT language='JavaScript'>
   document.galeria.view.src='$wgrany'
  </SCRIPT>";
?>
</body>
</html>
