<?
	if ($C_MIKOLAJ_EXPERIMENTAL)
	{
		include("include/tree_mikolaj.h");
		return;
	}
?>
<html>
<head>
    <title>KAMELEON: <?echo label("Webpage explorer");?></title>
    <link href="kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type"
        content="text/html; charset=<?echo $CHARSET?>">
<STYLE><!-- 
	A			{ text-decoration: none; color:#000000;}
	A:hover		{ color: #ff6a00; }
--></STYLE>
<?	include ("include/tree_js.h"); ?>
</head>
<body bgcolor="white" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>
<?
if (strpos($node,":"))
{
	$_node=explode(":",$node);
	$node=$_node[1];
	$dest_lang=$_node[0];		
}
else $dest_lang=$lang;

$n=($dest_lang==$lang)?$node:-1;

$node=$n+0;
include("include/explorer.h");
?>
</body>
</html>
