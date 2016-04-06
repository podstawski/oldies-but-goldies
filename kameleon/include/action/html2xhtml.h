<?
	if (!strlen($plain)) return;


	$ppos=0;
	$nplain=$plain; // (nplain=new plain)

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
		while(1)
		{
			// e = equal

			//echo ereg_replace(" ","&nbsp;",htmlspecialchars(substr($tag,$tpos)));

			$e=strpos(substr($tag,$tpos),"=");
			if (!$e)
			{
				$newtag.=strtolower(substr($tag,$tpos));
				break;
			}
			$newtag.=strtolower(substr($tag,$tpos,$e));
			$valuepart=substr($tag,$tpos+$e+1);

			$needle=" ";
			if ($valuepart[0]=="\"") $needle="\"";
			if ($valuepart[0]=="'") $needle="'";

			$start_search=($needle==" ")?0:1;
			$value_end=strpos(substr($valuepart,$start_search),$needle);
			if (!$value_end ) $value_end=strpos(substr($valuepart,$start_search),">"); 

			
			if (substr($valuepart,0,2)=="$needle$needle" 
				|| $valuepart[0]==" " 
				|| $valuepart[0]==">" 
				|| $valuepart[0]=="\t" ) 
			{
				$value="";
			}
			else 
				$value=substr($valuepart,$start_search,$value_end);

			$newtag.="=\"$value\"";
			$tpos+=$e+strlen($value)+2*$start_search+1;
			
			//echo " <font color=blue>e=$e ve=$value_end value='$value' </font><br>";
			
		}
		//echo "<br>".htmlspecialchars($tag)." <font color=red>=======></font> ".htmlspecialchars($newtag)."<br>"; $newtag=$tag;
		
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
