<?

/**
* Returns an array with browser information
*
* @access public
* @return array Array contains keys name, version and subversion
*/
function detect_browser()
{
  if(eregi('msie ([0-9].[0-9]{1,2})',$_SERVER['HTTP_USER_AGENT'],$log_version))
  {
    $browser['version']=$log_version[1];
    $browser['name']='MSIE';
  }elseif(eregi('opera/([0-9].[0-9]{1,2})',$_SERVER['HTTP_USER_AGENT'],$log_version))
  {
    $browser['version']=$log_version[1];
    $browser['name']='OPERA';
  }elseif(eregi('mozilla/([0-9].[0-9]{1,2}).*gecko/([0-9]+)',$_SERVER['HTTP_USER_AGENT'],$log_version))
  {
    $browser['version']=$log_version[1];
    $browser['name']='MOZILLA';
    $browser['subversion']=$log_version[2];
  }elseif(eregi('netscape/([0-9].[0-9]{1,2})',$_SERVER['HTTP_USER_AGENT'],$log_version))
  {
    $browser['version']=$log_version[1];
    $browser['name']='NETSCAPE';
  }else
  {
    $browser['version']=0;
    $browser['name']='OTHER';
  }
  return $browser;
}


function hex2dec($html_color)
{
  $R = hexdec(substr($html_color,1,2));
  $G = hexdec(substr($html_color,3,2));
  $B = hexdec(substr($html_color,5,2));
  $color = array();
  $color['R']=$R;
  $color['G']=$G;
  $color['B']=$B;
  return $color;
}

?>
