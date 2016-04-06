<?
	if (!strlen($plain)) return;

	$forbidden="";

	$forbidden[]=array("p","class","^ms");
	$forbidden[]=array("p","class","^fr");
	$forbidden[]=array("p","style",".");
	$forbidden[]=array("span","class","^ms");
	$forbidden[]=array("span","style",".");
	$forbidden[]=array("img","style",".");
	$forbidden[]=array("font","face",".");

	$ppos=0;
	$nplain=$plain; // (nplain=new plain)
	$script=0;
	$deleted_parts=0;

	while ($ppos<strlen($plain))
	{
		if (!strstr(substr($nplain,$ppos),"<")) break;
		
		$tag_begin=strpos(substr($nplain,$ppos),"<");
		$tag_end=strpos(substr($nplain,$ppos),">");

		$tag=substr($nplain,$ppos+$tag_begin,$tag_end-$tag_begin+1);

		if (strstr(substr($tag,1),"<")) 
		{
			$t=htmlspecialchars($tag);
			//echo "{$tag_begin-$tag_end,$t} TAG IN TAG<br>";
			break;
		}

		$newtag="";
		$tpos=0;
		$name_value_parts=0;
		while(1)
		{
			// e = equal

			//echo ereg_replace(" ","&nbsp;",htmlspecialchars(substr($tag,$tpos)));

			$begin=strtolower(substr($tag,$tpos));
			if ($begin[0]=="<")
			{
				$end=strpos($begin," ");
				if (!$end) $end=strpos($begin,">");
				$tagname=substr($begin,1,$end-1);
			}

			$e=strpos(substr($tag,$tpos),"=");
			if (!$e)
			{
				$newtag.=substr($tag,$tpos);
				//echo "\n$tag\n$newtag";
				break;
			}
			if ($begin[0]=="<")
			{
			   $pair_name=substr($tag,$tpos+strpos($begin," "),$e-+strpos($begin," "));	
			   $newtag.="<$tagname"; 
			}
			else
			   $pair_name=substr($tag,$tpos,$e);

			$pair_name=trim($pair_name);

			$valuepart=substr($tag,$tpos+$e+1);

			$needle=" ";
			if ($valuepart[0]=="\"") $needle="\"";
			if ($valuepart[0]=="'") $needle="'";

			$start_search=($needle==" ")?0:1;
			$value_end=strpos(substr($valuepart,$start_search),$needle);
			if (!$value_end ) 
			   $value_end=strpos(substr($valuepart,$start_search),">"); 

			
			if (substr($valuepart,0,2)=="$needle$needle" 
				|| $valuepart[0]==" " 
				|| $valuepart[0]==">" 
				|| $valuepart[0]=="\t" ) 
			{
				$value="";
			}
			else 
			{
				$value=substr($valuepart,$start_search,$value_end);
			}

			$forbid=0;
			for ($f=0;$f<count($forbidden);$f++)
			{
				$reg=$forbidden[$f];
				if ($reg[0]!=strtolower($tagname)) continue;

				if (eregi($reg[1],$pair_name) && eregi($reg[2],$value)) 
				{
					$forbid=1;
					break;
				}
			}
			//if ($forbid) echo "\nFORB";
			if (!$forbid) $newtag.=" $pair_name=\"$value\"";
			else $deleted_parts++;

			$tpos+=$e+strlen($value)+2*$start_search+1;
			
		}

		
		if (!$script)
		{
			$nplain=substr($nplain,0,$ppos+$tag_begin).$newtag.substr($nplain,$ppos+$tag_begin+strlen($tag));
			$ppos+=$tag_begin+strlen($newtag);
		}
		else
			$ppos+=$tag_begin+strlen($tag);


		if (strstr($newtag,"<script")) $script=1;
		if (strstr($newtag,"</script")) $script=0;

	}

	$plain=$nplain;
?>