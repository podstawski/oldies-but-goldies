<?php
	if ($dontdisplayanykameleonhtml) return;

	global $GLOBAL_PRE_DONE;
	ob_start();

	eval("\$PATH_PAGES_PREFIX=\"$DEFAULT_PATH_PAGES_PREFIX\";");
	$PAGE_PATH=kameleon_relative_dir($WEBPAGE->file_name,"$PATH_PAGES_PREFIX$PATH_PAGES");
	if (!strlen($PAGE_PATH)) $PAGE_PATH=".";

	if (!ereg("\.(ht|x)m[l]*\$",$WEBPAGE->file_name)) if ( $CONST_PRE_H || file_exists("$SZABLON_PATH/pre.h")) 
	{

		
		echo "<?php";
		echo " \$page=\"$page\"; ";
		echo " \$ver=\"$ver\"; ";
		echo " \$lang=\"$lang\"; ";
		echo " \$tree=\"$WEBPAGE->tree\"; ";
		echo " \$INCLUDE_PATH=\"$INCLUDE_PATH\"; ";
		echo " \$MAIN_INCLUDE_PATH=\"$INCLUDE_PATH\"; ";
		echo " \$PAGE_PATH=\"$PAGE_PATH\"; ";
		echo " \$IMAGES=\"$IMAGES\"; ";
		echo " \$UIMAGES=\"$UIMAGES\"; ";
		echo " \$UFILES=\"$UFILES\"; ";
		echo " \$SERVER_ID=\"$SERVER_ID\"; ";




    	if ($WEBPAGE->next) 
		{
			$nextpage=kameleon_href("","",$WEBPAGE->next);
			echo " \$nextpage=\"$nextpage\"; ";
		}
    	if ($WEBPAGE->prev>=0) 
		{
			$prev=$WEBPAGE->prev;
			$prevpage=kameleon_href("","",$prev,false);
			echo " \$prevpage=\"$prevpage\"; ";
		}
    		if ($WEBPAGE->type) 
		{
			$pagetype=$WEBPAGE->type;
			echo " \$pagetype=$pagetype; ";
		}
		
    		if (strlen($WEBPAGE->pagekey)) 
		{
			$pagekey=$WEBPAGE->pagekey;
			echo " \$pagekey='$pagekey'; ";
		}

		$pre_h="pre.h";
		if (strlen($CONST_PRE_H)>1) $pre_h=$CONST_PRE_H;
		$slash=strlen($INCLUDE_PATH)?"/":"";

		echo "include(\"$INCLUDE_PATH${slash}$pre_h\");";
		echo "?>"; 

		$GLOBAL_PRE_DONE=1;
	}

	if (strlen($CMS_API_HOST) && is_Array($C_MODULES)) echo "<?php \$CMS_API_HOST=\"$CMS_API_HOST\";?>";


	for ($_m=0;$_m<count($C_MODULES) && is_Array($C_MODULES);$_m++)
	{
		$_module_dir=$C_MODULES[$_m];
	
		$pre_h="pre.h";
		if (strlen($MODULES->$_module_dir->scripts->pre)) $pre_h=$MODULES->$_module_dir->scripts->pre;

		if (!$PRE_S["$_module_dir"] && file_exists("modules/@$_module_dir/$pre_h") )
		{
			echo "<?php \$INCLUDE_PATH=\"$INCLUDE_PATH/@$_module_dir\"; include(\"$INCLUDE_PATH/@$_module_dir/$pre_h\"); \$INCLUDE_PATH=\"$INCLUDE_PATH\"; ?>";
			$PRE_S["$_module_dir"]=1;
		}
	}

	if (!$KAMELEON_MODE)
	{

			$query="SELECT count(*) AS rights_count FROM kameleon_acl
				WHERE ka_server=$SERVER_ID AND ka_resource_name='webpage'";
			parse_str(ado_query2url($query));

			if ( $rights_count && strlen($DEFAULT_ACL_WEBPAGE_INCLUDE) )
			{
				eval("\$ACL_WEBPAGE_INCLUDE = \"$DEFAULT_ACL_WEBPAGE_INCLUDE\";");
				eval("\$ACL_WEBPAGE_DB_FILE = \"$DEFAULT_ACL_WEBPAGE_DB_FILE\";");
				eval("\$ACL_WEBPAGE_KEY = \"$DEFAULT_ACL_WEBPAGE_KEY\";");
				eval("\$ACL_NAME = \"$DEFAULT_ACL_NAME\";");


				echo "<?php ";
				if ( !$CONST_PRE_H && !file_exists("$SZABLON_PATH/pre.h"))
				{
					echo " \$INCLUDE_PATH=\"$INCLUDE_PATH\"; ";
					echo " \$page=\"$page\"; ";
					echo " \$ver=\"$ver\"; ";
					echo " \$lang=\"$lang\"; ";
					echo " \$tree=\"$WEBPAGE->tree\"; ";
				}
				echo " @include (\"\$INCLUDE_PATH/$ACL_WEBPAGE_INCLUDE\"); ";
				echo " if (function_exists('kameleon_remote_auth')) kameleon_remote_auth(\"\$INCLUDE_PATH/$ACL_WEBPAGE_DB_FILE\",\"$ACL_WEBPAGE_KEY\",\"$ACL_NAME\"); ?>";

			}
	}


	if ($KAMELEON_MODE) ob_end_clean();
	else ob_end_flush();
