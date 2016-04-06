<?php
	foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
	foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");	

	include("./label.php");
	include("./chk.php");
	include("./table.php");

?>
<html>
<head>
    <title>KAMELEON: SETUP</title>
    <link href="../kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo label("CHARSET")?>">
</head>
<body bgcolor="#c0c0c0" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>

<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
	<td class=k_td>
<?php
	echo "<div align=right class=k_text><A href='http://www.gammanet.pl/' 
		style='TEXT-DECORATION: none' target=_blank><FONT 
		color=#02461f face=Arial size=2><B>gamma</B></FONT><FONT 
		color=#fc7116 face=Arial size=2><B>net</B></FONT></A> - 
		<A href='http://www.webkameleon.com/' 
		style='TEXT-DECORATION: none' target=_blank>
		Web Kameleon</a>

		</div>";
?>
	<br>
	<br>
	<table bgcolor="silver" valign=top align="center" border="3" cellspacing="0" cellpadding="5">
	<tr>
		<td class=k_formtitle>
		<b><a class=k_formtitle 
			href="<?php echo $SCRIPT_NAME?>"><?php echo label("Check again");?></a></b>
		</td>
	</tr>
	<tr>
		<td class=k_td>
		<?php echo $TABLE; ?>
		</td>
	</tr>

	<tr>
		<td class=k_formtitle>
		<?php 
			echo label("Caption"); 
		?>
		</td>
	</tr>

	<tr>
		<td class=k_td>
		<img src=ok.gif align=absMiddle hspace=5 vspace=5> <?php echo label("Option is configured very well")?>
		<br>
		<img src=hym.gif align=absMiddle hspace=5 vspace=5> <?php echo label("Option is not required, but its lack may cause some errors")?>
		<br>

		<img src=stop.gif align=absMiddle hspace=5 vspace=5> <?php echo label("webKameleon will not work without this configured")?>

		</td>
	</tr>

	<tr>
		<td class=k_formtitle style="cursor:hand" 
			onClick="document.all.const_h_form.style.display=(document.all.const_h_form.style.display=='none')?'inline':'none';">
		<?php 
			echo label("configuration_file")." (log/const.php)"; 
		?>
		</td>
	</tr>

	<tr id="const_h_form" style="display:none">
		<td class=k_td >
			<?echo $FORM?>
		</td>
	</tr>

	</table>
	<br>
	<br>
	
	</td>
</tr>
</table>

</body>
</html>
