<?
	if (!strlen($MODULE_PATH)) $MODULE_PATH=$INCLUDE_PATH;

	include_once("$MODULE_PATH/crmfun.h");

	$CONST_TASK_UFILES=".task/.\".obj_id_on_page(\$page).\"/.\$sid";
	$CONST_SENDMAIL_UFILES=".sendmail/.\$sid";

?>
<script src="<?echo $INCLUDE_PATH?>/crmfun.js"></script>