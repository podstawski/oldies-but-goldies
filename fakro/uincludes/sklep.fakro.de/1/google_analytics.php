<?
$CFG['google_analytics_ip'][]           = "213.77.44.2";
$CFG['google_analytics_ip'][]           = "213.77.44.5";
$CFG['google_analytics_ip'][]           = "213.77.44.13";
$CFG['google_analytics_ip'][]           = "89.174.30.18";

for($i=0; $i<=count($CFG['google_analytics_ip']); $i++) {
        if($REMOTE_ADDR == $CFG['google_analytics_ip'][$i]) $CFG['google'] = 1;
        }

if(!$CFG['google'])
{
	$re_uacct[pl]	= 'UA-973963-2';
	$re_uacct[en]	= 'UA-973963-1';
	$re_uacct[de]	= 'UA-973963-3';
	$re_uacct[fr]	= 'UA-973963-4';
	$re_uacct[nl]	= 'UA-973963-5';
	$re_uacct[ru]	= 'UA-973963-6';
	
	if($re_uacct[$lang])
	$re = '
			<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
			<script type="text/javascript">
				_uacct = "'.$re_uacct[$lang].'";
				urchinTracker();
			</script>';
	//echo $re;
}
?>
