<?php
if ($dontdisplayanykameleonhtml) return;

if (ereg("\.(ht|x)m[l]*\$",$WEBPAGE->file_name)) return;
ob_start();

if ($_C_REMOTE_ACTION_INCLUDED==1) return;
$_C_REMOTE_ACTION_INCLUDED=1;


if (is_Object($WEBPAGE) && ($CONST_ACTION_H || file_exists("$SZABLON_PATH/action.h")) )
{
    $slash=strlen($INCLUDE_PATH)?"/":"";
    echo "<"; echo "?php";
    if (!$GLOBAL_PRE_DONE)
    {
		$p="page=$page&ver=$ver&lang=$lang&IMAGES=$IMAGES&UIMAGES=$UIMAGES&INCLUDE_PATH=$INCLUDE_PATH";
		echo " parse_str(\"$p\");";
    }
	$action_h="action.h";
	if (strlen($CONST_ACTION_H)>1) $action_h=$CONST_ACTION_H;
    echo " include(\"$INCLUDE_PATH${slash}$action_h\");";
    echo "?>"; 
}

if (is_Array($C_MODULES) )
{
	echo "<?php ";
	for ($_m=0;$_m<count($C_MODULES) ;$_m++)
	{
		$_module_dir=$C_MODULES[$_m];
		$action_h="action.h";
		if (strlen($MODULES->$_module_dir->scripts->action)) $action_h=$MODULES->$_module_dir->scripts->action;

		if (!$ACTION_S["$_module_dir"] && file_exists("modules/@$_module_dir/$action_h") )
		{
			echo "\$INCLUDE_PATH=\"$INCLUDE_PATH/@$_module_dir\"; include(\"$INCLUDE_PATH/@$_module_dir/$action_h\") ; \$INCLUDE_PATH=\"$INCLUDE_PATH\"; ";
			$ACTION_S["$_module_dir"]=1;
		}
	}
	echo "?>";
	
}

if ($KAMELEON_MODE) ob_end_clean();
else ob_end_flush();

