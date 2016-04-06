<?

	global $td_js_sel_upd,$td_js_sel_cookie;

	if (strlen($td_js_sel_upd))
	{
		$td_js_sel_cookie=$td_js_sel_upd;
	}


	if (strstr($td_js_sel_cookie,":$page:"))
	{
		$newcookie=str_replace(":$page:",':',$td_js_sel_cookie);
		
		foreach (explode(':',$newcookie) AS $np)
			if (strlen($np)) break;

		if ($np)
			echo "$np
		<script>
			document.cookie='td_js_sel_cookie=$newcookie';
			location.href='index.php?page=$np';
		</script>";

	}
?>
