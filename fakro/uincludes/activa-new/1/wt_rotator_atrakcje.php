<script>
  var $container;
  $(document).ready(function() {
    $container = $("#container")
    $container .wtRotator({
      width:738,
      height:248,
      background_color:"black",
      border:"1px solid #F76C10",
      button_width:24,
      button_height:24,
      button_margin:4,
      auto_start:true,
      delay:5000,
      transition:"random",
      transition_speed:800,
      block_size:80,
      vert_size:35,
      horz_size:35,
      cpanel_align:"BR",
      display_thumbs:false,
      display_dbuttons:false,
      display_playbutton:false,
      display_tooltip:true,
      display_numbers:true,
      cpanel_mouseover:false,
      text_mouseover:false
    });
  });
</script>
<pre><?php

    global $SERVER_ID,$lang,$ver,$adodb,$DEFAULT_PATH_PAGES,$WEBTD;

    $menu_id = $WEBTD->menu_id;
    $menuArray = kameleon_menus($menu_id);

?></pre>

<div class="panel">

  <div id="container" style="width:740px;height:250px;overflow:hidden" >
    <!-- begin rotator -->
    <div class="wt-rotator">
      <a href="#"><img id="main-img" src="<?php echo $UIMAGES.'/wt_rotator/zima_2011/wt_atrakcje_zima_03.jpg' ?>"/></a>

      <div class="desc"></div>
      <div class="preloader"><img src="http://s3.envato.com/files/353428/assets/loader.gif"/></div>
      <div id="tooltip"></div>

      <div class="c-panel">
        <div class="buttons">
          <div class="prev-btn"></div>
          <div class="play-btn"></div>
          <div class="next-btn"></div>
        </div>

        <div class="thumbnails">
          <ul>
            <?php
              foreach($menuArray  as $key => $row){
                $pos = explode(',',$row->alt);
                echo '<li>
                <a href="'.$UIMAGES.'/'.$row->img.'" title=""></a>';

                if(strlen(trim(kameleon_href('','',$row->page_target)) )>0 ){
                  echo '<a href="'.kameleon_href('','',$row->page_target).'" target="'.$row->target.'" alt="" ></a>';
                }else{
                  echo '<a href="'.$row->href.'" target="'.$row->target.'" alt="" ></a>';
                }
                echo '<p x="'.$pos[0].'" y="'.$pos[1].'" w="'.$pos[2].'" ><span style="font-size:14px; color:#CCC">'.$row->alt_title.'</span><br/>'.$row->description.'</p></li>';
              }
            ?>
          </ul>
        </div>

      </div>

    </div>
    <!-- end rotator -->
  </div>

</div>











