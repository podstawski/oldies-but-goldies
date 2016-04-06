<? if (!$editmode) echo "<script>location.href='/';</script>"; ?>
<html>
<head>
    <title>KAMELEON: <?echo label("Page Explorer");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">

    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
<STYLE><!-- 
	A			{ text-decoration: none; color:#000000;}
	A:hover		{ color: #ff6a00; }
--></STYLE>

<?
  include_js("jquery-1.4");
  include_js("jquery.tree");
  include_js("jquery.tree.contextmenu");
?>
    
</head>
<body bgcolor="#ffffff" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>

<?
	include("include/helpbegin.h");

   include("include/navigation.h");
?>
<div class="km_toolbar">
  <ul>
  <?
    echo "<li><a href=\"#\" id=\"km_lang_open\" class=\"km_icon km_iconi_lang_".$lang."\" title=\"".label($lang)."\">".label($lang)."</a></li>";
  ?>
  </ul>
</div>

<?
include ("include/lang-change.h");

	$TreeFollowLink=1;
//	include ("include/tree_fun.h");
//	drzewo(-1,$referpage+0,$lang);
	$node=$referpage+0;
	include("include/explorer.h");

?>


<?
	include("include/helpend.h");
?>

</body>
</html>

<form name=gotopage method=get action="index.<?echo $KAMELEON_EXT?>" >
	<input type=hidden name=page value=0>
</form>

<form name=ZbudujDrzewo method=post action="<?echo $SCRIPT_NAME?>" >
	<input type=hidden name=action value=ZbudujDrzewo>
</form>


<script language="javascript">
	function ZbudujDrzewoConfirm()
	{
		if (confirm("<?echo label("Are you sure to rebuild all page relations")?> ?")) document.ZbudujDrzewo.submit();
	}
</script>
