<?

if (!function_exists('f_fetch_images_during_save'))
{
	function fetch_image_file($f)
	{
		$plik=@fopen($f,'r');
		if (!$plik) return;
		while (strlen($data=fread($plik,1000))) $wynik.=$data;
		fclose($plik);
		return $wynik;
	}


	function fetch_images_during_save($html)
	{
		global $UIMAGES,$cookieipath;
		$delim='askjdhfjkashdfashkdjfjkasdhf';

		$dir=$UIMAGES;
		if (strlen($cookieipath)) $dir.="/$cookieipath";

		if (!is_dir($dir)) return $html;
		if (!is_writable($dir)) return $html;

		
		$h=eregi_replace('(http://[^"\'>=]+jp[e]*g)',"$delim\\1$delim",$html);
		$h=eregi_replace('(http://[^"\'>=]+gif)',"$delim\\1$delim",$h);
		$h=eregi_replace('(http://[^"\'>=]+png)',"$delim\\1$delim",$h);
		$h=eregi_replace('(http://[^"\'>=]+swf)',"$delim\\1$delim",$h);
		$h=eregi_replace('(http://[^"\'>=]+css)',"$delim\\1$delim",$h);
		$h=eregi_replace('(http://[^"\'>=]+js)',"$delim\\1$delim",$h);

		while ($pos=strpos($h,$delim))
		{
			$h=substr($h,$pos+strlen($delim));
			$pos=strpos($h,$delim);
			$i=substr($h,0,$pos);

			//echo "<br><b>$i</b>";
			$f=fetch_image_file($i);
			if (strlen($f))
			{

				$name=basename($i);

				$k=0;
				while (strlen($NAME_TAB[$name]) && $NAME_TAB[$name]!=$i)
				{
					if ($name[0]=='k') $name=substr($name,1+strlen($k));
					$name="k$k$name";
					$k++;
				}
				$NAME_TAB[$name]=$i;

				$file=fopen("$dir/$name","w");
				fwrite($file,$f);
				fclose($file);
				$html=str_replace($i,"$dir/$name",$html);
			}		
			$h=substr($h,$pos+strlen($delim));
		}

		return $html;
	}
}

$plain=fetch_images_during_save($plain);

?>