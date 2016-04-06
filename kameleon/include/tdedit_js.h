<?php
header("Content-type: application/x-javascript"); 
//debug zawsze wy³aczony
$adodb->debug_ips = array();
//$adodb->puke($SZABLON_PATH);
//$adodb->puke($SERVER);

$adodb->dontSaveSession=true;

$none = 'function createjsDOMenu(){return;}';

$co=$hf_editmode?'hf':'body';
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







//$adodb->puke($levele);

if($C_SHOW_TD_TYPE) {
	$typy = $TD_TYPY;
}

//$adodb->puke($typy);

$uslugi = $APIS;

//$adodb->puke($uslugi);

if ( empty($uslugi) && empty($typy) && empty($leveleP) && empty($leveleHF))
{
    return $none;
}

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

?>
var uslugiInd = new Array(<?php echo getArray('uslugi', 'keys'); ?>);
var uslugiVal = new Array(<?php echo getArray('uslugi', 'val'); ?>);

var typyInd = new Array(<?php echo getArray('typy', 'keys'); ?>);
var typyVal = new Array(<?php echo getArray('typy', 'val'); ?>);

var levelePInd = new Array(<?php echo getArray('leveleP', 'keys'); ?>);
var levelePVal = new Array(<?php echo getArray('leveleP', 'val'); ?>);

var leveleHFInd = new Array(<?php echo getArray('leveleHF', 'keys'); ?>);
var leveleHFVal = new Array(<?php echo getArray('leveleHF', 'val'); ?>);

//alert(uslugiInd);
//alert(uslugiVal);

function getMaxLength( array )
{
	maxLength = '';
	
	arrayL = array.length;
	
	for (k = 0; k < arrayL; k++)
	{
		if (maxLength < array[k].length ) maxLength = array[k].length;
	}
	return maxLength;
}

function checkDw( string )
{
	re = /\:/i;
	dw = string.replace(re, "_");
	return dw;
}

function int_test ( el )
{
	Menu = el.parent.menuObj;
	alert(Menu.name);
	//alert(el.parent.menuObj.name);
}

function int_skopiuj ( param1, param2,param3 )
{
	hideAllMenus();

	skopiuj(param1, param2,unescape(param3));
}

function int_skopiuj_identyfikator(s)
{
	hideAllMenus();
	skopiuj(s,'mask');
	//alert('<?echo label('The module identifier was copied')?>');
}



function int_zmiana( param1, param2)
{
	hideAllMenus();
	zmiana(param1, param2);
}

function int_visibility( page, page_id, pri, action )
{
	hideAllMenus();
	changeVisibility( page, page_id, pri, action );
}

function int_AddMenu( menu_id, page, page_id, sid, server )
{
	hideAllMenus();
	addMenu(menu_id, page, page_id, sid, server);
}

function int_Move (direction, page, page_id, pri) 
{
	//alert('direction='+direction+' page='+page+' page_id='+page_id+' pri='+pri);
	hideAllMenus();
	elementMove(direction, page, page_id, pri);
}

function showMenu(r_menu, page_id, sid, server, type, level, api, pri, page, hashencoded, tab_name, hidden, valid, script_name, menu_id, title, event) 
{
  //alert('r_menu='+r_menu+' page_id=' + page_id + ', sid=' + sid + ', server=' + server + ', type=' + type + ', level=' + level + ', api=' + api + ' pri='+pri+' page='+page+' hashencoded='+hashencoded+' tab_name='+tab_name+' script_name='+script_name+' menu_id='+menu_id);

  hideAllMenus();
 
  if (r_menu == 'TD')
  { 
  	createjsDOMenu(page_id, sid, server, type, level, api, pri, page, hashencoded, tab_name, hidden, valid, script_name, menu_id,title);
  }
  
  if (r_menu == 'HF')
  {
    createjsDOMenuObszar (page_id, sid, server, type, level, api, pri, page, hashencoded, tab_name, hidden, valid, script_name);	
  }
 
  event.cancelBubble=true;
  rightClickHandler(event);
  return false;
  
}


