<?
	$help_file="include/help_$mybasename.h";
	$user_help_file="incuser/help_$mybasename.h";

	if (!strlen($icon)) $icon="img/i_next_n.gif";


	if (file_exists($user_help_file)) include($user_help_file);
	if (file_exists($help_file)) include($help_file);

	include_js("help");
?>



<div id="kameleon_help" style="position:absolute;top:300;left:5;width:145;background:url('img/help.gif');visibility:hidden;z-index:100" 
	onMouseOver="CloseKameleonHelp()">
	<div id="kameleon_help_txt" class="k_text"
		style="margin-top:65;margin-left:20;margin-right:5;margin-bottom:5;color:white;font-weight:bold" >
		&nbsp;
	</div>
</div>

