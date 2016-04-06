<?
@include_once("include/xml_fun.h");

function getmicrotime()
{ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
}

//$_kameleon_debug=1;
if ($_kameleon_debug) $t=getmicrotime();

if (!is_Object($MODULES) && is_array($C_MODULES) )
{
	for ($_m=0;$_m<count($C_MODULES);$_m++)
	{
		$m_name=$C_MODULES[$_m];
		$m_path="$SZABLON_PATH/modules/$m_name.xml";
		$i_p=strlen($INCLUDE_PATH)?$INCLUDE_PATH:".";
		if (!file_exists($m_path)) $m_path="$i_p/modules/@$m_name/.$m_name.xml";
		if (!file_exists($m_path)) $m_path="$i_p/modules/@$m_name/$m_name.xml";
		if (!file_exists($m_path)) $m_path="$i_p/$m_name.xml";
		if (!file_exists($m_path)) continue;
		$xml=read_file($m_path);
		$obj=xml2obj($xml);
		$xml='';

		$MODULES->$m_name=$obj->$m_name;
	}
}

if ($_kameleon_debug) echo getmicrotime() - $t;





function module_query_param($MODULE_ACTION)
{
	global $adodb;

	$warunek="";
	$warunek_afterinsert="";
	$insert_what="";
	$insert_values="";
	reset($MODULE_ACTION[key]);
	if (is_array($MODULE_ACTION[key])) 
	 while(list($key_key,$key_val) = each($MODULE_ACTION[key]))
	 {
		$k_v=$key_val;
		
		$key_val=kameleon_global($key_val);
		if ( (strstr($key_val,"(") || strstr($key_val,"\$")) && $key_val!="(NULL)" )
		{
			$key_val=kameleon_global("GLOBAL($key_val)");
		}
		if($adodb->debug) echo "<font color='green'>(condition creator): $key_key => $k_v => $key_val</font><br>";

		if (!strlen($key_val)) continue;
		$key_val=toText($key_val);

		$quote=($key_val[0]=="(")?"":"'";
		$key_val=eregi_replace("\(NULL)","NULL",$key_val);
		$eq=($key_val=="NULL")?"IS":"=";

		if (strlen($warunek)) 
		{
			$warunek.=" AND ";
			$warunek_afterinsert.=" AND ";
		}

		if ($key_val!="NULL" && strlen($insert_what) ) $insert_what.=",";
		if ($key_val!="NULL" && strlen($insert_values) ) $insert_values.=",";
		
		$warunek.="$key_key $eq $quote$key_val$quote";

		if ($key_val!="NULL") $warunek_afterinsert.="$key_key $eq $quote$key_val$quote";
		else $warunek_afterinsert.="$key_key IN (SELECT max($key_key) FROM $MODULE_ACTION[table])";

		if ($key_val!="NULL") $insert_what.="$key_key";
		if ($key_val!="NULL") $insert_values.="$quote$key_val$quote";

	}


	if($adodb->debug) echo "<font color='green'>(condition creator) cond: $warunek</font><br>";
	if($adodb->debug) echo "<font color='green'>(condition creator) cond_afterinsert: $warunek_afterinsert</font><br>";

	$obj->warunek_afterinsert=$warunek_afterinsert;
	$obj->warunek=$warunek;
	$obj->insert_what=$insert_what;
	$obj->insert_values=$insert_values;
	return ($obj);
}

	
function module_select($m_obj,$warunek="")
{
	global $adodb;


	$MODULE_ACTION["table"] = $m_obj->action->table; 
	$MODULE_ACTION["form"]	= $m_obj->action->form;
	$MODULE_ACTION["xml"]	= $m_obj->action->xml;
	$MODULE_ACTION["key"]	= $m_obj->action->key;

	if (!strlen($MODULE_ACTION[table])) return;

	if (!strlen($warunek))
	{
		$obj=module_query_param($MODULE_ACTION);
		$warunek=$obj->warunek;
		$insert_what=$obj->insert_what;
		$insert_values=$obj->insert_values;
		if (!strlen($warunek)) return;
	}


	$query="SELECT * FROM $MODULE_ACTION[table] WHERE $warunek";
	$res=$adodb->Execute($query);
	if ($res && $adodb->debug) echo "(record count) ".$res->RecordCount()."<br>";

	if (!$res || ($res && !$res->RecordCount()) ) return(0);

	$res->Move(0);
	$data=$res->FetchRow();

	$cmd="global \$".$MODULE_ACTION["form"].";";

	eval($cmd);

	$xml=$MODULE_ACTION["xml"];
	$form=$MODULE_ACTION["form"];
	
	while( list($key_key,$key_val) = each($data) )
	{
		$key_val=trim($key_val);
		if ($key_key==$xml && strlen($key_val) )
		{
			$x=xml2obj($key_val);
			if (is_object($x->xml))
			 while( list($x_key,$x_val) = each($x->xml) )
			 {
				$cmd="\$$form"."[".$x_key."] = \$x_val ;";			
				eval($cmd);
			 }
			continue;
		}
		$cmd="\$$form"."[".$key_key."] = \$key_val ;";
		eval($cmd);
	}


	while( is_Array($m_obj->action->select) && list($key,$val) = each($m_obj->action->select) )
	{
		foreach(array_keys($data) AS $col)
		{
			$val=eregi_replace("$col","\$data[${col}]",$val);
		}
		$str =  "\$$form"."[$key] = $val ;";
		eval ($str);
	}
	return (1);
}

