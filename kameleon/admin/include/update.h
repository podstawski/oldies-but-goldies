<?
if (!strlen($action)) return;

error_reporting(E_ERROR);

$adodb->BeginTrans();

$prev_action="";
while ( !strlen($error) && $prev_action!=$action && strlen($action)  && file_exists("include/action/$action.h") )
{
	include ("include/action/$action.h");
}

if (strlen($error) )
{
	$adodb->RollbackTrans();
	echo "<script>alert('$error'); history.back()</script>";
}
else $adodb->CommitTrans();



?>
