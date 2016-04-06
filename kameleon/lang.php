<?


	include("include/request.h");

	if (file_exists('const.php')) include('const.php'); else include('const.h');


	define ('ADODB_DIR','adodb/');		
	include("include/adodb.h");
	
	include("include/const.h");

	$sql="SELECT id,lang FROM label ORDER BY label";


	$res=$adodb->execute($sql);

	for ($i=0;$i<$res->recordCount(); $i++)
	{
		parse_str(ado_explodeName($res,$i));

		$ch=$CHARSET_TAB[$lang];

		//if (strstr($ch,'ISO')) $ch=str_replace('ISO-8859-','LATIN',$ch);
		if (strstr($ch,'ISO')) $ch='LATIN2';


		if ($lastch!=$ch) 
		{
			$adodb->adodb->SetCharSet($ch);
			$chs=$adodb->adodb->GetCharSet();
			$lastch=$ch;
		}

		$sql="SELECT * FROM label WHERE id=$id";
		parse_str(ado_query2url($sql));

		if (!strlen(trim($label))) continue;

		$plik=fopen('include/lang/'.strtolower($lang).'_'.strtolower($CHARSET_TAB[$lang]).'.php','a');

		fwrite($plik,'$words[\''.trim($label).'\']=\''.trim($value)."';\n");

		fclose($plik);
	}