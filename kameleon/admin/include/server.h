<?

$_lang=$lang;
$server+=0;	
$query="SELECT *,lang AS slang FROM  servers WHERE  id=$server";

parse_str(ado_query2url($query));

$lang=$_lang;

if (!strlen($slang)) $slang=$lang;

$query = " SELECT id AS grupa, groupname FROM groups ORDER BY groupname";
$res=$adodb->Execute($query);
for ($i=0;$i<$res->RecordCount();$i++)
{
  parse_str(ado_ExplodeName($res,$i));
  if ($groupid==$grupa)
    $selected="selected";
  else
    $selected="";
  $gr.="<option value=$grupa $selected>$groupname</option>\n";
}
$gr="<select class='k_select' name=f_grupa>$gr</select>";


$szablony=array();
$handle=@opendir("../szablony");

if ($handle) while (($file = readdir($handle)) !== false ) 
{
	if ($file[0]==".") continue;
	if (is_dir("../szablony/$file")) $szablony[]=$file;
}
closedir($handle);
sort($szablony);

$handle=@opendir("../szablony.def");

if ($handle) while (($file = readdir($handle)) !== false ) 
{
	if ($file[0]==".") continue;
	if (is_dir("../szablony.def/$file") && is_writable("../szablony")) $szablony[]=":$file";
}

closedir($handle);

$szab="";
foreach ($szablony AS $szablon_name)
{
	$sel=($szablon_name==$szablon)?"selected":"";
	$szablon_value=$szablon_name;
	if ($szablon_value[0]==':') $szablon_name=strtoupper(substr($szablon_name,1));
	$szab.="<option value=\"$szablon_value\" $sel>$szablon_name</option>\n";
}

$szab="<select class='k_select' name='szablon'>
		<option value=\"\"></option>
		$szab
		</select>";

?>

<form method="post" name="link" action="servers.php#<?echo $nazwa?>">
  <input type="hidden" name="action" value="modserver" />
  <input type="hidden" name="server" value="<?echo $server?>" />
  <div class="secname">
    <h2><?echo label("Server parameters")?></h2>
  </div>
  <div class="formularz">
  
    <div class="litem_1">
      <label><?echo label("Server name")?></label>
      <div class="inputer">
        <input type="text" size="40" name="nazwa" value="<?echo $nazwa?>" />
      </div>
    </div>
    
    <div class="litem_2">
      <label><?echo label("Server URL")?></label>
      <div class="inputer">
        <input type="text" size="60" name="http_url" value="<?echo $http_url?>" />
      </div>
    </div>
  
    <div class="litem_1">
      <label><?echo label("Group name")?></label>
      <div class="inputer">
        <?echo $gr?>
      </div>
    </div>
    
    <div class="litem_2">
      <label><?echo label("Active border color")?></label>
      <div class="inputer">
        <input type="text" size="8" name="editbordercolor" value="<?echo $editbordercolor?>" />
      </div>
    </div>
    
    <div class="litem_1">
      <label><?echo label("Template name")?></label>
      <div class="inputer">
        <?echo $szab?>
      </div>
    </div>
    
    <div class="litem_2">
      <label><?echo label("Version number")?></label>
      <div class="inputer">
        <input type="text" size="3" name="version" value="<?echo $ver?>" />
      </div>
    </div>
    
    <div class="litem_1">
      <label><?echo label("Language")?></label>
      <div class="inputer">
        <input type="text" size="3" name="slang" value="<?echo $slang?>" />
      </div>
    </div>
      
    <div class="litem_2">
      <label><?echo label("File extention")?></label>
      <div class="inputer">
        <input type="text" size="5" name="file_ext" value="<?echo $file_ext?>" />
      </div>
    </div>  
      
  </div>
  <div class="secname">
    <h2><?echo label("FTP server parameters")?></h2>
  </div>
  <div class="formularz">
  
    <div class="litem_1">
      <label><?echo label("FTP host name");?>:</label>
      <div class="inputer">
        <input type="text" size="40" name="ftp_server" value="<?echo $ftp_server?>" />
      </div>
    </div>
    
    <div class="litem_2">
      <label><?echo label("FTP username")?></label>
      <div class="inputer">
        <input type="text" size="16" name="ftp_user" value="<?echo $ftp_user?>" />
      </div>
    </div>
    
    <div class="litem_1">
      <label><?echo label("FTP user password")?></label>
      <div class="inputer">
        <input type="password" size="16" name="ftp_pass" value="<?echo $ftp_pass?>" />
      </div>
    </div>
    
    <div class="litem_2">
      <label><?echo label("Remote dir")?></label>
      <div class="inputer">
        <input type="text" size="40" name="ftp_dir" value="<?echo $ftp_dir?>" />
      </div>
    </div>
  
  </div>
  <div class="secname">
    <h2><?echo label("Version management")?></h2>
  </div>
  <div class="formularz">
  
    <div class="litem_1">
      <label><?echo label("SVN command")?></label>
      <div class="inputer">
        <input type="text" size="60" name="svn" value="<?echo $svn?>" />
      </div>
    </div>
    
    <div class="litem_2">
      <label><?echo label("Create version archive in WebKameleon")?></label>
      <div class="inputer">
        <input type="checkbox" name="versions" value="1" <?if ($versions) echo 'checked'?> />
      </div>
    </div>
  
  </div>
  <input type="submit" class=k_button value='<?echo label("Save")?>' />
</form>

<?
	$query="SELECT count(*) AS c1 FROM webpage WHERE server=$server";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c2 FROM weblink WHERE server=$server";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c3 FROM webtd WHERE server=$server";
	parse_str(ado_query2url($query));

	if ( !($c1+$c2+$c3))
	{

?>

<div class="secname">
  <h2><?echo label("Import from file")?></h2>
</div>
<form method="post" action="servers.php" enctype="multipart/form-data" name="formImport">
  <input type="hidden" name="action" value="importserver" />
  <input type="hidden" name="server" value="<?echo $server?>" />
  <input type="hidden" name="ServerName" value="<?echo $nazwa?>" />
  <input type="hidden" id="_importfile_id"  name="_importfile" />
  <div class="formularz">
    <div class="litem_1">
      <label><?=label("Import server")?></label>
      <div class="inputer">
        <input type="file" size="25" name="importfile" />
        <input type="submit" value="<?=label("Import server")?>" />
      </div>
    </div>
    <div class="litem_2">
      <label><? echo label("Choose from prepared by Gammanet")?>:</label>
      <div class="inputer">
        - <a class="k_a" href="javascript:importGammanet('partner')">partner</a><br />
        - <a class="k_a" href="javascript:importGammanetPrompt()"><?echo label("Known file name")?></a>
      </div>
    </div>
  </div>
</form>

<script>
	function importGammanet(name)
	{
		document.formImport.importfile.disabled=true;
		document.formImport._importfile.value=name;
		document.formImport._importfile_id.name="importfile";
		document.formImport.submit();
	}

	function importGammanetPrompt()
	{
		n=prompt('<?echo label("Issue file name")?>','');
		if (n!=null) importGammanet(n);
	}
</script>


<?
	}
?>


<script>
	function test_form(f)
	{
		if (f.ServerSrc.selectedIndex==0)
		{
			alert ("<?echo label("First choose server !")?>");
			return false;
		}
	}
</script>
