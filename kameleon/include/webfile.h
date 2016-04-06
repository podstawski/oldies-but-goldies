<html>

<head>
  <title>KAMELEON: <? echo label("Files"); ?></title>
  <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/fileeditor.css" rel="stylesheet" type="text/css">
  <meta http-equiv="Content-Type"	content="text/html; charset=<?echo $CHARSET?>">

	<script language="Javascript">

		function checkAccessLevel(obj,limit)
		{
			if (obj.value>limit)
			{
				alert('<?echo addslashes(label('You are not permited to set access level'))?> - '+obj.value);
				obj.value=limit;
			}
		}

	</script>

</head>

<body>

  <?
  	if (!strlen($wf_file))
  	{
  		echo ('</body></html>');
  		exit();
  	}
  
  	include_once('include/file.h');
  	$wf_id=webfile($wf_file,$wf_gal);
  
  	if (!$wf_id)
  	{
  		echo '<div class="error">';
  		echo label("Insufficient rights");
  		echo ('</div></body></html>');
  		exit();
  	}
  	$query="SELECT * FROM webfile WHERE wf_id=".$wf_id;
  	parse_str(ado_query2url($query));
  
  
  ?>
  <div class="wfile">
    <form method="post" action="webfile.php">
      <input type="hidden" name="wf_id" value="<?echo $wf_id?>">
      <input type="hidden" name="km_action" value="ZapiszFile">
      <?echo label("Access level")?>: <input class="k_input" type="text" style="width: 50px;" onchange="checkAccessLevel(this,<? echo 0+$kameleon->current_server->accesslevel?>)" name="wf_accesslevel" value="<?echo $wf_accesslevel?>">
      <?echo label("Redirect page")?>: <input class="k_input" type="text" style="width: 50px;" name="wf_page" value="<?echo $wf_page?>">
      <input type="image" src="/<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/img/icons/i_save_n.gif" border="0" alt="<?echo label("Save")?>">
    </form>
  </div>
</body>
</html>