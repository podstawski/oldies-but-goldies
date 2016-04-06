(function($){
    $(document).ready(function(){
        $("#tabs").tabs();

        $("#subscribe, #registration-form, a#my-courses, a#go-back, input:submit").button();

        $(".datepicker").datepicker();

        $("ul.errors").prev("input").addClass("errors").tooltip({
            position : "center left",
            offset : [ -34, -5 ],
            events : {
                input : "mouseover focus,mouseout blur"
            }
        });
        
        $("input[id][autocomplete=true]").each(function(){
            var field = $(this).attr("id");
            $(this).autocomplete({
                source : AUTOCOMPLETE_URL + "?field=" + field
            });
        });
    });
})(jQuery);
