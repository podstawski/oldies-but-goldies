<?
	if (!function_exists('sysmsg'))
	{
		function sysmsg($a,$b)
		{
			return $a;
		}
	}


	class Importer 
	{
		var $directory = "";

		function Importer($dir)
		{
			$this->directory = $dir;
			if (!is_dir($this->directory)) mkdir($this->directory);
		}

		function getFileList()
		{
			$i = 0;
			$filelist = "<TABLE width=\"100%\">
			<TR>
				<Th>".sysmsg('File','import')."</Th>
				<Th>".sysmsg('Import','import')."</Th>
				<Th>".sysmsg('Delete','import')."</Th>
			</TR>";
			$d=$this->directory;
			if ($handle = opendir($this->directory)) 
				while (false !== ($file = readdir($handle))) 
					if ($file != "." && $file != "..") 
					{
						$filelist.= " 
						<TR>
							<TD style=\"border-bottom:1px solid black\"><a href=\"$d/$file\">$file</a></TD>
							<TD style=\"border-bottom:1px solid black\" align=\"center\"><INPUT TYPE=\"checkbox\" NAME=\"FILE_LIST[$i]\" value=\"$file\"></TD>
							<TD style=\"border-bottom:1px solid black\" align=\"center\"><INPUT TYPE=\"checkbox\" NAME=\"FILE_DELETE[$i]\" value=\"$file\"></TD>
						</TR>";
						$i++;
					}
			@closedir($handle);
			return $filelist."</TABLE>";
		}	
		
		function getFilesToImport()
		{
			global $FILE_LIST, $FILE_DELETE, $_FILES;

			if (is_array($FILE_DELETE))
				while (list(,$filename) = each($FILE_DELETE))
					if (file_exists($this->directory."/".$filename))
						@unlink($this->directory."/".$filename);
					

			if (is_array($FILE_LIST))
				$files_to_import = $FILE_LIST;

			if(is_uploaded_file($_FILES['userfile']['tmp_name']))//JAK PRZESЃAЃ PLIK
			{
				$name = $_FILES['userfile']['name'];
				move_uploaded_file($_FILES['userfile']['tmp_name'],$this->directory."/".$name);

				$files_to_import[] = $name;
			}
			return $files_to_import;
		}

		function printForm()
		{
			return "
			<form method=post action=\"$self\" enctype=\"multipart/form-data\">
			<fieldset style=\"width:99%;margin-left:3px; margin-bottom:3px\">
			<legend>Import</legend>
			".sysmsg('Find file form import','import').": <INPUT TYPE=\"file\" name=\"userfile\"><br>
			".sysmsg('or check one of following','import').":<br>
			".$this->getFileList()."
			<br><input type=\"submit\" value=\"Import\">
			</fieldset><br />&nbsp;
			</form>";
		}
	}
?>
