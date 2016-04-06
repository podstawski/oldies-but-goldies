<?
	if ($ver==10) return;

	$sel=array();
	parse_str($costxt);

	global $_REQUEST;

	if (strlen($_REQUEST["td_js_selector_$page"])) 
	{
		$sel['default']=$_REQUEST["td_js_selector_$page"];
	}

	$script='';
	$options='';
	
	
	while( list($i,$v) = each($sel) )
	{
		if (!is_array($v)) continue;
		if (!strlen($v[checked])) continue;

		$ch=($i==$sel['default'])?'selected':'';

		$options.='<option value="'.$v[checked].'" '.$ch.'>'.$v[text];

		if ($v[checked]>0) $init_id="s".$v[checked];

		if (strlen($ch) && $v[checked]) $script.="		PREVIOUS_SELECTED=".$v[checked].";\n";
		if (!strlen($ch) && $v[checked]) $script.="		document.getElementById('s".$v[checked]."').style.display='none';\n";
		$script.="		SID_ARRAY[".$v[checked]."]=$i;\n";
	}

?>
<select onchange="td_js_selector_select(this.value)" class="td_js_selector">
<?echo $options?>
</select>


<script>
	var PREVIOUS_SELECTED=0;
	var SID_ARRAY = new Array;
	var LAST_SELECTED;

	function td_js_selector_init()
	{
		id='<?echo $init_id?>';
		obj=null;
		obj=document.getElementById(id);
		if (obj==null)
		{
			setTimeout(td_js_selector_init,10);
			return;
		}
<?echo $script?>
	}

	function td_js_selector_cookie()
	{
		document.cookie='td_js_selector_<?echo $page?>='+LAST_SELECTED;
	}

	function td_js_selector_select(v)
	{
		id='s'+v;
		document.getElementById(id).style.display='';

		if (PREVIOUS_SELECTED>0)
		{
			id='s'+PREVIOUS_SELECTED;
			document.getElementById(id).style.display='none';
			
		}
		PREVIOUS_SELECTED=v;
	
		LAST_SELECTED=SID_ARRAY[v];
		setTimeout(td_js_selector_cookie,10);
	}
	setTimeout(td_js_selector_init,100);
</script>
