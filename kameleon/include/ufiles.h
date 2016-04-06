<?
if (!strlen($page)) $page=$page_id;

include("include/ufiles_const.h");
include_once("include/search.h");
include_once('include/file.h');


$rootsubdir="";
if ($BASIC_RIGHTS)
{
	$user=$USERNAME;
	$rootdir.="/$user";	
	$rootsubdir="$user/";
}

/**
 * checkPermissions() - Poniewaz w windowsie nie dziala is_writable()
 * trzeba "recznie" sprawdzic czy jest zapis do katalogu
 * 
 * @param $file
 * @return boolean 0,1
 **/
function checkPermissions( $file )
{
	$OS = ( stristr(PHP_OS,'win') && !stristr(PHP_OS,'darwin')? 'WIN' : ''); 

	if ( $OS != 'WIN' ) 
	{
		return ( is_writable($file) ) ? false : true;
	}
	else
	{
		if (is_dir($file)) 
		{
			$nazwapliku = $file . '/tmp_write_test';
		}
		
		if (@fopen($nazwapliku, 'w+')) {
			@unlink($nazwapliku);
			return false;
		}
		else
		{
			return true;
		}
	}
}


clearstatcache();


if (!file_exists($rootdir)) 
{
	mkdir_p($rootdir);
	if (strlen($init_gallery_cmd)) eval($init_gallery_cmd);
}
$PermissionDenied = checkPermissions($rootdir);
if (!file_exists($rootdir)) return;

//echo $rootdir . '<br>';
//echo is_writable($rootdir);

$path= $_COOKIE[$cookiename];


if (!strlen($path)) $path=".";

if (substr($path,strlen($path)-1) == '/') $path=substr($path,0,strlen($path)-1);


if ($newdir==".." && substr(basename($path),0,1)==".") $newdir="";

$ckfile="";

if (strlen($newdir))
{
	
	if (!file_exists("$rootdir/$path/$newdir")) 
	{
		
		$newdir=unpolish($newdir);
		$newdir=@ereg_replace("[^a-z|A-Z|0-9|\_|\.]","_",$newdir);
		$wf_accesslevel=webfile("$path",$galeria);
		if ($wf_accesslevel || $path=='.') mkdir("$rootdir/$path/$newdir",0755);
		else $newdir='';
	}

	$path = ($path==".") ? $newdir : "$path/$newdir";
	$path = ereg_replace("/[^/]+/\.\.","/",$path);
	$path = ereg_replace("[/]+","/",$path);
	$path = ereg_replace("/\./","/",$path);
	if (strstr($path,"..") ) $path=".";

	if (substr($path,strlen($path)-1) == '/') $path=substr($path,0,strlen($path)-1);
	SetCookie($cookiename,$path);
}
else if (strlen($_GET["ckpath"])>0)
{
  $ckpath = str_replace("/".$rootdir,"",$_GET["ckpath"]);
  if (substr($path,strlen($path)-1) != '/')
  {
    $tm = explode("/",$ckpath);
    $ckfile = $tm[(sizeof($tm)-1)];
    unset($tm[(sizeof($tm)-1)]);
    $path = implode("/",$tm);
    $path = str_replace($rootdir,"",$path);
    $path = substr($path,1,strlen($path));
    SetCookie($cookiename,$path);
  } 
}

$katalog = ($path==".") ? $rootdir : "$rootdir/$path";

if (!file_exists($katalog)) 
{
	$katalog=$rootdir;
	$path=".";	
	SetCookie($cookiename,"");
}




$handle=@opendir($katalog);

$lp=0;
$wgrany="";
while (($file = @readdir($handle)) !== false) 
{
	if ($file[0]==".") continue;

	if (substr($file,0,strlen($CONST_IMG_EDITOR))==$CONST_IMG_EDITOR)
	{
		unlink("$katalog/$file");
		continue;
	}

	clearstatcache();
	if (is_file("$katalog/$file"))
		$pliki[]=$file;
	else
		$katalogi[]=$file;

}
@closedir($handle); 

$jsrozmiar="";
$jslp=0;
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
	$optiondir.="<option $selected rel='dir' value='" . $path . "/" . $dir ."' style='font-weight:bold;'>[ " . $dir . "]\n";

	$jsrozmiar.="rozmiary[$jslp]='$dir,1';";
	$jslp++;
}


$len=count($pliki);
if ($len>1) sort($pliki);
$optionfile="";
for ($i=0;$i<$len;$i++)
{
	clearstatcache();
	$file=$pliki[$i];
	$value=$katalog . "/" . $file;
	$rozmiar=filesize($value);
	$datam=date("d-m-Y",filemtime ($value));
	if ($obrazek_name==$file)
	{
		$selected="selected";
		$wgrany=$value;
	}
	elseif ($ckfile==$file)
	{
    $selected="selected";
  }
	else
		$selected="";
	$jsrozmiar.="rozmiary[$jslp]='$rozmiar,0,$datam';";
	$jslp++;

	if ($path!=".") 
		$pa="$path/";
	else
		$pa="";
	//$optionfile.="<option $selected rel='file' value='". $UIMAGES . "/" . $pa . $file ."'>" . $file ."\n";
	$optionfile.="<option $selected rel='file' wel='".$ckpath."' value='". $rootdir . "/" . $pa . $file ."'>" . $file ."\n";
}

