<?


function debug_window($txt)
{
	static $opened;
	static $time;

	echo '<script lang="JavaScript">'."\n";
	if (!$opened) 
	{
		echo "
		var debug_window;
		debug_window=open('','','width=400,height=250,top=200,left=200');
		"; 
		$time=time();
	}
	if ($opened && !strlen($txt))
		echo "
		debug_window.close();
		";

	$txt=str_replace("'","\\'",$txt);

	$txt.='<br><br>'.(time()-$time);
	
	echo "
		debug_window.document.write('$txt');
		debug_window.document.close();
		";	

	$opened=1;


	echo "\n</script>






";
	ob_flush();
	flush();
}


?>
