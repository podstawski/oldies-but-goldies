<?
        global $WEBPAGE;
        $sql="SELECT fakro_header FROM webpage WHERE sid=".$WEBPAGE->sid;
        if (!$kameleon_adodb->execute($sql))
          $kameleon_adodb->execute("ALTER TABLE webpage ADD fakro_header Text; UPDATE webpage SET fakro_header=pagekey");


	/*
	if (strlen($WEBTD->plain) || strlen($WEBTD->bgimg) )
	{
		$plain=addslashes(stripslashes($WEBTD->plain));
		$title=addslashes(stripslashes($WEBTD->title));
		$bgimg=$WEBTD->bgimg;
		$sid=$WEBTD->sid;
		global $WEBPAGE;
		$wpsid=$WEBPAGE->sid;

		if (strlen($title)) $title="<h1>$title</h1>";
		$html="$title$plain";
		
		$query="UPDATE webtd SET plain='',title='',bgimg='',staticinclude=1 WHERE sid=$sid;
				UPDATE webpage SET background='$bgimg',fakro_header='$html' WHERE sid=$wpsid";

		$kameleon_adodb->execute($query);

	}
	*/

	if (!$WEBTD->staticinclude) $kameleon_adodb->execute("UPDATE webtd SET staticinclude=1 WHERE sid=$sid;");
	include("$INCLUDE_PATH/header.php");
?>