function createjsDOMenu( page_id, sid, server, type, level, api, pri, page, hashencoded, tab_name, hidden, valid, script_name, menu_id ,title) 
{
  
	saUslugi = uslugiInd.length;
	
	if ( uslugiInd[uslugiInd.length-1] != "NULL")
	{
		//Dodatkowo separator + suniêcie us³ugi
		uslugiInd[uslugiInd.length] = "-"; uslugiVal[uslugiVal.length] = "-";
 		uslugiInd[uslugiInd.length] = "NULL"; uslugiVal[uslugiVal.length] = "<?php echo label("Remove api");?>";
	}
	
	var levelInd = ( page_id < 0 ) ? leveleHFInd : levelePInd;
	var levelVal = ( page_id < 0 ) ? leveleHFVal : levelePVal;

	var labelMoveUp   = ( page_id < 0 ) ? "<?php echo label("Move left");?>" : "<?php echo label("Move up");?>";
	var labelMoveDown = ( page_id < 0 ) ? "<?php echo label("Move right");?>" : "<?php echo label("Move down");?>";
	
	var isFirst = true;
	
	var Mvisible = ( hidden == 1 ) ? "<?php echo label("Show module");?>" : "<?php echo label("Hide module");?>";

	usL = uslugiInd.length;
	typL = typyInd.length;
	levelL = levelInd.length;
	
	tostr_page_id = new String (page_id);
	str_page_id = tostr_page_id.replace('-', '_');
	//str_api = checkDw ( api );
	
	//alert(str_api);
	//alert(str_page_id);

	if ( usL > 0 && saUslugi > 0 )
	{
		//alert("menu_uslugiIn"+str_page_id+" = new jsDOMenu(190);");
		eval("menu_uslugiIn"+str_page_id+" = new jsDOMenu(190);");
		
		for (i = 0; i < usL; i++) 
		{                        
			apinode = '';
			if (api) apinode = "item"+checkDw(uslugiInd[i]);
			eval("menu_uslugiIn"+str_page_id+".addMenuItem( new menuItem(\""+uslugiVal[i]+"\", \""+apinode+"\", \"code:doMenuChange("+page_id+", "+sid+", "+server+", 'usluga', '"+uslugiInd[i]+"');\") );");
		}
	}
	
	if ( typL > 0 )
	{
		
		eval("menu_typyIn"+str_page_id+" = new jsDOMenu(180);");
		
		for (i = 0; i < typL; i++) 
		{                        
			//alert(i);
			typenode = '';
			if (type == '')
			{
				if (isFirst)
				{ 
					typenode = "item";
					isFirst = false;
				}
				else
				{
					typenode = "item"+typyInd[i];
				}
			}
			else
			{
				typenode = "item"+typyInd[i];
			}
			//alert(typenode);
			eval("menu_typyIn"+str_page_id+".addMenuItem( new menuItem(\""+typyVal[i]+"\", \""+typenode+"\", \"code:doMenuChange("+page_id+", "+sid+", "+server+", 'typ', '"+typyInd[i]+"');\") );");
		}
	}
	
	if ( levelL > 0 )
	{
		
		eval("menu_levelIn"+str_page_id+" = new jsDOMenu(210);");
		
		for (i = 0; i < levelL; i++) 
		{                        
			//alert(i);
			levelenode = '';
			if (level) levelenode = "item"+levelInd[i];
			eval("menu_levelIn"+str_page_id+".addMenuItem( new menuItem(\""+levelVal[i]+"\", \""+levelenode+"\", \"code:doMenuChange("+page_id+", "+sid+", "+server+", 'level', '"+levelInd[i]+"');\") );");
		}
	}

	
  if ( usL > 0 || typL > 0 || level > 0 )
  {
		eval("menu_All"+str_page_id+" = new jsDOMenu(180);");
  }
  
  if ( usL > 0 && saUslugi > 0)
  {
  	eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Include api") ?>\", \"uslugi\", \"code:void(0);\") );");
  }
  
  if ( typL > 0 )
  {
  	eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Type"); ?>\", \"typy\", \"code:void(0);\") );");
  }
  if ( level > 0 )
  {
  	eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Level"); ?>\", \"levele\", \"code:void(0);\") );");
  }

  //menu

