<?
	$action="";

	
	$filename=$save_and_restore_dir."/".$paste;
	
	
	$sql=file_get_contents($filename);
	
	
	$sql=str_replace($CONST_EXPORT_SERVER_TOKEN,$SERVER_ID,$sql);
	$sql=str_replace($CONST_EXPORT_VER_TOKEN,$ver,$sql);
	$sql=str_replace($CONST_EXPORT_LANG_TOKEN,$lang,$sql);
	$sql=str_replace($CONST_EXPORT_PAGE_TOKEN,$page_id,$sql);
	$sql=str_replace($CONST_EXPORT_NL_TOKEN,"\n",$sql);
	
	$adodb->Execute($sql);
	
