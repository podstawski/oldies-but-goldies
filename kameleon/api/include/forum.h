<?
include_once("include/forumfun.h");
include_once("captcha/kcaptcha.php");

if (strlen($page)==0) return;

if ($api_em )
{
	$api_mode = set_cos_api_mode();

	$query="SELECT * FROM forum_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount()) parse_str(ado_ExplodeName($result,0));


	$admin="
		<fieldset style=\"width:99%; margin-left:2px;\">
		<legend>".label('Forum settings')."</legend>
		<form method=post name=api_forum  action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>

		<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
		<tr>
			<td colspan=2>
				".label("Type email to moderator")."<br>
				<input style=\"width:100%\" class=k_input type=text size=30 name=apiForumEmail value='$email'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				".label("Subject")."<br>
				<input style=\"width:100%\" class=k_input type=text size=30 name=apiForumSubject value='$subject'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				".label("Dictionary")."<br>
				<textarea style=\"width:100%\" class=k_input cols=30 rows=5 name=apiForumSlownik>$slownik</textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type='hidden' value='0' name='api_form_opt[0]'>
				<input type='checkbox' value='$sid' name='api_form_opt[2]' ".(($api_mode & 2)?'checked':'')." title='szablony/xxx/images: forum_answer.gif, forum_return.gif, forum_tree.gif, spacer.gif '> ".label("forum user images")."<br>
				<input type='checkbox' value='$sid' name='api_form_opt[4]' ".(($api_mode & 4)?'checked':'')."> ".label("search engine")."<br>
				<input type='checkbox' value='$sid' name='api_form_opt[8]' ".(($api_mode & 8)?'checked':'')."> ".label("new nodes forbidden")."<br>
				<input type='checkbox' value='$sid' name='api_form_opt[16]' ".(($api_mode & 16)?'checked':'')."> ".label("enable captcha")."<br>
				<input type='checkbox' value='$sid' name='api_form_opt[32]' ".(($api_mode & 32)?'checked':'')."> ".label("image captcha")."<br>
			</td>
			<td align=right valign=bottom>
			<input type='submit' class='k_button' value='".label("Save")."' onClick=\"document.api_forum.api_action.value='apiZapiszForumUstawienia'\">
			</td>
		</tr>
		</table>
		</form>
		</fieldset>
		<br/>&nbsp;		
		";


	echo $admin;
}



if (strlen($api_size)==0)
	$limit=10;
else
	$limit=$api_size;


$siteName="";
$AUTH_ID=1;

if ($api_km)
	$href="$api_next";
else
	$href="$api_next?a=1";



if ($api_km)
$href="$api_next";

if ($api_mode & 1)
	$serwisID="";
else
	$serwisID="$page";

if ($api_mode & 2)
	$FORUMIMAGES="/images";
else
	$FORUMIMAGES="$API_SERVER/images";

//CLASS
//class=api_forum_title
//class=api_forum_caption
//class=api_forum_message
//class=api_forum_info
//class=api_forum_backcolor
//class=api_forum_activecolor
//class=api_forum_noactivecolor
//class=api_forum_button 
//class=api_forum_input

?>
<table border="0" cellspacing="2" cellpadding="2">
<?
if ($api_mode & 4)
  echo "
	<tr>
	<td colspan=\"2\">
	<form method=post action=$api_next>
	  $GLOBAL_HIDDEN
		<input type=text class=api_forum_input size=15 name=forum_szukaj value='$forum_szukaj'>
		<input type='submit' class=api_forum_button value=\"".label("Search")."\">
	</form>
	</td>
	</tr>";


