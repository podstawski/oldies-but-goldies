<?
if (function_exists("xml2obj")) return;



function kameleon_xml_build_path($level)
{
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;

	$path="";
	for ($i=0;$i<=$level;$i++)
	{
		if ($i) $path.="->";
		$path.=$kameleon_xml_stack[$i];
	}
	return($path);
}

function kameleon_xml_tag_start($parser, $name, $attrs)
{
	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
	global $kameleon_tag_end_extras,$kameleon_tag_ignore;

	$kameleon_xml_idx++;	
	$kameleon_xml_stack[$kameleon_xml_idx]=$name;
	$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);

	eval("if (isset(\$$kameleon_xml_path)) \$arr_req=1;");
	if ($arr_req)
	{
		eval("if (is_array(\$$kameleon_xml_path)) \$arr_exists=1;");
		if (!$arr_exists)
		{
			eval("\$value=\$$kameleon_xml_path ;");
			if (!strlen($value)) $arr_req=0;
			if ($arr_req) eval("\$$kameleon_xml_path = array(\$$kameleon_xml_path) ;");
		}
		if ($arr_req)
		{
			eval("\$arr_size=sizeof(\$$kameleon_xml_path) ;");
			$kameleon_xml_stack[$kameleon_xml_idx]=$name."[$arr_size]";
			$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);	
		}
	}

	if (strlen($attrs[id]))
	{
		$kameleon_xml_stack[$kameleon_xml_idx]=$name."[".$attrs[id]."]";
		$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);	
	}
		 
	$kameleon_tag_ignore=0;

	switch ($attrs[action])
	{
		case "label":
			$kameleon_tag_begin_extras="GLOBAL(label(\"";
			$kameleon_tag_end_extras="\"))";
			break;

		case "global":
			$kameleon_tag_begin_extras="GLOBAL(";
			$kameleon_tag_end_extras=")";
			break;

		case "function":
			//$kameleon_tag_ignore=function_exists($attrs[name])?0:1 ;
			if ($kameleon_tag_ignore) break;
			$kameleon_tag_begin_extras="$attrs[name](";
			$kameleon_tag_end_extras=")";
			break;
	
		default:
			$kameleon_tag_begin_extras="";
			$kameleon_tag_end_extras="";
			break;
	}

	if ($kameleon_tag_ignore) return;

	eval("\$$kameleon_xml_path = \"\";" );
	$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$kameleon_tag_begin_extras ;" ;
	eval( $code);

}

function kameleon_xml_tag_end($parser, $name)
{
	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
	global $kameleon_tag_end_extras,$kameleon_tag_ignore;

	

	if (!$kameleon_tag_ignore && strlen($kameleon_tag_end_extras))
	{
		$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$kameleon_tag_end_extras ;";
		eval( $code);
		$kameleon_tag_end_extras="";
	}

	$kameleon_xml_idx--;
	$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);

}

function kameleon_xml_data($parser,$data)
{
	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
	global $kameleon_tag_ignore;

	if (!strlen(trim($data)) || $kameleon_tag_ignore) return;

	$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$data ;" ;
	eval( $code);
}

function xml2obj($xml)
{
	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;

	$kameleon_xml_obj="";

	$kameleon_xml_idx=0;	
	$kameleon_xml_stack[$kameleon_xml_idx]="kameleon_xml_obj";
	$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);

	
	$parser = xml_parser_create(); 
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0); 
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1); 
	xml_parser_set_option($parser,XML_OPTION_SKIP_TAGSTART ,1);


	xml_set_element_handler($parser, "kameleon_xml_tag_start", "kameleon_xml_tag_end"); 
	xml_set_character_data_handler($parser, "kameleon_xml_data");
	if (!xml_parse($parser,$xml,1))
	{
			
		$wynik=xml_error_string(xml_get_error_code($parser));
		$wynik.="(line: ".xml_get_current_line_number($parser);	
		$wynik.=")";
		return $wynik;
	}
	xml_parser_free($parser); 

	return $kameleon_xml_obj;
}


function obj2xml($obj,$XmlTag="xml",$depth=0)
{
	$wynik="";

	if (strlen($XmlTag))
	{
		$bXmlTag=true;
		if (is_Array($obj))
		{
			$ak=array_keys($obj);
			if ($ak[0]=="0") $bXmlTag=false;
		}
	}


	if ($bXmlTag) 
	{
		for ($d=0; $d<$depth; $d++) $wynik.="\t";
		$wynik.="<$XmlTag>\n";
	}

	if ( is_Array($obj) || is_Object($obj) )
	{
		while(list($k,$v)=each($obj))
		{
			if (strlen($XmlTag) && is_Integer($k)) $k=$XmlTag;

			
			$bList=false;
			if (is_Array($v))
			{
				$ak=array_keys($v);
				if ($ak[0]=="0") $bList=true;
			}
			
			if (!$bList) 
			{
				for ($d=0; $d<$depth; $d++) $wynik.="\t";
				$wynik.="<$k>";
			}
			if ( is_Array($v) || is_Object($v) )
			{
				$tag="";
				if (!$bList) $wynik.="\n";
				else $tag=$k;
				$wynik.=obj2xml($v,$tag,$depth+1);
			}
			else
				$wynik.=htmlspecialchars($v);

			if (!$bList)
			{
				for ($d=0; $d<$depth && (is_Array($v) || is_Object($v)); $d++) $wynik.="\t";
				$wynik.="</$k>\n";
			}
		}
	}
	if ($bXmlTag) 
	{
		for ($d=0; $d<$depth; $d++) $wynik.="\t";
		$wynik.="</$XmlTag>\n";
	}
	
	return $wynik;
}
?>