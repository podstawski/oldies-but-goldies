<?
//echo "start";

	$display_menu="";
	$webtd=kameleon_td($page,$ver,$lang,$level);
	
	if (($copy_page) && ($copy_page!=$page) && ($parser_alias))
	{
		$webtd2=kameleon_td($copy_page,$ver,$lang,$level);
		if (is_array($webtd))
			$webtd=array_merge($webtd,$webtd2);
		else
			$webtd=$webtd2;
	}
	
	if (!is_array($webtd) ) return;
	for ($td_w_szpalcie=0;$td_w_szpalcie<count($webtd);$td_w_szpalcie++)
	{
		$tdcount=$td_w_szpalcie;
		$WEBTD=$webtd[$td_w_szpalcie];
		include ("include/td.php");
	}

