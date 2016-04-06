<?
$copyright_date = date("Y");

$rev=$adodb->GetCookie('KAMELEON_VERSION_REV');
if (strlen($rev)) $rev=".$rev";


$who=$kameleon->user['fullname'];
if (strlen($who))
{
	$who=label('Logged in').':<br /><span style="font-size: 12px; color: #979797; font-weight: bold;">'.$who.'</span>';
}

if (strlen($CONST_LICENSE_NAME))	
{
	$lic="".label("License for").": <b>$CONST_LICENSE_NAME</b>";;
	if ($CONST_LICENSE_NAME=="-") $lic="";
	
	echo "
    <div class=\"km_head\">
      <div class=\"km_left\">
        <a href=\"javascript:licence()\" class=\"km_head_logo km_icon km_iconkm_logo\">webkameleon</a> 
        <div class=\"km_verinfo\">$KAMELEON_VERSION$rev, <a href=\"javascript:licence()\">".label("Copyright")." &copy; ".$copyright_date." Gammanet Sp. z o.o. ".$lic."</a></div>  
      </div>";
      if (strlen($identity_sysinfo)>0) 
      {
      	if (strlen($identity_sysinfo_link)) $identity_sysinfo="<a href=\"$identity_sysinfo_link\" $identity_sysinfo_more>$identity_sysinfo</a>";
      	echo "<div class=\"km_sysinfo\"><span class=\"km_icon km_iconi_sysinfo\"></span>".$identity_sysinfo."</div>";
	}
      
  echo "
      <div class=\"km_right\"><span class=\"km_icon km_iconkm_profile\"></span>$who<div class=\"km_mini_icons\"><a class=\"km_logout_icon\" href=\"index.php?action=KameleonLogout\">".label("Logout")."</a><a href=\"#\" class=\"km_chpass_icon\" id=\"km_chpass_open\">".label("Change password")."</a></div></div>
      <div class=\"km_clean\"></div>
    </div>  
  ";
}
else	
{
  echo "
    <div class=\"km_head\">
      <div class=\"km_left\">
        <div><img src=\"".$kameleon->user[skinpath]."/img/nkam/kameleon_logo.gif\" alt=\"webkameleon\" /></div>
        <div class=\"km_verinfo\">ver: $KAMELEON_VERSION, <a href=\"javascript:licence()\">Copyright &copy; ".$copyright_date." Gammanet Sp. z o.o. All right reserved.</a></div> 
      </div>
      <div class=\"km_right\" style=\"background-image: url('".$kameleon->user[skinpath]."/img/nkam/head_profile.gif');\">$who<br />$lic</div>
      <div class=\"km_clean\"></div>
    </div>  
  ";
}
?>
