<?
	$conf=xml2obj($costxt);
	$conf=$conf->xml;


	if (!is_Object($MODULES->crm->files->calendar->conf)) return;

	while ( list($name,$val)=each($MODULES->crm->files->calendar->conf) )
	{
		echo "<p class='k_text'>";

		while ( list($type,$opt)=each($val) )
		{
			switch ($type)	
			{
				case "radio";
					while ( list($id,$desc)=each($opt->option) )
					{
						$s=($conf->$name==$id)?"checked":"";
						echo "&nbsp; <input type='radio' name='CALENDAR[$name]' value='$id' $s> ";
						echo kameleon_global($desc);
					}
					break;
				case "select";
					echo "&nbsp;<select name='CALENDAR[$name]' class='k_select'>\n";
					while ( list($id,$desc)=each($opt->option) )
					{	
						$desc=kameleon_global($desc);
						if (strstr($desc,"("))
						{
							$desc=ereg_replace("$name","\$conf->$name",$desc);
							eval("\$desc=$desc;");
						}
						if (!strstr($desc,"<option"))
						{
							$sel="";
							$sel=($conf->$name == $id) ?"selected":"";
							$desc="<option value='$id' $sel>$desc</option>";
						}
						echo $desc;
						
					}
					echo "</select>";
					break;
			}	
		}
		echo "</p>";

		
	}

?>