<?
	$action="";

	function skopiuj_obj_sql($tabela,$where)
	{
		global $adodb;
		global $SERVER_ID,$ver,$lang;
		global $CONST_EXPORT_SERVER_TOKEN, $CONST_EXPORT_VER_TOKEN, $CONST_EXPORT_LANG_TOKEN, $CONST_EXPORT_PAGE_TOKEN;
		global $CONST_EXPORT_NL_TOKEN,$CONST_EXPORT_USER_TOKEN;

		$sql="SELECT * FROM $tabela WHERE $where";	
		$export_sql='';
		
		$a=ado_ObjectArray($adodb,$sql);
		if (is_array($a)) foreach ($a AS $export)
		{
			$inserts=array();
			$values=array();
			foreach ($export AS $k=>$v)
			{
				if ($k=='sid') continue;
				if ($k=='uniqueid') continue;
				if ($k=='autor_update') continue;
				if ($k=='nd_update') continue;
				if (!strlen($v)) continue;
				
				if ($k=='server') $v=$CONST_EXPORT_SERVER_TOKEN;
				if ($k=='ver') $v=$CONST_EXPORT_VER_TOKEN;
				if ($k=='lang') $v=$CONST_EXPORT_LANG_TOKEN;
				if ($k=='page_id') $v=$CONST_EXPORT_PAGE_TOKEN;
				
				if ($k=='autor') $v=$CONST_EXPORT_USER_TOKEN;
				
				if ($k=='plain')
				{
					$v=preg_replace('/[\r\n]/',$CONST_EXPORT_NL_TOKEN,$v);
					$v=preg_replace("#uimages/$SERVER_ID/[0-9]+#","uimages/$CONST_EXPORT_SERVER_TOKEN/$CONST_EXPORT_VER_TOKEN",$v);
					$v=preg_replace("#ufiles/$SERVER_ID-att#","ufiles/$CONST_EXPORT_SERVER_TOKEN-att",$v);				
				}
				
				$inserts[]=$k;
				$values[]="'".addslashes($v)."'";
				
				if ($k=='menu_id' && $tabela=='webtd' || $k=='submenu_id' && $tabela=='weblink' )
				{
					$export_sql.=skopiuj_obj_sql('weblink',"server=$SERVER_ID AND ver=$ver AND lang='$lang' AND menu_id=$v");
				}
		
				
				
			}
			
			$export_sql.="\n\nINSERT INTO $tabela\n(".implode(',',$inserts).")\nVALUES (".implode(',',$values).");";
		}		
		
		return $export_sql;
	}


	$ca=explode(':',$_REQUEST['paste']);

	if (!file_exists($save_and_restore_dir)) mkdir($save_and_restore_dir);

	$filename=$save_and_restore_dir."/hf-".$ca[1].'-'.$ca[2].$ca[3].'.sql';

	$export_sql=skopiuj_obj_sql('webtd',"server=$SERVER_ID AND ver=$ver AND lang='$lang' AND page_id=$ca[3]");
	
	$plik=fopen($filename,'w');
	fwrite($plik,$export_sql);
	fclose($plik);