function module_update($m_obj,$warunek="")
{
	global $adodb;

	$MODULE_ACTION["table"]= $m_obj->action->table; 
	$MODULE_ACTION["form"]	= $m_obj->action->form;
	$MODULE_ACTION["xml"]	= $m_obj->action->xml;
	$MODULE_ACTION["key"]	= $m_obj->action->key;

	if (!strlen($MODULE_ACTION[table])) return;

	if (!strlen($warunek))
	{
		$obj=module_query_param($MODULE_ACTION);
		$warunek=$obj->warunek;
		$warunek_afterinsert=$obj->warunek_afterinsert;
		$insert_what=$obj->insert_what;
		$insert_values=$obj->insert_values;
	}

	if (!strlen($warunek)) return;

	$query="SELECT * FROM $MODULE_ACTION[table] WHERE $warunek";
	$res=$adodb->Execute($query);

	if ($res && $adodb->debug) echo "(record count) ".$res->RecordCount()."<br>";

	if (!$res || ($res && !$res->RecordCount()) ) 
	{
		$sql="INSERT INTO $MODULE_ACTION[table] ($insert_what) VALUES ($insert_values)";
		if ($adodb->Execute($sql)) logquery($sql);

		$warunek=$warunek_afterinsert;

		$query="SELECT * FROM $MODULE_ACTION[table] WHERE $warunek";
		$res=$adodb->Execute($query);
	}
	

	if (!$res || ($res && !$res->RecordCount()) ) return;

	$res->Move(0);
	$data=$res->FetchRow();


	$xml=$MODULE_ACTION["xml"];
	$cmd="global \$".$MODULE_ACTION["form"].";\$form_fields=\$".$MODULE_ACTION["form"].";";
	eval($cmd);
	
	if (!is_array($form_fields)) return;
	if (strlen($xml)) $form_fields[$xml]="";
	
	$set_val="";

	while( list($key_key,$key_val) = each($form_fields) )
	{
		if ($key_key==$xml) continue;
		$key_val=addslashes(stripslashes($key_val));
		if ( array_key_exists($key_key,$MODULE_ACTION[key]) ) 
		{
			//if (strlen($key_val)) $warunek.=" AND $key_key='$key_val'";
			continue;
		}
		if ( array_key_exists($key_key,$data))
		{
			if (!strlen($key_val)) $key_val="(NULL)";

			if (strlen($set_val)) $set_val.=",\n";
			$set_val.="$key_key=";
			if ($key_val[0]!="(" ) $set_val.="'";
			$set_val.=eregi_replace("\(null)","NULL",$key_val);
			if ($key_val[0]!="(" ) $set_val.="'";
			continue;
		}
		if (is_array($m_obj->action->select) && array_key_exists($key_key,$m_obj->action->select) ) 
			continue;

		$form_fields[$xml].=" <$key_key>";
		$form_fields[$xml].=addslashes(htmlspecialchars(stripslashes($key_val)));
		$form_fields[$xml].="</$key_key>\n";
	}

	if (!strlen($set_val)) return;
	if (strlen($xml) && array_key_exists($xml,$data) ) 
		$set_val.=",\n$xml='<xml>\n$form_fields[$xml]</xml>'";

	$sql="UPDATE $MODULE_ACTION[table]
		SET $set_val
		WHERE $warunek";

	//echo nl2br($sql); return;

	if ($adodb->Execute($sql)) 
	{
		logquery($sql);
		return true;
	}
	else
	{
		//$adodb->debug=1;
		//$adodb->Execute($sql);	
		//$adodb->debug=0;
		return false;
	}

}


