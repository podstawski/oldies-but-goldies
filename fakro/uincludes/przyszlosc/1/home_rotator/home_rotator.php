<?php
  global $SERVER_ID,$lang,$ver,$adodb,$DEFAULT_PATH_PAGES,$WEBTD;

  $menu_id = $WEBTD->menu_id;
  $menuArray = kameleon_menus($menu_id);

  $res = '';

  if(is_array($menuArray) && !empty($menuArray)) {

    $res .= '<div id="home_slideshow"><div id="slide_navigation" class="clearfix"></div>';
    $res .= '<ul id="home_slides">';

    foreach($menuArray as $k => $v) {

      $href = kameleon_href('','',$v->page_target);
      $res .= '<li style="position: absolute; top: 0px; left: 0px; display: none; width: 545px; height: 333px;">';
      $res .= '<img src="'.$UIMAGES.'/'.$v->img.'" alt="" border="0">';
      $res .= '<div class="slide_caption">';
      $res .= '<div class="slide_title"><a href="'.$href.'">'.$v->alt_title.'</a></div>';
      $res .= $v->alt;
      $res .= '</div>';
      $res .= '</li>';

    }

    $res .= '</ul>';
    $res .= '<div id="home_slideshow_violator" class="clearfix">';
    $res .= '<div style="display: block;" id="project_caption"></div>';
    $res .= '</div>';
    $res .= '</div>';

  }
  echo $res;
?>

<style>
  #home_slideshow { position:relative; overflow:hidden !important; }
  #home_slideshow { font: 12px/20px arial,sans-serif;}
  #home_slideshow { margin: 0 !important; padding: 0 !important }

  #home_slideshow a { font-size: 20px; text-decoration:none; color:#FFFFFF; }
  #home_slideshow a:hover { text-decoration:none; }
  #home_slideshow ul { position: relative; width: 545px; height: 333px; list-style-type: none; margin: 0; padding: 0; }
  #home_slideshow li div.slide_caption { display:none; }

  #home_slides { overflow:hidden; height:333px; }
  #home_slides { margin: 0 !important; padding: 0 !important }
  #home_slides div.slide_title { margin: 3px 0; padding: 0; display: block; }

  #home_slideshow_violator{ bottom: 0; left: 0; overflow: hidden; position: absolute; width: 100%; z-index:999; }

  #project_caption{  width: 95%; overflow:hidden; margin: 5px 10px; height: 65px; color:#FFFFFF; float:left; text-align: left; }

  #slide_navigation { z-index:1000; position:absolute; right:10px; bottom:50px; }
  #slide_navigation a { margin: 0 3px; padding: 2px 4px; text-decoration: none; font-size: 10px !important; text-decoration:none; background-color:#00AE00; color: #004800 !important; }
  #slide_navigation a:hover { background-color:#004800; color: #00AE00 !important; }
  #slide_navigation a.activeSlide { background-color:#004800; color: #00AE00 !important; }

  .clearfix:after { content:"."; display:block; height:0;clear:both; visibility:hidden; }
  .clearfix { display:inline-block; }
  .clearfix { display:block; }
</style>
<script type="text/javascript" language="javascript">

  jQuery(function(){
    $('#home_slides').cycle({
      fx:'fade',
      timeout:8000,
      pager:'#slide_navigation',
      after:update_slide_caption
    })
  });

  fade_slide_caption = function(next, previous) {
    caption_container = $('#project_caption');
    caption_container.fadeOut('fast');
  }

  update_slide_caption = function(next, previous) {
    caption_container = $('#project_caption');
    caption = $('div.slide_caption', previous);
    caption_container.fadeIn('fast');
    caption_container.html(caption.html());
  }

</script>