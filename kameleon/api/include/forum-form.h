<?

if ($FORUMFORM==1) return;

$FORUMFORM=1;
include_once("captcha/kcaptcha.php");

$forumForm= "
<form method=post action=$api_next>
  $GLOBAL_HIDDEN
  <input type=hidden name=api_action value='apiSetForumTxt'>
  <input type=hidden name=serwisID value='$serwisID'>
  <input type=hidden name=pokazuj value='$pokazuj'>
  <input type=hidden name=apiForumid value='$apiForumid'>
  <table border=0 cellspacing=0 cellpadding=0 class=\"api_forum_form\">
  <tr>
   <td class=api_forum_info>&nbsp;$forumtyt</td></tr>
  <tr>
   <td $back_class_color>
    &nbsp;<b>".label("Your name").":</b><br>&nbsp;<input type='text' name='forum_osoba' value='$api_osoba' size='50' maxlength='50' class='api_forum_input'><br>
    &nbsp;<b>".label("Subject").":</b><br>&nbsp;<input type='text' name='forum_temat' value='$forumsub' size='50' maxlength='50' class='api_forum_input'><br>
    &nbsp;<b>".label("Message").":</b><br>&nbsp;<textarea rows=5 cols=50 name=forum_msg class='api_forum_input'></textarea>
   </td>
  </tr>
<!--  <tr>
   <td $back_class_color align=right>
     $forum_dodatkowe_pytanie
   </td>
  </tr>
-->
  <tr>
   <td $back_class_color align=center>
   " . KCAPTCHA::KCAPTCHA() . "
    <input type='submit' class=api_forum_button value='".label("Save")."'>
   </td>
  </tr>
 </table>
</form>";

$api_mode+=0;

if (($api_mode & 8))
{
	if ($pokazuj>0)
	{
		echo $forumForm;		
	}
}
else
	echo $forumForm;



?>