function module_delete($m_obj,$warunek="")
{
	global $adodb;


	$MODULE_ACTION["table"] = $m_obj->action->table; 
	$MODULE_ACTION["form"]	= $m_obj->action->form;
	$MODULE_ACTION["xml"]	= $m_obj->action->xml;
	$MODULE_ACTION["key"]	= $m_obj->action->key;

	if (!strlen($MODULE_ACTION[table])) return;

	if (!strlen($warunek))
	{
		$obj=module_query_param($MODULE_ACTION);
		$warunek=$obj->warunek;
		$insert_what=$obj->insert_what;
		$insert_values=$obj->insert_values;
		if (!strlen($warunek)) return;
	}

	$sql="DELETE FROM $MODULE_ACTION[table] WHERE $warunek";

	//echo nl2br($sql); return;

	if ($adodb->Execute($sql)) 
	{
		logquery($sql);
		return true;
	}
	else
	{
		//$adodb->debug=1;
		//$adodb->Execute($sql);	
		//$adodb->debug=0;
		return false;
	}

}

	function _display_view($obj,$what="view")
	{
		global $SZABLON_PATH;
		
		if (!strlen($obj->action->form)) return;

		$cmd="global \$".$obj->action->form.";";
		$cmd.="\$tokens = \$".$obj->action->form.";";
		eval($cmd);
		@include_once("include/parser.h");
		eval(" \$parser_start=\$obj->parser->${what}[begin]; ");
		eval(" \$parser_end=\$obj->parser->${what}[end]; ");

		$parser_template="$SZABLON_PATH/" . $obj->parser->template;
		if ( !file_exists($parser_template) ) 
			$parser_template="$obj->INCLUDE_PATH/" . $obj->parser->template;

		parser("%$parser_start%","%$parser_end%",$parser_template,$tokens);
	}

	function _display_form($obj)
	{
		_display_view($obj,"form");
	}

	function _RevertDate(&$d)
	{
		if (strlen(trim($d))!=10) return $d;
		$dd=explode("-",$d);
		$d0=trim($dd[0]);
		$d1=trim($dd[1]);
		$d2=trim($dd[2]);
		if ($d0>1000)
			$d=sprintf("%02d-%02d-%d",$d2,$d1,$d0);
		else
			$d=sprintf("%d-%02d-%02d",$d2,$d1,$d0);
		return($d);
	}


function html_txt2html_obj($html)
{
	global $MODULES;

	$module=$html;

	$bn=basename($module);
	$dn=dirname($module);
	$m_name=substr($dn,1);

	reset ($MODULES->$m_name->files);

	while ( list( $m_key, $m_val ) = each( $MODULES->$m_name->files) )
	{
		if ($bn==$m_val->file)	return $m_val;
	}
	return null;

}

?>