<?
	if ($dontdisplayanykameleonhtml) return;
	if (ereg("\.(ht|x)m[l]*\$",$WEBPAGE->file_name) ) return;

	ob_start();

	if ($CONST_POST_H || file_exists("$SZABLON_PATH/post.h")) 
	{

		$post_h="post.h";
		if (strlen($CONST_POST_H)>1) $post_h=$CONST_POST_H;

		$slash=strlen($INCLUDE_PATH)?"/":"";
		echo "<"; echo "?";
		echo "include(\"$INCLUDE_PATH${slash}$post_h\");";
		echo "?"; echo ">";
	}

	if ($KAMELEON_MODE && !$editmode && $CONST_REMOTE_INCLUDES_ARE_HERE && !$_C_REMOTE_HTML_INCLUDED) 
	{
		//kameleon_include("nop.h","pagetype=$WEBPAGE->type");
	}


	for ($_m=0;$_m<count($C_MODULES) && is_Array($C_MODULES);$_m++)
	{
		$_module_dir=$C_MODULES[$_m];

		$post_h="post.h";
		if (strlen($MODULES->$_module_dir->scripts->post)) $post_h=$MODULES->$_module_dir->scripts->post;

	

		if (!$POST_S["$_module_dir"] && file_exists("modules/@$_module_dir/$post_h") )
		{
			echo "<?php \$INCLUDE_PATH=\"$INCLUDE_PATH/@$_module_dir\"; include(\"$INCLUDE_PATH/@$_module_dir/$post_h\"); \$INCLUDE_PATH=\"$INCLUDE_PATH\"; ?>";
			$POST_S["$_module_dir"]=1;
		}
		
	}

        if ($KAMELEON_MODE) ob_end_clean();
        else ob_end_flush();
?>