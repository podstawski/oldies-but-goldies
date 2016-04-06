<?
	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');
	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");
	include_once ("../include/gemius.h");

	for ($i=1;$i<$argc;$i++)
	{
		parse_str($argv[$i]);
	}

	
	if (!strlen($server)) usage();	

	if (!is_integer($server))
	{
		$query="SELECT id AS server FROM servers WHERE nazwa='$server'";
		parse_str(query2url($query));
	}
	if (!$server) usage();	


	$query="SELECT * FROM servers WHERE id=$server";
	$_server=ado_ObjectArray($adodb,$query);

	if (!is_Array($_server)) return;
	$SERVER=$_server[0];

	$SERVER_ID=$SERVER->id;
	if (!$ver) $ver=$SERVER->ver;
	if (!$lang) $lang=$SERVER->lang;

	$szablon=$SERVER->szablon;


	$depth+=0;
	$root+=0;
	$gemius+=0;

	
	for ($i=$ver;$i>0;$i--)
	{
		$sz="../szablony/$szablon/$i";
		if (file_exists($sz))
		{
			$SZABLON_PATH=$sz;
			$images_ver=$i;
			break;
		}
	}
	if (!strlen($SZABLON_PATH))
	{
		$sz="../szablony/$szablon";
		if (file_exists($sz)) $SZABLON_PATH=$sz;
		$images_ver=$ver;
	}

	$KAMELEON_MODE=1; // zeby nie robil tablicy linkow
	include_once ("../include/kameleon_href.h");
	if (file_exists("$SZABLON_PATH/const.h")) include ("$SZABLON_PATH/const.h");
	if (file_exists("$SZABLON_PATH/const.php")) include ("$SZABLON_PATH/const.php");


	echo "Starting: depth=$depth, root=$root, gemius=$gemius\n";


	$query="SELECT id AS page,ver AS v,hidden,tree, title, prev 
			FROM webpage 
			WHERE server=$SERVER_ID AND lang='$lang' 
			AND ver<=$ver
			ORDER BY id,ver DESC";

	$res=$adodb->Execute($query);

	echo "Total pages ".$res->RecordCount()."\n";

	for ($iii=0;$iii<$res->RecordCount();$iii++)
	{
		parse_str(ado_ExplodeName($res,$iii));

		if ($page_visited[$page]==1) continue;
		if ($hidden || $nositemap) continue;
		$page_visited[$page]=1;			

		if ($root && !strstr($tree,":$root:") && $page!=$root ) continue;

		if ($depth)
		{
			$d=substr_count($tree,":");
			if ($d) $d--;
			if ($d>=$depth) continue;
		}	

		$query="SELECT count(*) AS node FROM webpage 
			WHERE server=$SERVER_ID AND lang='$lang' 
			AND ver<=$ver AND prev=$page";
		parse_str(ado_query2url($query));
		

		if ($page==$root && $gemius) $parent=$gemius;
		else
		{
			$parent = $node ? get_node_id($server,$ver,$lang,$page) : get_node_id($server,$ver,$lang,$prev);

			if ($node) $tree.=$page;
			if (!$parent) create_nodes($server,$ver,$lang,$tree,$root,$gemius);

			$parent = $node ? get_node_id($server,$ver,$lang,$page) : get_node_id($server,$ver,$lang,$prev);
			if (!$parent) $parent=$gemius;
		}

		$pagekey="";$sid=0;
		$query="SELECT sid,pagekey
			FROM gemius WHERE server=$server
			AND ver=$ver AND lang='$lang' AND page_id=$page";
		
		parse_str(ado_query2url($query));

		if (strlen($pagekey) && !$overwrite) continue;

		$script=$ver;
		$title=titlelize($title);
		eval("\$label = \"$C_GEMIUS_LABEL\";");
		$label=urlencode($label);
	
		//continue;

		echo "Adding new script ($page [$v], $label, node=$node) as parent of $parent ... ";
		$url="AddNode.php?script=1&label=$label&parent=$parent";
		$response=gemius_geturl($url);	
		parse_str($response);	
		$gemius_id+=0;
		echo "$gemius_id\n";

		if (!$gemius_id)
		{
			echo "Result: $gemius_res\n";
			break;
		}

		
		$query=$sid?"UPDATE gemius SET node=$parent,pagekey='$gemius_key' WHERE sid=$sid":
			"INSERT INTO gemius (server,ver, lang, page_id, node,pagekey,id)
			 VALUES ($server,$ver, '$lang', $page, $parent,'$gemius_key',$gemius_id)";

		$adodb->Execute($query);
	}
	
	$adodb->Close();

	


	function usage()
	{
		global $argv;
			
		echo "Gemius synhronizator !\n\n\n";
		echo " Syntax: $argv[0] server={server_name|server_id} [gemius=node_number] [ver=n] [lang=x] [depth=n] [root=n] [overwrite=1] \n";
		echo "\n\tDescription:\n";
		echo "\tserver_name   - server name or id in Web Kameleon\n";
		echo "\tgemius        - parent node in gemius structure, default=0\n";
		echo "\tlang, ver     - language and version, if not submited - the defaults will be assumed\n";
		echo "\tdepth         - how deep you want walt to the explorer tree (0 means no limit)\n";
		echo "\troot          - root page number in Web Kameleon tree, default=0\n";
		echo "\toverwrite     - tels the tool to recreate script if exists\n";

		exit();
	}




	function get_node_id($server,$ver,$lang,$page)
	{
		//global $adodb; $adodb->debug=1;

		$query="SELECT node FROM gemius WHERE server=$server AND ver=$ver AND lang='$lang' AND page_id=$page";

		parse_str(ado_query2url($query));

		return 0+$node;
	}

	function create_nodes($server,$ver,$lang,$tree,$root,$gemius)
	{
		global $C_GEMIUS_LABEL;
		global $adodb;

		if (!strstr($tree,":$root:")) return;
		$tree=ereg_replace("^[0-9:]*:$root:","",$tree);
		$parent=$gemius;


		foreach (explode(":",$tree) AS $page)
		{
			if (!strlen($page)) continue;

			$node="";$sid=0;
			$query="SELECT node,sid
				FROM gemius WHERE server=$server
				AND ver=$ver AND lang='$lang' AND page_id=$page";

			parse_str(ado_query2url($query));

			if (strlen($node)) 
			{
				$parent=$node;
				continue;
			}

			$query="SELECT title,hidden,nositemap FROM webpage WHERE server=$server
				AND ver<=$ver AND lang='$lang' AND id=$page
				ORDER BY ver DESC LIMIT 1";
			parse_str(ado_query2url($query));

			if ($hidden) return;

			$title=titlelize($title);
			eval("\$label = \"$C_GEMIUS_LABEL\";");
			$label=urlencode($label);
			
			$gemius_id=0;
		
			echo "Adding new node ($page, $label) as parent of $parent ... ";

			$url="AddNode.php?script=0&label=$label&parent=$parent";
			$response=gemius_geturl($url);	
			parse_str($response);	
			$gemius_id+=0;

			echo "$gemius_id\n";

			if (!$gemius_id) 
			{
				echo "Result: $gemius_res\n";
				die();
			}

			$query=$sid?"UPDATE gemius SET node=$gemius_id WHERE sid=$sid":
				"INSERT INTO gemius (server,ver, lang, page_id, node)
				 VALUES ($server,$ver, '$lang', $page, $gemius_id)";

			$adodb->Execute($query);
		}
	}



?>