<? if ($C_SHOW_TD_MENU ) { ?>
	
	if (menu_id == '') 
	{
		menu_id = '-1';
		addMenuLabel = '<?php echo label("Insert new menu"); ?>';
	}
	else
	{
		menu_id = '0';
		addMenuLabel = '<?php echo label("Turn off menu"); ?>';
	}
	eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\""+addMenuLabel+"\", \"menuNav\", \"code:int_AddMenu('"+menu_id+"', '"+page+"', '"+page_id+"', '"+sid+"', '"+server+"');\") );");

<? } ?>
  
	// end menu

  //Kopiuj, przenies w gorê...
  eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"-\", \"\", \"-\") );");
  eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Copy"); ?>\", \"copy\", \"code:int_skopiuj('"+sid+"','td','"+escape(title)+"');\") ); ");
  eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Copy module identifier"); ?>\", \"copy_id\", \"code:int_skopiuj_identyfikator('"+sid+"');\") ); ");

//separator
eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"-\", \"\", \"-\") );");
  
eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\""+labelMoveUp+"\", \"moveUp\", \"code:int_Move('up','"+page+"', '"+page_id+"', '"+pri+"');\") ); ");
  
eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\""+labelMoveDown+"\", \"moveDown\", \"code:int_Move('down','"+page+"','"+page_id+"','"+pri+"');\") ); ");

eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Edit");?>\", \"wlasciwosci\", \"code:tdedit('"+page_id+"','"+pri+"','"+hashencoded+"','"+tab_name+"');\") ); ");

//separator
eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"-\", \"\", \"-\") );");

eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\""+Mvisible+"\", \"visibility\", \"code:int_visibility("+page+","+page_id+","+pri+",'visibility');\") ); ");

eval("menu_All"+str_page_id+".addMenuItem( new menuItem(\"<?php echo label("Delete");?>\", \"delete\", \"code:int_zmiana('"+page_id+":"+pri+"','UsunTD');\") ); ");

  
  eval("menu_All"+str_page_id+".items.copy.showIcon(\"jsdomenucopy\");");
  eval("menu_All"+str_page_id+".items.copy_id.showIcon(\"icon1\");");

  
  if (page_id < 0)
  {
	eval("menu_All"+str_page_id+".items.moveUp.showIcon(\"jsdomenuleft\");");
  }
  else
  {
	eval("menu_All"+str_page_id+".items.moveUp.showIcon(\"jsdomenuup\");");
  }

  if (page_id < 0)
  	eval("menu_All"+str_page_id+".items.moveDown.showIcon(\"jsdomenuright\");");
  else
  	eval("menu_All"+str_page_id+".items.moveDown.showIcon(\"jsdomenudown\");");

  
  if ( usL > 0 && saUslugi > 0 )
  {
	eval("menu_All"+str_page_id+".items.uslugi.setSubMenu(menu_uslugiIn"+str_page_id+");");
	if (api) eval("menu_uslugiIn"+str_page_id+".items.item"+checkDw(api)+".showIcon(\"icon4\");");
  }
  if ( typL > 0 )
  {
  	eval("menu_All"+str_page_id+".items.typy.setSubMenu(menu_typyIn"+str_page_id+");");
	if (type || type == '') eval("menu_typyIn"+str_page_id+".items.item"+type+".showIcon(\"icon4\");");
  }
  if ( levelL > 0 )
  {
  	eval("menu_All"+str_page_id+".items.levele.setSubMenu(menu_levelIn"+str_page_id+");");
	if (level) eval("menu_levelIn"+str_page_id+".items.item"+level+".showIcon(\"icon4\");");
  }

  
  
  eval("menu_All"+str_page_id+".setNoneExceptFilter(new Array(\"TD.td_"+sid+"\"));");

	eval("setPopUpMenu(menu_All"+str_page_id+");");
  activatePopUpMenuBy(1, 2);



}


//
//
//
//
//