if ($apiForumid)
{
   if (($apiForumid == $pokazuj) && ($AUTH_ID>0))
      $back_class_color="class=api_forum_activecolor";
   else
      $back_class_color="class=api_forum_noactivecolor";

   $forum_answer = "
    <a href=$href&pokazuj=$apiForumid&apiForumid=$apiForumid>
    <img src=".$FORUMIMAGES."/forum_answer.gif width=19 height=11 hspace=0 vspace=0 border=0 alt='".label("Reply to")."'></a>";
                
   echo "
    <tr>
     <td colspan=2 class=api_forum_info>".label("Main subject").":</td>
    </tr>
    <tr class=api_forum_backcolor>
     <td nowrap valign=_top>
      <a href=$href&pokazuj=$apiForumid&apiForumid=$parentforum>
      <img src=".$FORUMIMAGES."/forum_return.gif width=19 height=11 hspace=0 vspace=0 border=0 alt='".label("Return to main subject")."'></a>
      $forum_answer
     </td>
     <td width=100%>
      <span class=api_forum_title>".GetForumSubject($adodb,$apiForumid)."</span><br>
      <span class=api_forum_message>".GetForumTxt($adodb,$apiForumid)."</span>          
     </td>
    </tr>
    <tr><td colspan=2 class=api_forum_info>".label("Answers").":</td></tr>";
}
else
  echo "<tr><td colspan=2 class=api_forum_info>".label("Main subjects").":</td></tr>";
			
if (strlen($forum_szukaj))
  $f=ForumSearch($adodb,$serwisID, $forum_szukaj);
else
  $f=GetForum($adodb,$serwisID,$apiForumid+0);

if (is_array($f))
  for (($i=count($f)-1);$i>=0;$i--)
  {
    $forum=$f[$i];
    if (($forum[0] == $pokazuj)  && ($AUTH_ID>0))
      $back_class_color = "class=api_forum_activecolor";
    else
      $back_class_color = "class=api_forum_noactivecolor";
    if ($forum[4]>0)
      $forum_plus = "<a href=$href&pokazuj=$forum[0]&apiForumid=$forum[0]&parentforum=$apiForumid>
        <img src=".$FORUMIMAGES."/forum_tree.gif width=19 height=11 hspace=0 vspace=0 border=0 alt='".label("Explore")."'></a>";
    else 
      $forum_plus = "<img src=".$FORUMIMAGES."/spacer.gif width=19 height=11 hspace=0 vspace=0 border=0>";

    if ($AUTH_ID>0)
      $forum_answer = "<a href=$href&pokazuj=$forum[0]&apiForumid=$apiForumid#forumquest>
        <img src=".$FORUMIMAGES."/forum_answer.gif width=19 height=11 hspace=0 vspace=0 border=0 alt='".label("Reply to")."'></a>";				
    if ($api_em)
      $delete="<a href=javascript:api_zmiana('$forum[0]','apiUsunForum')><img src=$API_URL/img/ikona-smietnik-b.gif alt='".label("Delete")."' width=12 height=12 border=0></a>";
    else
      $delete="";
    echo "
     <tr $back_class_color>
      <td nowrap valign=_top>
       $delete
       $forum_plus
       $forum_answer
      </td>
      <td width=100%>
        <span class=api_forum_title>".$forum[3]."</span><br>
        <span class=api_forum_caption>".$forum[1].", ".$forum[2].", ".label("answers").": ".$forum[4]."</span><br>
        <span class=api_forum_message>".$forum[5]."</span>          
      </td>
      </tr>
      <tr><td colspan=2 class=api_forum_backcolor> </td></tr>
     ";
     if ($i == (count($f)-20) ) break;
  }
?>
<form name=api_zmiany method=post action=<?echo $api_next?>>
 <?echo $GLOBAL_HIDDEN?>
 <input type=hidden name=api_action value="">
 <input type=hidden name=api_id value="">
 <input type=hidden name=page value="<?echo $page?>">
</form>
<script>
function api_zmiana(pri,api_action)
{
	if ( api_action=="apiUsunForum"  
		&& !confirm("<?echo label("Are you sure?")?>")) return;

	document.api_zmiany.api_id.value=pri;	
	document.api_zmiany.api_action.value=api_action;	
	document.api_zmiany.submit();
}
</script>
</table>

<?
if ($pokazuj>0) 
{
  $forumtyt = label("Reply to");
  $forumsub = label("Answer")." ";
  $forumsub.= GetForumSubject($adodb,$pokazuj);
}
else 
{
  $forumtyt=label("New main subject");
  $forumsub="";
  if ($apiForumid > 0)
  {
    $forumtyt = "Odpowiedz na bie¿¹cy temat.";
    $forumsub = "Odp. ";
    $forumsub.= GetForumSubject($adodb,$apiForumid);
  }
}


include ("include/forum-form.h");
?>
