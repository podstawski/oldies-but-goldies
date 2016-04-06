<?
	push($adodb);
	$adodb=$kameleon_adodb;


	include_once('include/webver.h');

	function newsletter_obj2str($obj)
	{
		while(list($k,$v)=each($obj))
		{
			if (strlen($wynik)) $wynik.="&";
			$wynik.="$k=".urlencode(stripslashes($v));
		}

		return $wynik;
	}

	function newsletter_action($page,$page_id,$referer,$action,$parsestr,$deb=false)
	{
		global $SERVER_ID,$ver,$lang,$adodb;

		$adodb->debug=$deb;
		parse_str($parsestr);

		//echo $parsestr;


		include('include/action/'.$action.'.h');
		

		switch ($action)
		{
			case 'DodajStrone':
				return $page;
		}
		
	}

	function newsletter_todo(&$WEBTD,$notatka)
	{
		$todo=$WEBTD->xml;

		if (strstr($todo,$notatka)) return;
		if (strlen($todo)) $todo.="<br><br>";
		$todo.=$notatka;


		$todo=addslashes(stripslashes($todo));
		$sql="UPDATE webtd SET xml='$todo' WHERE sid=".$WEBTD->sid;

		global $adodb;
		$adodb->execute($sql);

		$WEBTD->xml=stripslashes($todo);
		
	}

	$adodb->begin();

	$webtd=newsletter_obj2str($WEBTD);

	$cos=$WEBTD->cos+0;

	if ($cos==0)
	{

		newsletter_action($page,$page,$page,'ZapiszTD',$webtd.'&menu_id=-1');
		$query="SELECT menu_id FROM webtd WHERE sid=$sid";
		parse_str(ado_query2url($query));

		newsletter_action($page,$page,$page,'DodajLink',$webtd.'&menu='.$menu_id);

		newsletter_action($page,$page,$page,'ZapiszMenu',$webtd."&menu_id=$menu_id:$INSTALL_NAME+admin");

		newsletter_action($page,$page,$page,'ZapiszLink',$webtd."&menu_id=$menu_id&pri=1&alt=Wysy³ka");
		$new_page_1=newsletter_action(-1,-1,$page,'DodajStrone',"ref_menu=$menu_id:1");
		newsletter_action($new_page_1,$new_page_1,$new_page_1,'DodajTD',"_level=".$WEBTD->level);
		$query="SELECT * FROM webtd WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver AND page_id=$new_page_1 ORDER BY pri DESC LIMIT 1";
		$new_td_1=ado_query2url($query);
		newsletter_action($new_page_1,$new_page_1,$new_page_1,'ZapiszTD',$new_td_1."&html=$INSTALL_NAME/mailmanage.php&menu_id=-1");
		$query="SELECT menu_id AS submenu 
				FROM webtd WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver AND page_id=$new_page_1 LIMIT 1";
		parse_str(ado_query2url($query));

		newsletter_action($new_page_1,$new_page_1,$new_page_1,'DodajLink',$new_td_1.'&menu='.$submenu);
		newsletter_action($new_page_1,$new_page_1,$new_page_1,'ZapiszMenu',$new_td_1."&menu_id=$submenu:$INSTALL_NAME+wysy³ka");
		newsletter_action($new_page_1,$new_page_1,$new_page_1,'ZapiszLink',$new_td_1."&menu_id=$submenu&pri=1&alt=Wzorzec");
		newsletter_action(-1,-1,$new_page_1,'DodajStrone',"ref_menu=$submenu:1");

		newsletter_action($page,$page,$page,'DodajLink',$webtd.'&menu='.$menu_id);
		newsletter_action($page,$page,$page,'ZapiszLink',$webtd."&menu_id=$menu_id&pri=2&alt=Subskrybenci");
		$new_page_2=newsletter_action(-1,-1,$page,'DodajStrone',"ref_menu=$menu_id:2");
		newsletter_action($new_page_2,$new_page_2,$new_page_2,'DodajTD',"_level=".$WEBTD->level);
		$query="SELECT * FROM webtd WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver AND page_id=$new_page_2 ORDER BY pri DESC LIMIT 1";
		$new_td_2=ado_query2url($query);
		newsletter_action($new_page_2,$new_page_2,$new_page_2,'ZapiszTD',$new_td_2."&html=@api/crm_users.h");


		global $SZABLON_PATH;

		newsletter_todo($WEBTD,"Proszê siê upewniæ czy zainstalowano modu³ <B>api</B>.");
		newsletter_todo($WEBTD,"Proszê sprawdziæ czy w pliku <b>$SZABLON_PATH/const.php</b> znajduje siê wpis <br>&nbsp;&nbsp;<b>\$C_MODULES=array('api');</b>");
		newsletter_todo($WEBTD,"Proszê sprawdziæ czy w pliku <b>$SZABLON_PATH/const.php</b> znajduje siê wpis <br>&nbsp;&nbsp;<b>\$CONST_MORE_TOKENS=array('newsletter_tokens'=>'newsletter_tokens.php');</b>");


		if (!file_exists("$SZABLON_PATH/newsletter_tokens.php"))
		{
			$tokens=implode('',file("$INCLUDE_PATH/$INSTALL_NAME/szablon/tokens.php"));
			$tokens=str_replace('INCLUDE_NEWSLETTER_PATH',$INSTALL_NAME,$tokens);
			$plik=@fopen("$SZABLON_PATH/newsletter_tokens.php",'w');
			@fwrite($plik,$tokens);
			@fclose($plik);
		}

		if (!file_exists("$SZABLON_PATH/themes/$INSTALL_NAME/body.html"))
		{
			$theme=implode('',file("$INCLUDE_PATH/$INSTALL_NAME/szablon/body_newsletter.html"));
			@mkdir("$SZABLON_PATH/themes/$INSTALL_NAME",0755);
			$plik=@fopen("$SZABLON_PATH/themes/$INSTALL_NAME/body.html",'w');
			@fwrite($plik,$theme);
			@fclose($plik);
		}

		newsletter_todo($WEBTD,"Proszê sprawdziæ czy w pliku <b>$SZABLON_PATH/const.php</b> znajduje siê wpis <b>
						<br>&nbsp;&nbsp;\$PAGE_TYPY[]=array(x,'x. NEWSLETTER','$INSTALL_NAME/body.html');</b>");

	}	

	if ($cos>0)
	{
		$query="UPDATE webtd SET xml='',html='' WHERE sid=$sid";
		ado_query2url($query);
		$WEBTD->xml='';
		unlink("$INCLUDE_PATH/$INSTALL_NAME/INSTALL.php");
		unlink("$INCLUDE_PATH/$INSTALL_NAME/INSTALL.INFO.php");
	}

	
	$test=0;
	$query="SELECT sid AS test FROM webtd WHERE sid=$sid";
	parse_str(ado_query2url($query));

	if ($test)
	{
		$cos++;
		$query="UPDATE webtd SET cos=$cos WHERE sid=$sid";
		ado_query2url($query);
	}





	//$adodb->rollback();
	$adodb->commit();
	

	$adodb=pop();

	echo $WEBTD->xml;
?>