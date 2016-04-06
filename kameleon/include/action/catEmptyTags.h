<?
	if (!strlen($plain)) return;

	// tagname, check param (0|1), expect notag (0|1) 

	$trash[]=array("^span",1,1);
	$trash[]=array("^font",1,1);
	$trash[]=array("\?",0,0);
	$trash[]=array(":",0,0);


	$TRASH_ARR=""; $TRASH_IDX=0;
	

	if (!function_exists('trashpush'))
	{

		function trashpush($str)
		{
			global $TRASH_ARR, $TRASH_IDX;
			$TRASH_ARR[$TRASH_IDX++]=$str;
		}
		function trashtop()
		{
			global $TRASH_ARR, $TRASH_IDX;
			return($TRASH_ARR[$TRASH_IDX-1]);
		}
		function trashpop()
		{
			global $TRASH_ARR, $TRASH_IDX;
			return($TRASH_ARR[--$TRASH_IDX]);
		}
	}


	$ppos=0;
	$nplain=$plain; // (nplain=new plain)
	$script=0;
	$deleted_tags=0;

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
		$num_tag_parts=0;
		while(1)
		{
			$begin=strtolower(substr($tag,$tpos));
			if ($begin[0]=="<")
			{
				$end=strpos($begin," ");
				if (!$end) $end=strpos($begin,">");
				$tagname=strtolower(substr($begin,1,$end-1));
			}

			$e=strpos(substr($tag,$tpos),"=");
			if (!$e)
			{
				$permit=1;
				for ($t=0;$t<count($trash);$t++)
				{
					$reg=$trash[$t];

					if (eregi($reg[0],$tagname))
					{
						if (($reg[1] && !$num_tag_parts) || !$reg[1])
						{
							$permit=0;
							if ($reg[2]) trashpush("1/$tagname");
						}
						if ($reg[2] && $permit )
						{
							trashpush("0/$tagname");
						}
						break;
					}

				}
				$top=trashtop();
				if (substr($top,1)==$tagname)
				{
					trashpop();
					if ($top[0]) $permit=0;
				}

				//$newtag.=substr($tag,$tpos);
				break;
			}
			$num_tag_parts++;

			if ($begin[0]=="<")
			{
			   $pair_name=substr($tag,$tpos+strpos($begin," "),$e-+strpos($begin," "));	
			   //$newtag.="<$tagname"; 
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

			//$newtag.=" $pair_name=\"$value\"";

			$tpos+=$e+strlen($value)+2*$start_search+1;
			
		}

		$newtag=$tag;
		if (!$permit && !$script) 
		{
			$newtag="";
			$deleted_tags++;
			// echo "BYE $tag \n";
		}
		
		if (!$script)
		{
			$nplain=substr($nplain,0,$ppos+$tag_begin).$newtag.substr($nplain,$ppos+$tag_begin+strlen($tag));
			$ppos+=$tag_begin+strlen($newtag);
		}
		else
			$ppos+=$tag_begin+strlen($tag);


		if (strstr(strtolower($tag),"<script")) $script=1;
		if (strstr(strtolower($tag),"</script")) $script=0;

	}

	$plain=$nplain;

?>