$jsrozmiar="var rozmiary = new Array();\n$jsrozmiar\n";


$display_path=ereg_replace("/\.[^/]+","","/$path");
$display_path=ereg_replace("/\.[/]*","/",$display_path);

?>
<html>

<head>
    <title>KAMELEON: <? echo label("Files"); ?></title>
    <script language="JavaScript">var UFILES='<? echo "$rootdir"?>'</SCRIPT>
  	<?
  		include_js("cookies");
  		include_js("jquery-1.4");
  		include_js("ufiles");
  	?>

    <script language="JavaScript">
    	<?echo $jsrozmiar?>

      function str_replace(co,naco,text)
      {
        return text.split(co).join(naco);
      }
    
    	var label_unzip='<? echo label("UnZip and remove")?>';
    	function wstawPlik(obj)
    	{
    		if (document.getElementById('final_button').value!=label_unzip )
    		{
    			img=obj[obj.selectedIndex].value;
          cimg=img;
    			// Jeszcze warunek
    			// jesli otwierana w window.open
    			// to ma byc top.opener
    			if ( typeof(top.opener) == 'object' )
    			{
    			  <?
    			  if ($backwithdir==0)
            {
              echo "cimg=str_replace('".$rootdir."/','',cimg);";
            }
    			  ?>
      			if (top.opener.execScript) {
              top.opener.execScript("<? echo $callback."('";?>"+cimg+"')","JavaScript"); //for IE
            } else {
              eval('self.opener.' + "<? echo $callback."('";?>"+cimg+"')"); //for Firefox
            } 
    			
    				//top.opener.execScript("<? echo $callback?>('<? if (strlen($rootsubdir)) echo $rootsubdir?>"+img+"')","JavaScript");
    			}
    			else
    			{
    				// Jesli przez modalDialog
    				// wyjscie z iframa jest przez parent
    				parent.getParam('<? if (strlen($backdir)) echo $backdir?>'+img);
    			}
    			window.close();
    		}
    		else
    		{
    			document.galeria.action.value='UnzipPlik';
    			document.galeria.submit();
    		}
    	}
    
    	function webfileset(s)
    	{
    	  if (document.getElementById('view'))
    	  {
          document.getElementById('view').style.display='';
      		document.getElementById('view').style.visibility='';
      		document.webfile.wf_file.value=s.value;
        }
    	}
    
    	function openJUpload(dir)
    	{
    		a=open('jupload.php?dir='+dir,'JUpload','width=640,height=480');
    	}
    	
    	label_newdir='<?echo label("Issue directory name")?> ?';
    	label1='<?echo label("Select file or directory !")?>';
    	label2='<?echo label("Are You sure")?> ?';
    	label3='<?echo label("File size")?>:';
    	label4='<?echo label("bytes")?>.';
    	label5='<?echo label("Upload date")?>:';
    	label6='<?echo label("Directory")?>:';
    	
    	function preloader()
    	{
        document.getElementById('uploader').style.display='none';
        document.getElementById('preloader').style.display='block';
      }
    	
    </script>
	<script language="javascript" src="jsencode/prompt.js"></script> 
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/fileeditor.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
  <?
    if ($ckeditor==0)
    {
      echo "
      <style type=\"text/css\">
        body { padding: 20px; margin: 0;}
      </style>
      ";
    }
  ?>
</head>

<body>
<form method="post" action="ufiles.<?echo $KAMELEON_EXT?>" name="galeria" id="formularz" enctype="multipart/form-data" onsubmit="preloader();">
<input type="hidden" name="page_id" value="<?echo $page_id?>">
<input type="hidden" name="page" value="<?echo $page?>">
<input type="hidden" name="pri" value="<?echo $pri?>">
<input type="hidden" name="galeria" value="<?echo $galeria?>">
<input type="hidden" name="action" id="akcja" value="">
<input type="hidden" name="newdir" value="">
<input type="hidden" name="appmode" value="<?echo $appmode?>">
<input type="hidden" name="remotefile" value="">
<input type="hidden" name="path" value="<?echo urlencode($path)?>">

<div class="contener">
  <div class="kol_left">
  
  <?php if (!$PermissionDenied) { ?>
  	<div id="uploader">
      <div class="hint"><?=label("First click Browse button and select file from your disk and then click Save")?></div>
  	  <div class="inp"><input type="file" name="obrazek" class="intext"></div>
  	  <div class="buttons">
        <input type="button" value="Multi" onclick="openJUpload('<?=urlencode($katalog)?>')" />	
  	    <input type="submit" value="<?echo label("Save")?>" onclick="remotefile.value=obrazek.value;action.value='WgrajNowyPlik'" />
  	    <input type="checkbox" name="overwrite" value="1" /> <?echo label("Overwrite")?>
  	  </div>
  	</div>
  	<div id="preloader" style="display:none">
      <img src="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/img/upload_file_loader.gif" alt="<?echo label("Trwa wgrywanie pliku")?>" /><br /><?echo label("Trwa wgrywanie pliku")?>
  	</div>
  	  
  <? 
  }
  else
  {
    echo "<div class=\"errmsg\">".label("File upload permission denied")."</div>";
  }
  ?>
  
      <div class="path"><?=$display_path."<br />".$spath?></div>
	
	    <div class="filelist">
  	    <select name="lista" id="select_lista" size="18" onclick='tryedit(this,<? echo ($edit_allowed && !$PermissionDenied)?1:0?>)' onchange='<? if ($ckeditor>0) echo "setCKEditor(this,".$ckeditor.");"; else echo "preview(this);webfileset(this);"; ?>tryedit(this,<? echo ($edit_allowed && !$PermissionDenied)?1:0?>)' ondblclick='SetDir(this)'>
          <?echo $optiondir?>	
          <?echo $optionfile?>
        </select>
        <script type="text/javascript">
        	jQueryKam("#select_lista").change();
        </script>
	    </div>
	
      <div class="buttons">
        <?if (strlen($path)) { ?><img src="img/i_dirup_n.gif" onclick="dirUp()" onmouseover="this.src='img/i_dirup_a.gif'" onmouseout="this.src='img/i_dirup_n.gif'" alt="<?echo label("..Up")?>" title="<?echo label("..Up")?>" /><? } ?>
        <img src="img/i_new_n.gif" onclick="newDir(label_newdir)" onmouseover="this.src='img/i_new_a.gif'" onmouseout="this.src='img/i_new_n.gif'" alt="<?echo label("Create new directory")?>" title="<?echo label("Create new directory")?>" />
        <img src="img/i_delete_n.gif" onclick="deleteFile(document.galeria.lista,label1,label2)" onmouseover="this.src='img/i_delete_a.gif'" onmouseout="this.src='img/i_delete_n.gif'" alt="<?echo label("Delete selected file or directory")?>" title="<?echo label("Delete selected file or directory")?>" />
        <img src="img/i_rights_n.gif" onclick="document.webfile.submit()" onmouseover="this.src='img/i_rights_a.gif'" onmouseout="this.src='img/i_rights_n.gif'" alt="<?echo label("rights selected file or directory")?>" title="<?echo label("rights selected file or directory")?>" />
        <img src="img/i_property_min_n.gif" onclick="edit_file(<?echo $galeria+0?>,'<?echo $edytor?>')" style="display:none" id="edit_file_img" onmouseover="this.src='img/i_property_min_a.gif'" onmouseout="this.src='img/i_property_min_n.gif'" alt="<?echo label("Edit file")?>" title="<?echo label("Edit file")?>" />
        <? if(!$preview_denied) { ?>
      		<input type="checkbox" name="preview_mode" value="1" id="preview_check" onclick="PreviewMode(this)"><label for="preview_check"><?echo label("Preview")?></label>
      	<? } ?>
        
        <? if ( strlen($final_button) ) { ?><input type="button" id="final_button" value="<?echo $final_button ?>" onclick="wstawPlik(lista)"><? } ?>      
      </div>
      <iframe id="webfiler" src="empty.php" scrolling="no" name="ufiles" frameborder="0" style="display:inline; width:100%; height:45px;"></iframe>
  
    </div>
    
    <?
    if(!$preview_denied) { ?>
    <div class="kol_right">
      <iframe id="view" style="display:inline; width:100%; height:400px;" src="empty.php" name="ufiles" frameborder="0" ></iframe>
    </div>
    <? } ?>
    <div class="clean"></div>
  </div>
</form>
<form name="webfile" target="ufiles" action="webfile.php">
	<input type="hidden" name="wf_gal" value="<?echo $galeria?>">
	<input type="hidden" name="wf_file" value="<? echo $path?>">
</form>

<?
 if ($default_preview && !$preview_denied) 
  echo "
  <script language='JavaScript'>
		document.getElementById('preview_check').checked=true;
	</script>
	";

 if (strlen($wgrany) && !$preview_denied)
  echo "
  <script language='JavaScript'>
  	preview(document.all.lista);
  	$check_preview_checkbox
  </script>";
  
  $type_f="";
  if ($galeria==8) $type_f="info";
  if ($galeria==7) $type_f="Link";
  
  if (strlen($type_f)>0) 
  {
    echo "
    <script type=\"text/javascript\">
      val = window.parent.CKEDITOR.dialog.getCurrent().getContentElement( '".$type_f."', 'txtUrl' ).getValue();
      opt = document.getElementById('select_lista');
      for (var i = 0; i < opt.options.length; i++) 
      {
        if (opt.options[i].value==val) { opt.selectedIndex=i; }
      }
    </script>
    ";
  }
?>
</body>
</html>
