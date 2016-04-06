<?
	parse_str($costxt);

	if (!strlen($filename)) return;

	$WM->template_context=$filename;

	$query="SELECT sk_nazwa FROM sklep WHERE sk_id=$SKLEP_ID";
	parse_str($WM->ado_query2url($query,$true));
	$n=ereg_replace("[^a-z\.]","",$sk_nazwa);

	$cache_dir="/var/tmp/wm.$n.$SERVER_ID";
	if (!file_exists($cache_dir)) mkdir ($cache_dir,0700);
	$a=($AUTH[id]>0)?1:0;
	eval("\$tmp=\"$cachev\";");
	$cache_name="$cache_dir/tmpl$tmp${a}_${sid}_".$LIST[id]."-".($LIST[start]+0).$LIST[sort_f].$LIST[sort_d];

	if ($cache && file_exists($cache_name) && !$KAMELEON_MODE)
	{
		
		$t=filemtime($cache_name);
		$st=filemtime(basename($SCRIPT_NAME));

		if ($t+$cache>$NOW && $t>$st)
		{
			echo "<!-- $filename from cache: $cache_name -->";
			readfile($cache_name);
			$WM->template_context="";
			return;
		}
	}

	$templ_dir="$SKLEP_INCLUDE_PATH/templates/$filename";


	$foundfilename=$filename;

	if (file_exists("$SKLEP_INCLUDE_PATH/templates.html/$lang/$foundfilename.main")) $foundfilename="$lang/$foundfilename";

	if (!file_exists("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.main")) return;


	ob_start();


	$INCLUDE_INC_PHP=1;
	if (file_exists("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.inc")) 
	{
		include("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.inc");
	}
	if (file_exists("$templ_dir/inc.php") && $INCLUDE_INC_PHP) 
	{
		include("$templ_dir/inc.php");
	}


	$INC=ob_get_contents();
	ob_end_clean();

	
	ob_start();
	$result="";
	$error="";
	eval("\$$filename=\"\";");
	$template_list="";
	
	if (file_exists("$templ_dir/main.php")) include("$templ_dir/main.php");
	if (strlen($error)) echo $error;
	if (file_exists("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.main.inc")) 
	{
		include("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.main.inc");
	}
		
	$template_loop=1;
	$parity_idx=0;
	while($template_loop && !strlen($error))
	{
		$list_continue=0;
		$parity_row = ( ($parity_idx++)%2 ) ? 1:0;
		if (file_exists("$templ_dir/list.php")) include("$templ_dir/list.php");
		else break;
		if ($list_continue) continue;
		if (!$template_loop) break;
		$HTML=fcontent("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.list");
		if (!strlen($HTML)) break;
		$HTML=phpfun_html(addslashes($HTML));
		eval("\$HTML=\"$HTML\";");
		$HTML=stripslashes($HTML);
		$template_list.=$HTML;
	}

	eval("\$$filename = \$template_list ;");

	

	$debug=ob_get_contents();
	ob_end_clean();

	if ($KAMELEON_MODE)
	{
		$WM->debug("<div style='border: 1px solid'><b>Debug (tylko w kameleonie):</b><br>$debug</div>");
	}


	if (strlen($error))
	{
		$HTML=fcontent("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.err");
		$HTML=phpfun_html(addslashes($HTML));
		eval("\$HTML=\"$HTML\";");
		$HTML=stripslashes($HTML);
		echo $INC.$HTML;
	}
	else
	{
		$HTML=fcontent("$SKLEP_INCLUDE_PATH/templates.html/$foundfilename.main");
		$HTML=phpfun_html(addslashes($HTML));
		eval("\$HTML=\"$HTML\";");
		$HTML=stripslashes($HTML);
		echo $INC.$HTML;
	}

	if ($cache && !$KAMELEON_MODE && !strlen($error))
	{
		$plik=fopen($cache_name,"w");
		fwrite($plik,$INC.$HTML);
		fclose($plik);
	}

	$error="";
	$WM->template_context="";

?>