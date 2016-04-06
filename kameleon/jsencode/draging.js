
jQueryKam('.km_dragbox').each(function(){
	jQueryKam(this).hover(function(){
		jQueryKam(this).find('.km_dragicon').addClass('collapse');
	}, function(){
		jQueryKam(this).find('.km_dragicon').removeClass('collapse');
	})
	.find('h2').hover(function(){
		jQueryKam(this).find('.configure').css('visibility', 'visible');
	}, function(){
		jQueryKam(this).find('.configure').css('visibility', 'hidden');
	})
	.click(function(){
		jQueryKam(this).siblings('.dragbox-content').toggle();
	})
	.end()
	.find('.configure').css('visibility', 'hidden');
});

kameleon_draging=true;


function km_levelname_display(val,hfb)
{
  if (val==true)
  {
    jQueryKam('.km_dragdrop_place').addClass('km_dragdrop_place_active');
    jQueryKam('.km_szpaltanames_'+hfb).show();
    jQueryKam('.km_dragdrop_'+hfb).addClass('km_dragdrop_place_active');
    jQueryKam('.km_draghelper').addClass('km_draghelper_show');
    jQueryKam('.km_dragon').addClass('km_dragon_on');
  }
  else
  {
  //jQueryKam('object').css('display','block');
    jQueryKam('.km_dragdrop_place').removeClass('km_dragdrop_place_active');
    jQueryKam('.km_szpaltanames_'+hfb).hide();
    jQueryKam('.km_dragdrop_'+hfb).removeClass('km_dragdrop_place_active');
    jQueryKam('.km_draghelper').removeClass('km_draghelper_show');
    jQueryKam('.km_dragon').removeClass('km_dragon_on');
  }
}

jQueryKam(document).ready( function() {
	jQueryKam('.km_dragdrop_body').sortable({  
	    connectWith: '.km_dragdrop_body',  
	    handle: '.km_dragicon',  
	    cursor: 'move',  
	    placeholder: 'km_placeholder',  
	    forceHelperSize : true,
	    forcePlaceholderSize: true,
	    tolerance: 'intersect', 
	    revert: true,
	    distance: 30,
	    opacity: 0.4,
	    start : function(event, ui) {
	      km_levelname_display(true,"body");
	    },
	    stop: function(event, ui){
	        km_levelname_display(false,"body");
	        var tm_drag='';
	
	        jQueryKam(ui.item).parent().find('.km_dragbox').each(
	          function (i) {
	            tm_drag+=jQueryKam(this).attr('title')+';';
	          }
	        );
	        var level = jQueryKam(ui.item).parent().attr("title");
	  		var sid = jQueryKam(ui.item).attr("title");
	        km_module_drag(level,sid,tm_drag);
		}
  	});//.disableSelection();
  
	jQueryKam('.km_dragdrop_head').sortable({  
	    connectWith: '.km_dragdrop_head',  
	    handle: '.km_dragicon',  
	    cursor: 'move',  
	    placeholder: 'km_placeholder',  
	    forceHelperSize : true,
	    forcePlaceholderSize: true,
	    tolerance: 'pointer', 
	    revert: true,
	    distance: 30,
	    opacity: 0.4,
	    start : function(event, ui) {
	      km_levelname_display(true,"head");
	    },
	    stop: function(event, ui){
	        km_levelname_display(false,"head");
	        var tm_drag='';
	
	        jQueryKam(ui.item).parent().find('.km_dragbox').each(
	          function (i) {
	            tm_drag+=jQueryKam(this).attr('title')+';';
	          }
	        );
	        var level = jQueryKam(ui.item).parent().attr("title");
			var sid = jQueryKam(ui.item).attr("title");
			km_module_drag(level,sid,tm_drag); 
	    }
	});
  
	jQueryKam('.km_dragdrop_foot').sortable({  
	    connectWith: '.km_dragdrop_foot',  
	    handle: '.km_dragicon',  
	    cursor: 'move',  
	    placeholder: 'km_placeholder',  
	    forceHelperSize : true,
	    forcePlaceholderSize: true,
	    tolerance: 'pointer', 
	    revert: true,
	    distance: 30,
	    opacity: 0.4,
	    start : function(event, ui) {
	      km_levelname_display(true,"foot");
	    },
	    stop : function(event, ui){
	        km_levelname_display(false,"foot");
	        var tm_drag='';
	        
	        jQueryKam(ui.item).parent().find('.km_dragbox').each(
	          function (i) {
	            tm_drag+=jQueryKam(this).attr('title')+';';
	          }
	        );
	        var level = jQueryKam(ui.item).parent().attr("title");
			var sid = jQueryKam(ui.item).attr("title");
			km_module_drag(level,sid,tm_drag);
	    }    
	});
});