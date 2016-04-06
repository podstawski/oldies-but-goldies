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
