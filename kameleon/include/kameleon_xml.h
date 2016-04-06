<?php
	include_once('include/swf.h');
	
	global $adodb,$SERVER_ID,$LINK_TYPY,$SZABLON_PATH;


	if ($KAMELEON_MODE)
	{
		if (!$WEBPAGE->menu_id)
		{
			global $AUTO_MENU_GENERATOR;
			include('include/menu_max.h');

			if (strlen($error)) die($error);
					
			$sql="UPDATE webpage SET menu_id=$menu_id WHERE sid=".$WEBPAGE->sid;
			$adodb->execute($sql);
			$WEBPAGE->menu_id=$menu_id;
		}



		$form='<form method="post" action="index.php?page='.$WEBPAGE->id.'">';

		$form.='<input type="text" class="k_input" title="'.label('Root xml tag').'" name="kameleon_xml[root]" value="'.$WEBPAGE->pagekey.'"/> ';

		$form.='<input type="submit" class="k_button" value="'.label('Save').'"/>';
		$form.="</form>";

		$src = ($editmode) ? 'menus.php?hidenavigation='.$WEBPAGE->type.'&menu='.$WEBPAGE->menu_id : 'index.php?dontdisplayanykameleonhtml=1&page='.$WEBPAGE->id;


		echo "<div style=\"width:96%; margin:2% \"><iframe style=\"width:100%; height:80%\" src=\"$src\" /></div>";
	}
	else
	{
		global $CHARSET;
		@header("Content-type: application/xml; charset=$CHARSET");
		

		
		$menu=kameleon_menus($WEBPAGE->menu_id);
		$_linkcount=count($menu);

		if ($_linkcount)
		{
			$WEBLINK=$menu[0];
			$link_template=kameleon_template($SZABLON_PATH,$LINK_TYPY,$WEBLINK->type+0);
			$link_start="%SECTION_LINK_BEGIN%";
			$link_end="%SECTION_LINK_END%";
			parser($link_start,$link_end,$link_template,$parser_tokens);
		}

	}