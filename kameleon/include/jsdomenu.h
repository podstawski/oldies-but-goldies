<?
  $adodb->debug_ips = array();
  //$adodb->dontSaveSession=true; // CARTMAN - Wywalone bo niezapisywa³o sesji dla switcheditmode i takich tam...
  
  $co = $hf_editmode ? 'hf' : 'body' ; // edycja - nag³owek / body
  
  $levels=$adodb->getFromSession('page_levels');
  if (is_array($levels[$co][$pagetype]))
  {
  	$lvs=$levels[$co][$pagetype];
  	$tab=$hf_editmode?$TD_POZIOMY_HF:$TD_POZIOMY;
  	for ($i=0;$i< count($tab);$i++ )
  	{
  		$type=$tab[$i][0];
  		if (!in_array($type,$lvs)) unset($tab[$i]);
  	}
  	ksort($tab);
  	if ($hf_editmode) $TD_POZIOMY_HF=$tab;
  	else $TD_POZIOMY=$tab;
  }
  
  if($C_SHOW_TD_LEVEL) 
  {
  	$leveleP = $TD_POZIOMY;
  	$leveleHF = $TD_POZIOMY_HF;
  }
  
  if($C_SHOW_TD_TYPE) 
  {
  	$typy = $TD_TYPY;
  }
  
  $uslugi = $APIS;
  
  function getArray ( $array, $param )
  {
  	global $$array;
  	
  	if ( is_array( $$array ) ) 
  	{
  		foreach ( $$array as $val )
  		{
  			if ($val[0] !== '')
  			{
  				$u['keys'] .= ",\"" . $val[0] . "\"";
  				$u['val'] .= ",\"" . $val[1] . "\"";
  			}
  		}
  		$u['keys'] = substr($u['keys'],1);
  		$u['val'] = substr($u['val'],1);
  	}
  
  	return $u[$param];
  }
  
  echo "<div><pre>";
  echo "</pre></div>";
  echo "<ul id=\"km_jsdomenu\" class=\"km_jqcontextmenu\">";
  
  // PODSTAWOWE
  echo "
  <li id=\"km_contenxtmenu_menu_up\"><a href=\"#\">".label("Move up")."</a></li>
  <li id=\"km_contenxtmenu_menu_down\"><a href=\"#\">".label("Move down")."</a></li>
  <li id=\"km_contenxtmenu_menu_copy\"><a href=\"#\">".label("Copy")."</a></li>
  <li id=\"km_contenxtmenu_menu_delete\"><a href=\"#\">".label("Delete")."</a></li>
  <li id=\"km_contenxtmenu_menu_infoswf\"><a href=\"#\">".label("Info")."</a></li>
  <li id=\"km_contenxtmenu_menu_next\"><a href=\"#\">".label("Next page")."</a></li>
  <li id=\"km_contenxtmenu_menu_more\"><a href=\"#\">".label("More")."</a></li>
  <li id=\"km_contenxtmenu_menu_visibility\"><a href=\"#\">&nbsp;</a></li>
  <li class=\"km_sep\"></li>
  ";
  
  // API 
  echo "<li id=\"km_contenxtmenu_api\"><a class=\"km_contenxtmenu_api\" href=\"#\">".label("Include api")."</a><ul>";
  for ($i=1; $i<sizeof($APIS); $i++)
  {
    echo "<li><a href=\"".$APIS[$i][0]."\" rel=\"".$APIS[$i][0]."\">".$APIS[$i][1]."</a></li>";
  }
    echo "<li class=\"km_sep\"></li>";
    echo "<li><a href=\"NULL\" id=\"km_contextmenu_apiremove\">".label("Remove api")."</a></li>";
  
  echo "</ul></li>
  <li id=\"km_contenxtmenu_typ\"><a class=\"km_contenxtmenu_typ\" href=\"#\">".label("Type")."</a><ul>";
  
  for ($i=0; $i<sizeof($TD_TYPY); $i++)
  {
    echo "<li><a href=\"".$TD_TYPY[$i][0]."\" rel=\"".$TD_TYPY[$i][0]."\">".$TD_TYPY[$i][1]."</a></li>";
  }
  
  echo "</ul></li>
  <li id=\"km_contenxtmenu_lvl_hf\"><a class=\"km_contenxtmenu_lvl\" href=\"#\">".label("Level")."</a><ul>";
  
  for ($i=0; $i<sizeof($TD_POZIOMY_HF); $i++)
  {
    echo "<li><a href=\"".$TD_POZIOMY_HF[$i][0]."\" rel=\"".$TD_POZIOMY_HF[$i][0]."\">".$TD_POZIOMY_HF[$i][1]."</a></li>";
  }
  
  echo "</ul></li>
  <li id=\"km_contenxtmenu_lvl_td\"><a class=\"km_contenxtmenu_lvl\" href=\"#\">".label("Level")."</a><ul>";
  
  for ($i=0; $i<sizeof($TD_POZIOMY); $i++)
  {
    echo "<li><a href=\"".$TD_POZIOMY[$i][0]."\" rel=\"".$TD_POZIOMY[$i][0]."\">".$TD_POZIOMY[$i][1]."</a></li>";
  }
  
  echo "</ul></li>
  <li class=\"km_sep\"></li>
  <li id=\"km_contenxtmenu_menu_menu\"><a href=\"#\">".label("Menu")."</a></li>
  <li id=\"km_contenxtmenu_menu_insert\"><a href=\"#\">".label("Insert new menu")."</a></li>
  <li id=\"km_contenxtmenu_menu_off\"><a href=\"#\">".label("Turn off menu")."</a></li>
  <li id=\"km_contenxtmenu_menu_mask\"><a href=\"#\">".label("Copy module identifier")."</a></li>
  ";
  /*
  <li><a href="#">Item 2a</a></li>
  <li><a href="#">Item Folder 3a</a>
  	<ul>
  	<li><a href="#">Sub Item 3.1a</a></li>
  	<li><a href="#">Sub Item 3.2a</a></li>
  	<li><a href="#">Sub Item 3.3a</a></li>
  	<li><a href="#">Sub Item 3.4a</a></li>
  	</ul>
  </li>
  */
  echo "</ul>";
  //print_r($APIS);
?>