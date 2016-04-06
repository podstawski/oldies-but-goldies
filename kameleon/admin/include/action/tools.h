<style>
*	{font-family: Tahoma,Verdana,Arial; font-size:12px}
</style>

<?
	$action="";

	if ($_ver && $tool=='cp_lang2lang') $tool='cp_lang2langver';
	echo label('Running');

	$cmd="$tool.php server=$server src=$src dst=$dst";

	if ($_ver) $cmd.=" ver=$_ver";

	
	echo " tools/$cmd <br>\n";
	echo label('Please wait')." ...<br>\n";

	flush();
	ob_flush();
	flush();

	echo "<b><pre>";
	chdir ('../tools');
	system("$PHP_EXE $cmd");
	echo "</pre></b>";
?>
<br>
<input type="button" value=" OK " onClick="top.location.href='servers.php'">
<?
exit();
?>