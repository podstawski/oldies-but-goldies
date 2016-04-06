/*
opTooltip jQuery Plugin ver. 1.1
12.04.2011

author: Pawel Ostolski/SQ9ATK
ostol@tlen.pl or other in future :-)

EXAMPLE:

jQuery('*[title]').opTooltip({
'css':{
'color':'black',
'background-color':'white'
},
'text':function(element){   //required
return jQuery(element).attr('title')
}
});
*/

$.fn.opTooltip = function(options) {

  var defaults = {};

  defaults.css = {

    'background-color':'#ffffff',
    'border':'1px solid #888888',
    'display':'none',
    //'white-space':'nowrap',
    //'width':'250px',
    'position':'absolute',
    'padding':'3px 5px 3px 5px',
    'z-index':'1000',
    //css 3
    'filter':'progid:DXImageTransform.Microsoft.Shadow(color=\'#777777\',direction=\'120\',strength=\'3\')',
    'opacity': '0.8',
    '-moz-box-shadow':'2px 2px 3px #666',
    '-webkit-box-shadow':'2px 2px 3px #666',
    'box-shadow':'2px 2px 3px #666',
    '-moz-border-radius':'4px',
    '-webkit-border-radius':'4px',
    'border-radius':'4px',
    'cursor':'pointer'
  };

  var opt = $.fn.opTooltip;


  if(typeof opt.tooltip == 'undefined'){
    opt.tooltip = new jQuery('<div id="opTooltip" >');
    $('body').append(opt.tooltip);
  }

  jQuery(this).bind('click', function(){
    opt.tooltip.hide();
  });

  jQuery(this)
  .hover( function() {

    opt.tooltip.css(defaults.css);

    if(options.css){
      opt.tooltip.css(options.css);
    }

    var content = options.text(this);

    if(content && content.length > 1 ){

      opt.tooltip
      .html( content )
      .show();

      if( $(this).attr('title') ){
        $(this)
        .data('title', $(this).attr('title')) //czyścimy title zeby sie chmurka systemowa nie pokazywała
        .attr('title', '');
      }
    }

  }, function() {

    if( $(this).data('title' ) ){
      $(this)
      .attr('title', $(this).data('title'))// przywracamy atrybut title zeby było na czym pracowac przy następnym najechaniu myszką :-)
      .data('title','');
    }

    opt.tooltip
    .html('')
    .hide();

  })
  .mousemove( function(e) {
    opt.move(e);
  });//end opt.target

  opt.move = function(event) {

    var viewport = opt.viewPort();

    //var left = event.clientX + document.body.scrollLeft + jQuery(document).scrollLeft();
    var left = event.clientX +  jQuery(document).scrollLeft();

    if(opt.tooltip.width() + event.clientX + 51> viewport.width  ) {
      left = left - opt.tooltip.width() - 20 + "px";
    } else {
      left = left + 13 + "px";
    }

    //var top  = event.clientY + document.body.scrollTop + jQuery(document).scrollTop();
    var top  = event.clientY +  jQuery(document).scrollTop();

    if(opt.tooltip.height() + event.clientY + 40> viewport.height) {
      top = top - opt.tooltip.height() - 15 + "px";
    } else {
      top = top + 25 + "px";
    }

    opt.tooltip.css({
      'left': left,
      'top':  top
    });

  };



  opt.viewPort = function() {
    //funckja zwraca rozmiary obszaru roboczego przeglądarki

    var width;
    var height;

    if (typeof window.innerWidth !== 'undefined'){
      // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
      width = window.innerWidth;
      height = window.innerHeight;
    }else if (typeof document.documentElement !== 'undefined' && typeof document.documentElement.clientWidth !== 'undefined' && document.documentElement.clientWidth !== 0){
      // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
      width = document.documentElement.clientWidth;
      height = document.documentElement.clientHeight;
    }else{
      // older versions of IE
      width = document.getElementsByTagName('body')[0].clientWidth;
      height = document.getElementsByTagName('body')[0].clientHeight;
    }

    var viewport = {
      'width': width,
      'height': height
    };
    return viewport;
  };

};


