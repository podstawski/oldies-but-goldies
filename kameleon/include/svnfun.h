<?
	function getFileTree($katalog,$search='',$parent='')
	{
		$handle=@opendir($katalog);

		$wynik=array();
		while (($file = @readdir($handle)) !== false) 
		{
			if ($file=="." || $file=="..") continue;

			if (is_dir("$katalog/$file") )
			{
				$p=strlen($parent)?"$parent/$file":$file;
				$wynik=array_merge($wynik, getFileTree("$katalog/$file",$search,$p));
			}
			else
			{
				$f=strlen($parent)?"$parent/$file":$file;

				if (strlen($search)) 
				{
					foreach( explode(':',ereg_replace("[,; ]+",":",$search)) AS $s) if (strstr($file,$s))
					{
						$wynik[]=$f;
						break;
					}
				}
				else $wynik[]=$f;
			}


		}

		closedir($handle);

		return $wynik;
	}


	function fcmp($f1,$f2)
	{
		if (@filesize($f1) != @filesize($f2)) return false;


		$s1=@implode('',file($f1));
		$s2=@implode('',file($f2));

		if ($s1!=$s2) return false;
		
		return true;


	}

?>
