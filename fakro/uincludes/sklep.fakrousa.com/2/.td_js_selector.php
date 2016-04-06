<?
	if (!function_exists(sel_array2costxt))
	{
		function sel_array2costxt($TD_JS_SELECTOR,$sid,&$kameleon_adodb)
		{
			while( list($k,$v) = each($TD_JS_SELECTOR) )
			{
				if (strlen($costxt)) $costxt.='&';
				if (!is_array($v)) $costxt.="sel[$k]=".urlencode($v);
				else 
				{
					while(list($ki,$vi)=each($v) ) 
					{
						if (strlen($costxt)) $costxt.='&';
						$costxt.="sel[$k][$ki]=".urlencode($vi);
					}
				}
			}
			$sql="UPDATE webtd SET costxt='$costxt' WHERE sid=$sid";
			$kameleon_adodb->execute($sql);

		}
	}

	include("$INCLUDE_PATH/.td_js_selector_walker.php");

	$adodb=$kameleon_adodb;

	if (!$size) $size=300;

	global $TD_JS_SELECTOR,$TD_JS_SELECTOR_SID;
	parse_str($costxt);
	echo '<a name="TD_JS_SELECTOR"></a>';

	if (is_array($TD_JS_SELECTOR) && $TD_JS_SELECTOR_SID==$sid)
	{
		$sel=$TD_JS_SELECTOR;
		
		//echo '<pre>';print_r($sel);echo '<pre>';
		sel_array2costxt($TD_JS_SELECTOR,$WEBTD->sid,$kameleon_adodb);
	}



	$i=0;

	$ch_checked=$sel[$i][checked]==="$i"?'checked':'';
	$ra_checked=$sel['default']==="$i"?'checked':'';

	echo '<form method="post" action="'.$self.'#TD_JS_SELECTOR"><table width="100%">';
	echo "<input type='hidden' name='TD_JS_SELECTOR_SID' value='$sid'>";
	echo '<tr>';
	echo '<td><input style="width: '.$size.'px" type="text" value="'.$sel[$i][text].'" name="TD_JS_SELECTOR['.$i.'][text]">';
	echo '<td><input type="checkbox" '.$ch_checked.' name="TD_JS_SELECTOR['.$i.'][checked]" value="0">';
	echo '<td><input type="radio" '.$ra_checked.' name="TD_JS_SELECTOR[default]" value="'.$i.'">';
	echo '</tr>';

	foreach( kameleon_td($WEBTD->page_id,$WEBTD->ver,$WEBTD->lang,$WEBTD->level,1) AS $_WEBTD)
	{
		if ($_WEBTD->sid == $WEBTD->sid ) continue;
		$i++;

		if (!strlen($sel[$i][text])) $sel[$i][text]=ereg_replace('<[^>]*>','',$_WEBTD->title);


		$ch_checked=$sel[$i][checked]>0?'checked':'';
		$ra_checked=$sel['default']==$i?'checked':'';

		//$inp_style=($sel[$i][checked]==$_WEBTD->sid)?'':'text-decoration:line-through;';

		if ($sel[$i][checked]!=$_WEBTD->sid) 
		{
			$sel[$i][checked]=$_WEBTD->sid;
			$need_overwrite=1;

		}


		echo '<tr>';
		echo '<td><input style="'.$inp_style.'width: '.$size.'px" type="text" value="'.$sel[$i][text].'" name="TD_JS_SELECTOR['.$i.'][text]">';
		echo '<td><input type="checkbox" '.$ch_checked.' name="TD_JS_SELECTOR['.$i.'][checked]" value="'.$_WEBTD->sid.'">';
		echo '<td><input type="radio" '.$ra_checked.' name="TD_JS_SELECTOR[default]" value="'.$i.'">';

		echo '</tr>';


	}

	echo '</table><input type="submit" value="Zapisz"></form>';
	if ($need_overwrite) sel_array2costxt($sel,$WEBTD->sid,$kameleon_adodb);
?>
