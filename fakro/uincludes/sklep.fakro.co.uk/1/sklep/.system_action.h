<?

	global $hf_editmode,$_system_action,$_system_obiekt,$WEBPAGE;

	$so_id=0;
	$query="SELECT so_id,so_nazwa,so_parent 
			FROM system_obiekt WHERE so_server=$SERVER_ID AND so_klucz='p_$page'";
	parse_str(query2url($query));

	$prev=$WEBPAGE->prev;
	if ($page==0) $prev=-1;
	$nazwa=addslashes(stripslashes($WEBPAGE->title));


	if (!$so_id)
	{	
		
		$query="INSERT INTO system_obiekt (so_server,so_klucz,so_parent,so_nazwa)
			VALUES ($SERVER_ID,'p_$page',$prev,'$nazwa');
			SELECT so_id FROM system_obiekt WHERE so_server=$SERVER_ID AND so_klucz='p_$page'";
		parse_str(query2url($query));

		if ($prev)
		{
			
			$query="INSERT INTO system_acl_obiekt (sao_grupa_id,sao_server,sao_klucz)
					SELECT sao_grupa_id,sao_server,'p_$page' 
					FROM system_acl_obiekt WHERE sao_klucz='p_$prev' 
					AND sao_server=$SERVER_ID";
			parse_str(query2url($query));
			
		}
	}
	else 
	{

		if ($prev!=$so_parent || stripslashes($so_nazwa)!=stripslashes($nazwa))
		{
			$query="UPDATE system_obiekt SET so_parent=$prev, so_nazwa='$nazwa' WHERE so_id=$so_id";
			parse_str(query2url($query));
		}
		
	}
	
	if (!$hf_editmode) return;
?>

<form method="post" action="<?echo $self?>">
<input name="_system_action[0]" type="hidden" value=0>
<input name="_system_obiekt[0]" type="hidden" value=0>

<div style="height:200px; overflow:auto">
<table width=700><tr>
<? if ($so_id) { ?>
<td valign="top"><b>Prawa do strony maj±:</b><br>
<?
	$query="SELECT sg_nazwa,sg_id FROM system_grupa WHERE sg_server=$SERVER_ID ORDER BY sg_nazwa";
	$result = pg_exec($query);
	for ($i=0;$i<pg_numrows($result);$i++)
	{
		parse_str(pg_ExplodeName($result,$i));

		
		$query="SELECT count(*) AS c FROM system_acl_obiekt WHERE sao_grupa_id=$sg_id AND sao_klucz='p_$page'
			AND sao_server=$SERVER_ID";
		parse_str(query2url($query));


		if (is_array($_system_action))
		{

			$query="";
			if ($c && !$_system_obiekt[$sg_id]) 
			{
				$c=0;
				$query="DELETE FROM system_acl_obiekt 
					WHERE sao_server=$SERVER_ID AND sao_grupa_id=$sg_id 
						AND sao_klucz='p_$page'";
			}
			if (!$c && $_system_obiekt[$sg_id]) 
			{
				$query="INSERT INTO system_acl_obiekt 
					(sao_server,sao_grupa_id,sao_klucz) 
					VALUES ($SERVER_ID,$sg_id,'p_$page')";
				$c=1;
			}
			if (strlen($query)) pg_exec($query);
		}
		

		$ch=$c?"checked":"";
		echo "<input $ch type=\"checkbox\" name=\"_system_obiekt[$sg_id]\" value=1> $sg_nazwa<br>";
	}

?>
</td>
<? } ?>

<td valign="top"><b>Prawa do akacji na stronie:</b><br>
<?
	$dh = opendir("$SKLEP_INCLUDE_PATH/action"); 
	while (($file = readdir($dh)) !== false) 
	{
		if ($file[0]==".") continue;

		$actions[]=$file;
	}
	sort($actions);

	foreach ($actions AS $file)
	{

		$f=explode('.',$file); $file=$f[0];

		$query="SELECT count(*) AS c FROM system_action 
			WHERE sa_action='$file' AND sa_page_id=$page AND sa_server=$SERVER_ID ";
		parse_str(query2url($query));

		if (is_array($_system_action))
		{
			$query="";
			if ($c && !$_system_action[$file]) 
			{
				$c=0;
				$query="DELETE FROM system_action WHERE sa_action='$file' AND sa_server=$SERVER_ID AND sa_page_id=$page";
			}
			if (!$c && $_system_action[$file]) 
			{
				$query="INSERT INTO system_action (sa_server,sa_page_id,sa_action) VALUES ($SERVER_ID,$page,'$file')";
				$c=1;
			}
			if (strlen($query)) pg_exec($query);
		}

		$ch=$c?"checked":"";
		echo "<input $ch type=\"checkbox\" name=\"_system_action[$file]\" value=1> $file<br>";
		
	}
	closedir($dh);

?>

</td></tr>
</table>
</div>
<input type="submit" value="Przypisz prawa" class="formbutton">
</form>
