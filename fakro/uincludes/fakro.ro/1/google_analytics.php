<?
$CFG['google'] = 0;
$CFG['google_analytics_ip'][]           = "213.77.44.2";
$CFG['google_analytics_ip'][]           = "213.77.44.5";
$CFG['google_analytics_ip'][]           = "213.77.44.13";
$CFG['google_analytics_ip'][]           = "89.174.30.18";
$CFG['google_analytics_ip'][]           = "89.174.30.19";

for($i=0; $i<=count($CFG['google_analytics_ip']); $i++) {
        if($_SERVER['REMOTE_ADDR'] == $CFG['google_analytics_ip'][$i]) $CFG['google'] = 1;
        }

if(!$CFG['google']) { ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-973963-25']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<? } ?>
