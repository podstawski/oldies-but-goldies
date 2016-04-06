<?php

echo "
<script type=\"text/javascript\">
	var identyfiersArray=new Array();

	var km_infos = new Array();
	km_infos[\"ajax_link\"]='ajax.php';
	km_infos[\"page_link\"]='".$SCRIPT_NAME."';
	km_infos[\"page\"]='".$page."';
	km_infos[\"return_link\"]='".base64_encode($_SERVER["REQUEST_URI"])."';	
	var km_droplist = new Array();
	km_droplist['active']=\"\";
	
</script>
";
