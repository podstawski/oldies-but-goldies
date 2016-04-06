<?
return;

$mid = "21";
$LANGS[]="y";
//$LANGS[]="s";
/**/
$MENUS[]=15;
$MENUS[]=21;
$MENUS[]=22;
$MENUS[]=23;
$MENUS[]=24;
$MENUS[]=101;
$MENUS[]=119;

$MENUS[]=35;
$MENUS[]=36;
$MENUS[]=37;

for ($l=0;$l<count($LANGS);$l++) 
{
	$_lang = $LANGS[$l];
	for ($m=0;$m<count($MENUS);$m++) 
	{
		$mid = $MENUS[$m];
		echo "<b>$_lang:$mid</b><br>";
		$sql = "SELECT * FROM weblink WHERE menu_id=".$mid." AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver ;
		$res = $adodb->execute($sql);		
		for ($i=0;$i<$res->RecordCount();$i++) 
		{
			parse_str(ado_explodename($res, $i));
			$alt_arr = explode(":",$alt);

			$msg_msg="";
			$query="SELECT msg_msg FROM messages WHERE msg_label='".$alt_arr[1]."' AND msg_lang='$_lang'";
			parse_str(query2url($query));
			
			if (!strlen($msg_msg)) continue;
			
			$new_alt = $alt_arr[0].":".strtolower($msg_msg);
			echo $alt.">>".$new_alt."<br>";
			$sql_u	= "UPDATE weblink SET alt='".$new_alt."' WHERE sid=".$sid;
			//$adodb->execute($sql_u);
			
			$sql_u2="UPDATE webpage SET title='".$new_alt."' WHERE id=".$page_target." AND server=".$SERVER_ID." AND lang='".$_lang."' AND ver=".$ver;
			echo "<i>".$sql_u2."</i><br><br>";
			//$adodb->execute($sql_u2);
		}
	}
}	
return;
?>