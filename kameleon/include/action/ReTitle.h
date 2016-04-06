<?
	$action="";
	
	if (strlen($title)) $title=addslashes(stripslashes($title));
	if (strlen($alt)) $alt=addslashes(stripslashes($alt));

	$STD_WHERE="lang='$lang' AND server=$SERVER_ID AND ver=$ver";


	if (!$kameleon->checkRight('write','page',$page_id) && !$kameleon->checkRight('write','menu',$menu_id))
	{
		$error=$norights;
		return;
	}


	switch ($table)
	{
		case "page": 
			$set="title='$title'";
			$SPECIAL_WHERE = "AND id=$page";
			break;

		case "td": 
			$set="title='$title'";
			$SPECIAL_WHERE = "AND page_id=$page_id AND pri=$pri";
			break;

		case "link":
			$SPECIAL_WHERE = "AND menu_id=$menu_id AND pri=$pri";
			$set="alt='$alt'";
			break;

		default:
			$error=label("dbTable not supported");
	
	}

	$query="UPDATE web$table SET $set WHERE $STD_WHERE $SPECIAL_WHERE";

	//echo nl2br($query);return;
	if (!strlen($error)) if ($adodb->Execute($query)) 
	{
		logquery($query) ;
	}


?>