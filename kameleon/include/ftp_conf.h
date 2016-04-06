
<form method="post" name="link" action="<?echo $SCRIPT_NAME?>">
<input name="action" type="hidden" value="SaveFtpConf">
<div id="advanced">
  <div class="litem_1">
    <label><?echo label("FTP Server [:port]");?>:</label>
    <div class="inputer"><input type="text" size="30" name="ftp_server" value="<?echo $ftp_server?>" /></div>
  </div>
  <div class="litem_2">
    <label><?echo label("FTP Username");?>:</label>
    <div class="inputer"><input type="text" size="30" name="ftp_user" value="<?echo $ftp_user?>" /></div>
  </div>
  <div class="litem_1">
    <label><?echo label("Users password");?>:</label>
    <div class="inputer"><input type="password" size="30" name="ftp_pass" /></div>
  </div>
  <div class="litem_2">
    <label><?echo label("Directory name to change after connect");?>:</label>
    <div class="inputer"><input type="text" size="30" name="ftp_dir" value="<?echo $ftp_dir?>"></div>
  </div>
</div>
<input type="image" src="img/i_save_n.gif" onmouseover="this.src='img/i_save_a.gif'" onmouseout="this.src='img/i_save_n.gif'" value='<?echo label("Save menu link")?>'>
</form>
