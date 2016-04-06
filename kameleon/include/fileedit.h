<html>
<head>
    <title>KAMELEON: <?echo label("Simple plain editor");?></title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/fileedit.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?echo $CHARSET?>">
    <script src="codemirror/lib/codemirror.js" type="text/javascript"></script>
	<link rel="stylesheet" href="codemirror/lib/codemirror.css">
	<script src="codemirror/mode/javascript/javascript.js"></script>
	<script src="codemirror/mode/css/css.js"></script>
	<script src="codemirror/mode/xml/xml.js"></script>
	<script src="codemirror/mode/plsql/plsql.js"></script>
	<script src="codemirror/mode/php/php.js"></script>
	<script src="codemirror/mode/scheme/scheme.js"></script>
	<link rel="stylesheet" href="codemirror/theme/default.css">
</head>

<?

	include_once('include/file.h');
	$galeria=$_REQUEST[galeria];
	include('include/ufiles_const.h');

  $_REQUEST["plik"]=str_replace($rootdir."/","",$_REQUEST["plik"]);

	$wf_id=0+webfile($_REQUEST[plik],$galeria);
	$wf_accesslevel=webfile($_REQUEST[plik],$galeria,'wf_accesslevel');

	$action="ZapiszPlik";
	
	if ($wf_accesslevel > $kameleon->current_server->accesslevel)
	{
		$action='';
	}	

	eval("\$INCLUDE_PATH=\"$DEFAULT_PATH_INCLUDE\";");

	if (file_exists("$rootdir/../".$kameleon->user[username].'/'.$_REQUEST[plik])) $rootdir="$rootdir/../".$kameleon->user[username];

	$plik=implode('',file($rootdir.'/'.$_REQUEST[plik]));

	$plik=htmlspecialchars($plik);
	
	$path_info = pathinfo($rootdir.'/'.$_REQUEST[plik]);
  $ext = $path_info['extension'];

	
?>



<body>
<form method="post" action="<? echo $SCRIPT_NAME?>" id="fileeditor" name="edit_form">
<input name="plik" type="hidden" value="<? echo $_REQUEST[plik]?>">
<input name="galeria" type="hidden" value="<? echo $galeria?>">
<input name="action" type="hidden" value="<? echo $action?>">

	
<div class="km_toolbar">
  <ul>
    <li><a class="km_icon km_iconi_save" href="javascript:document.getElementById('fileeditor').submit();"><?=label('Save')?></a></li>
  </ul>
</div>
<div class="codeplace">
  <textarea style="width:100%; height:450px" name="file_contents" id="code"><?echo $plik?></textarea>
</div>
</form>

<script type="text/javascript">
  var editor = CodeMirror.fromTextArea(document.getElementById('code'), {
    <?php
    
    switch ($ext)
    {
      case 'js':
        echo "
        mode : \"javascript\",
        ";
        break;
      
      case 'sql':
        echo "
        mode: \"plsql\",
        ";
        break;
      
      case 'xml':
        echo "
        mode: \"xml\",
        ";
        break;
        
      case 'css':
        echo "
        mode: \"css\",
        ";
        break;
        
      case 'html':
      case 'h':
      case 'php':
      case 'php3':
      case 'php4':
      case 'php5':
      default:
        echo "
        mode: \"scheme\",
        ";
        break;
    }
    
    ?>
    tabMode : "shift"
  });
</script>


</body>
</html>
