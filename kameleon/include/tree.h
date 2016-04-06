<html>
<head>
    <title>KAMELEON: <?echo label("Webpage explorer");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
        
    <?
      include_js("jquery-1.4");
      include_js("jquery-ui.core.min");
      include_js("jquery-ui.sortable.min");
      include_js("draging");
      include_js("jquery.tree");
      include_js("jquery.tree.contextmenu");
    ?>
<STYLE>
<!-- 
	A			{ text-decoration: none; color:#000000;}
	A:hover		{ color: #ff6a00; }
-->
</STYLE>
<script type="text/javascript">
	// UWAGA wszystkie odwo³ania do "parenta"
	// musza byæ robione przez "okno" ;)
	var okno = parent;
</script>
</head>
<body bgcolor="white" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>
<?
if (strpos($node,":"))
{
	$_node=explode(":",$node);
	$node=$_node[1];
	$dest_lang=$_node[0];		
	$lang=$_node[0];
}
else $dest_lang=$lang;

$n=($dest_lang==$lang)?$node:-1;
$node=$n+0;

include("include/explorer.h");
?>
</body>
</html>