function createjsDOMenuObszar( page_id, sid, server, type, level, api, pri, page, hashencoded, tab_name, hidden, valid, script_name ) 
{
  if ( uslugiInd[uslugiInd.length-1] == "NULL")
  {
  	uslugiInd.length = uslugiInd.length-2;
  	uslugiVal.length = uslugiVal.length-2;
  }

  var levelInd = ( page_id < 0 ) ? leveleHFInd : levelePInd;
  var levelVal = ( page_id < 0 ) ? leveleHFVal : levelePVal;
  var isFirst = true;
  
  var Atypenode = new Array();
  var Ausluginode = new Array();

  var levelenode = '';
  
  usL = uslugiInd.length;
  typL = typyInd.length;
  levelL = levelInd.length;
 
  eval("menu_All = new jsDOMenu(180, \"\", \"\", \"\", \"\", \"Mall\");");
  eval("menu_All.addMenuItem( new menuItem(\"<?php echo label("Include api"); ?>\", \"uslugi\", \"code:int_test(this.parent.menuObj.id);\") );");
  eval("menu_All.addMenuItem( new menuItem(\"<?php echo label("Insert module"); ?>\", \"dodajmodul\", \"code:int_test(this.id);\") );");
  
  eval("menu_All.setNoneExceptFilter(new Array(\"TD.HF"+page_id+"\"));");
  
  
  	
	if ( usL > 0 )
	{
		eval("menu_uslugi = new jsDOMenu(190, \"\", \"\", \"\", \"\", \"Musulugi\");");
		
		for (i = 0; i < usL; i++) 
		{                        
			apinode = "api_"+checkDw(uslugiInd[i]);
			Ausluginode[Ausluginode.length] = apinode;
			eval("menu_uslugi.addMenuItem( new menuItem(\""+uslugiVal[i]+"\", \""+apinode+"\", \"code:void(0);\") );");
		}
		//alert(Ausluginode);
	}
  
  if ( usL > 0 )
  {
	eval("menu_All.items.uslugi.setSubMenu(menu_uslugi);");
  }
  
  
  	if ( typL > 0 )
	{
		
		eval("menu_typy = new jsDOMenu(180, \"\", \"\", \"\", \"\", \"Mtyp\");");
		
		for (i = 0; i < typL; i++) 
		{                        
			//alert(i);
			typenode = "typ_"+typyInd[i];
			Atypenode[Atypenode.length] = typenode;
			eval("menu_typy.addMenuItem( new menuItem(\""+typyVal[i]+"\", \""+typenode+"\", \"code:void(0);\") );");
		}
		//alert(Atypenode);
	}
	
  if ( usL > 0 )
  {
	eval("menu_All.items.dodajmodul.setSubMenu(menu_typy);");
  }

  if ( levelL > 0 )
	{
		
		eval("menu_levelUsl = new jsDOMenu(210, \"\", \"\", \"\", \"\", \"Musulga\");");
		eval("menu_levelMod = new jsDOMenu(210, \"\", \"\", \"\", \"\", \"Mmodul\");");
		
		for (i = 0; i < levelL; i++) 
		{                        
			//alert(i);
			levelenode = "level_"+levelInd[i];
			eval("menu_levelUsl.addMenuItem( new menuItem(\""+levelVal[i]+"\", \""+levelenode+"\", \"code:int_test(this);\") );");
		}
		
		for (i = 0; i < levelL; i++) 
		{                        
			//alert(i);
			levelenode = "level_"+levelInd[i];
			eval("menu_levelMod.addMenuItem( new menuItem(\""+levelVal[i]+"\", \""+levelenode+"\", \"code:int_test(this);\") );");
		}
	}
  
  
  for (key in Ausluginode)
  {
    eval("menu_uslugi.items."+Ausluginode[key]+".setSubMenu(menu_levelUsl);");
  }
  
  for (key in Atypenode)
  {
    eval("menu_typy.items."+Atypenode[key]+".setSubMenu(menu_levelMod);");
  }
  
  eval("setPopUpMenu(menu_All);");
  activatePopUpMenuBy(1, 2);
 
}
