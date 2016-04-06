<?
	global $WEBPAGE;
	$sql="SELECT fakro_header FROM webpage WHERE sid=".$WEBPAGE->sid;
	
	$kameleon_adodb->debug=0;
	
	if (!$kameleon_adodb->execute($sql)) $kameleon_adodb->execute("ALTER TABLE webpage ADD fakro_header Text; UPDATE webpage SET fakro_header=pagekey");

	global $ORYGINAL_WEBTD;

	if (is_object($ORYGINAL_WEBTD)) $WEBTD=$ORYGINAL_WEBTD;

	if ($WEBPAGE->pagekey)
	{
		$plain=addslashes(stripslashes($WEBTD->plain));
		$title=addslashes(stripslashes($WEBTD->title));
		$bgimg=$WEBTD->bgimg;
		$sid=$WEBTD->sid;
		$wpsid=$WEBPAGE->sid;

		if (strlen($title)) $title="<h1>$title</h1>";
		$html="$WEBPAGE->pagekey";
		
		$query="UPDATE webtd SET plain='',title='',bgimg='',staticinclude=1 WHERE sid=$sid;";

		$query.="UPDATE webpage SET ";
		$set=array();
		if (strlen($bgimg)) $set[]="background='$bgimg'";
		if (strlen($html)) $set[]="fakro_header='$html'";
		$query.=implode(',',$set);
		$query.=" WHERE sid=$wpsid";



		$kameleon_adodb->execute($query);

	}


	include("$INCLUDE_PATH/header.php");
?>
