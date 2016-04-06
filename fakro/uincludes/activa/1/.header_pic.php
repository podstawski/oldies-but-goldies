<?
global $ORYGINAL_WEBTD;

if(is_object($ORYGINAL_WEBTD)) $WEBTD=$ORYGINAL_WEBTD;

	if(strlen($WEBTD->title) || strlen($WEBTD->plain) || strlen($WEBTD->bgimg) ||  $WEBTD->swfstyle)
	{
		$plain=addslashes(stripslashes($WEBTD->plain));
		$title=addslashes(stripslashes($WEBTD->title));
		$bgimg=$WEBTD->bgimg;
		$sid=$WEBTD->sid;
		global $WEBPAGE;
		$wpsid=$WEBPAGE->sid;
		
		if(strlen($title)) $title="<h1>$title</h1>";
		$html="$title$plain";
		
		$query="UPDATE webtd SET plain='',title='',bgimg='',staticinclude=1 WHERE sid=$sid;";
		
		$query.="UPDATE webpage SET ";
		$set=array();
		if(strlen($bgimg)) $set[]="background='$bgimg'";
		if(strlen($html)) $set[]="pagekey='$html'";
		$query.=implode(',',$set);
		$query.=" WHERE sid=$wpsid";
		
		$kameleon_adodb->execute($query);
	}

include("$INCLUDE_PATH/header_pic.php");
?>
