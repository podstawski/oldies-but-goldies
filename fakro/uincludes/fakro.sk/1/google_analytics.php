<?
$CFG['google_analytics_ip'][]           = "89.174.30.18";
$CFG['google_analytics_ip'][]           = "89.174.30.19";

for($i=0; $i<=count($CFG['google_analytics_ip']); $i++) {
        if($REMOTE_ADDR == $CFG['google_analytics_ip'][$i]) $CFG['google'] = 1;
        }

if(!$CFG['google'])
{
	if($_SERVER['HTTP_HOST'] === 'www.fakrosk.com') {	$re_uacct = 'UA-973963-12'; }
	if($_SERVER['HTTP_HOST'] === 'www.fakro.sk') {		$re_uacct = 'UA-973963-49'; }
	
	if($re_uacct)
	$re = '
			<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
			<script type="text/javascript">
				_uacct = "'.$re_uacct.'";
				urchinTracker();
			</script>';
	echo $re;
	
	if($_SERVER['HTTP_HOST'] === 'www.fakro.sk') {
		$re_uacct = 'UA-26421203-1';
		
		if($re_uacct)
		$re = '
			<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
			<script type="text/javascript">
				_uacct = "'.$re_uacct.'";
				urchinTracker();
			</script>';
		echo $re;
	}
}
?>
