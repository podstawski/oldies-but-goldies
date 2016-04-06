<?
	$STATUSY[0]=sysmsg("status_0","status");
	$STATUSY[1]=sysmsg("status_1","status");
	$STATUSY[2]=sysmsg("status_2","status");
	$STATUSY[3]=sysmsg("status_3","status");
	$STATUSY[-1]=sysmsg("status_-1","status");
	$STATUSY[-5]=sysmsg("status_-5","status");

	$delim=$costxt;
	if (!strlen($delim)) $delim=", ";

	$stat="";
	$statusy_cookie=$_REQUEST[statusy];

	while ( list($s,$label)=each($STATUSY) )
	{
		if (!is_array($statusy_cookie))
		{
			echo "<script>
					document.cookie='statusy[$s]=1';
					</script>";
		}

		if (strlen($stat)) $stat.=$delim;
		$checked=($statusy_cookie[$s] || !is_array($statusy_cookie)) ? "checked" : "";
		$stat.= "<input type=\"checkbox\" value=1 name=\"s$s\" 
					onClick=\"status_checkbox_clicked(this)\" $checked> $label";

	}

?>
<span class="zakres_dat"><? echo $stat?></span>
<script language="JavaScript"> 
	function status_checkbox_clicked(obj)
	{
		cname=obj.name.substr(1);
		v=(obj.checked) ? '1' : '0';
		document.cookie='statusy['+cname+']='+v;
	}
</script>
