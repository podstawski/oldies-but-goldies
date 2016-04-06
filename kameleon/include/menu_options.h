<?
    if ($menu) $menu_id=$menu;


	$query="UPDATE weblink SET name='' WHERE server=$SERVER_ID
	    	AND ver=$ver AND lang='$lang' AND name IS NULL";
	$adodb->Execute($query);

    $query="SELECT menu_id AS m,name FROM weblink WHERE server=$SERVER_ID
	    	AND ver=$ver AND lang='$lang' AND menu_id IS NOT NULL
			GROUP BY menu_id,name
			ORDER BY name,menu_id "; 
    
    $menusy_result=$adodb->Execute($query);
    
    for ($i=0;$i<$menusy_result->RecordCount();$i++)
    {
    	parse_str(ado_ExplodeName($menusy_result,$i));
	$name=stripslashes($name);
    	
	if ($menu_id==$m) $menuselected="selected";
	else $menuselected="";
	if (!strlen($name)) $name=label("Menu no");

    	echo "<option class=k_select value='$m' $menuselected>$name [$m]</opiton>";
    }
?>
