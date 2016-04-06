<?
	parse_str($costxt);

	//default_converter=xxx.php&upload_dir=xxx&default_step=xxx&default_do=xxx

	global $LINIA;
	if (!isset($LINIA["step"])) $LINIA["step"]=$default_step;
	if (!isset($LINIA["do"])) $LINIA["do"]=$default_do;
	

	$IMPORTS=array("kategoria","towar");

	$dh = opendir("$SKLEP_INCLUDE_PATH/import_converters"); 
	$conv = "";
	if (!strlen($default_converter)) 
		$conv = "<input checked type=\"radio\" name=\"form[konwerter]\" value=\"\">bez konwersji<br>";
	
	while (($file = readdir($dh)) !== false) 
	{
		if ($file[0]==".") continue;
		if ($file[0]=="_") continue;
		$nfile = eregi_replace(".php","",ereg_replace("_"," ",$file));

		if (strlen($default_converter) && $file!=$default_converter) continue;
		$sel=($file==$default_converter)?'checked':'';
		$conv.="<input type=\"radio\" name=\"form[konwerter]\" $sel value=\"".$file."\">$nfile<br>";
		
	}
	closedir($dh);

?>
<FORM METHOD=POST ACTION="<? echo $self?>" ENCTYPE="multipart/form-data">
<TABLE cellspacing=5>
<TR>
	<TD><? echo sysmsg('File','import');?>:</TD>
	<TD><INPUT TYPE="file" NAME="import_plik" size="60"></TD>
</TR>
<TR>
	<TD valign="top"><? echo sysmsg('Convert','import');?>:</TD>
	<TD><? echo $conv ?></TD>
</TR>

<TR>
	<TD valign="middle"><? echo sysmsg('Lines','import');?>:</TD>
	<TD>
		<input type="text" size=4 name="LINIA[od]" value="<?echo $LINIA[od]?>"> -
		 <input type="text" size=4 name="LINIA[do]" value="<?echo $LINIA["do"]?>">
		/ <input type="text" size=4 name="LINIA[step]" value="<?echo $LINIA["step"]?>">
		(<? echo sysmsg('Don\'t expect every module to support it','import');?>) 
	</TD>
</TR>
<TR>
	<TD valign="top" colspan=2>
<?
	global $_REQUEST, $import_plik,$import_plik_name,$import_pliki;
	$dir="/var/tmp/wm-import";

	if (strlen($upload_dir)) $dir="$SKLEP_INCLUDE_PATH/$upload_dir";

	if (!file_exists($dir)) mkdir($dir,0755);

	if (!is_array($import_pliki)) $import_pliki=array();

	if (is_uploaded_file($import_plik)) 
	{
		move_uploaded_file($import_plik,"$dir/$import_plik_name");
		$import_pliki[$import_plik_name]=1;
		$import_plik="";
	}

	$FORM=txt_addslash($_REQUEST["form"]);	

	

	$handle=opendir("$dir");
	while ($file = readdir($handle))
	{
			if ($file[0]==".") continue;
			$pliki[]=$file;
	}
	closedir($handle);
	if (is_array($pliki)) sort($pliki);

	for ($i=0;is_array($pliki) && $i<count($pliki);$i++)
	{
		$p=$pliki[$i];
		$checked=in_array($p,array_keys($import_pliki))?"checked":"";
		$t=date("d-m-Y H:i",filemtime("$dir/$p"));
		$s=filesize("$dir/$p");
		echo "<input type='checkbox' $checked name='import_pliki[$p]' value=1> <a href='$dir/$p'>$p</a> (<I>$t</I>), $s B<br>";

	}
	if (is_array($pliki) && count($pliki)>1)
	{
		$checked=strlen($import_pliki['IMPLODE'])?"checked":"";
		echo "<input type='checkbox' $checked name='import_pliki[IMPLODE]' value=':'> 
				<font color=red><i>".sysmsg('All files together','import')."</i></font><br>";
	}

	$LINIA_ORG=$LINIA;
	$LINIA["step"]+=0;


	if ($LINIA["step"] && !$LINIA["do"])
	{
		$LINIA["step"]=0;
		echo "Brak ograniczenia górnego - nie mo¿na wykonaæ krokowo.";
	}

	if ( $LINIA["step"] && $LINIA["do"] - $LINIA["od"] > $LINIA["step"] )
	{
		$LINIA["do"]=$LINIA["od"]+$LINIA["step"]-1;
	}

	
	if (count($import_pliki) && strlen($import_pliki['IMPLODE']) )
	{
		
		$impl=$import_pliki['IMPLODE'];
		unset($import_pliki['IMPLODE']);
		$linia=implode($impl,array_keys($import_pliki));

		$import_pliki=array();
		$import_pliki[$linia]=1;
	}


	$import_start_time=time();
	echo "
			<script>
				var okno_debug=null;
				function import_debug(str)
				{
					if (okno_debug==null) 
					{

						wys = 150;
						szer = 350;
						_top = Math.round(screen.height / 2) - Math.round(wys / 2);
						_left = Math.round(screen.width / 2) - Math.round(szer / 2);
						param='width='+szer+',height='+wys+',top='+_top+',left='+_left;

						okno_debug=open('','debug',param);
					}

					okno_debug.document.write('<font face=\"Tahoma\">'+str+'</font>');
					okno_debug.document.close();

				}
			</script>
	";


	while( count($import_pliki) )
	{
		

		if (function_exists('memory_get_usage')) echo "<hr size=1><b>memory_get_usage() = ".memory_get_usage()."</b><br>";

		unset($converted_xml);
		unset($obj);
		$obj=null;
		$converted_xml=null;

		foreach(array_keys($import_pliki) AS $import_plik)
		{

			if (strtolower(substr($import_plik,strlen($import_plik)-3))=='zip' || strtolower(substr($import_plik,strlen($import_plik)-3))=='rar') 
				$file_content=ereg_replace("(^|:)","\\1$dir/",$import_plik);
			else $file_content = file("$dir/$import_plik");

			$conv_name = $SKLEP_INCLUDE_PATH."/import_converters/".$FORM[konwerter];
			if (strlen($FORM[konwerter]))
			{
				if (file_exists($conv_name))
				{
					include_once($conv_name);
					$converted_xml = import_convert($file_content);
					if (!is_object($converted_xml))
					{
						$converted_xml = win2iso($converted_xml);
						$obj=xml2obj($converted_xml);
						unset($converted_xml);
					}
					else $obj=&$converted_xml;
				}
			}
			else
			{
					if (is_array($file_content)) $obj=xml2obj(implode('',$file_content));
			}

			$TODO=array();
			while (is_object($obj->magazyn) && list($__key,$v)=each($obj->magazyn))
			{
				if (file_exists("$SKLEP_INCLUDE_PATH/import/$__key.php"))
					$TODO[]="$SKLEP_INCLUDE_PATH/import/$__key.php";
				else
					echo "Nieznany poziom: <B>$__key</B><br>";
			}

			foreach ($TODO AS $f) include($f);
		}

		//echo "<hr>";print_r($LINIA); print_r($LINIA_ORG); echo "<hr>";

		if (!$LINIA["step"]) break;
		if ($LINIA["do"] >= $LINIA_ORG["do"]) break;

		$LINIA["od"]+=$LINIA["step"];
		$LINIA["do"]+=$LINIA["step"];


		//$razem_loop++; if ($razem_loop>10) break;
	}

	if (count($import_pliki) && function_exists('import_convert') ) import_convert('');
?>
<TR>
	<TD valign="top" colspan=2><INPUT TYPE="submit" value="Import"></TD>
</tr>
</TABLE>
</FORM>
