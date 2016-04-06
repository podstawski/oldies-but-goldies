function sparse()
{
    if (arguments.length == 0) return '';
    var text = arguments[0];
    for (var i = 1, L = arguments.length; i < L; i++) {
        text = text.replace('%s', arguments[i]);
    }
    return text;
}

function number_format(number, decimals, dec_point, thousands_sep)
{
    // http://kevin.vanzonneveld.net
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number, prec = !isFinite(+decimals) ? 0 : Math.abs(decimals), sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, dec = (typeof dec_point === 'undefined') ? '.' : dec_point, s = '', toFixedFix = function (n, prec)
    {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3)
    {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec)
    {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function currency(number, decimals)
{
    return number_format(number, decimals != null ? decimals : 2, ',', ' ') + ' ' + CURRENCY_SYMBOL;
}

function percent(number, decimals)
{
    return number_format(number, decimals != null ? decimals : 2, ',', '') + '%'
}

function pluralization(n, case1, case2, case3)
{
    if (n == 1) {
        return case1;
    }
    if (case3 == null || (!between(n, 10, 20) && between(n % 10, 2, 4))) {
        return case2;
    }
    return case3;
}

function between(n, min, max)
{
    return n >= min && n <= max;
}

function time_format(s) {
	var m = h = 0;
	if (s > 59) {
		m = Math.floor(s / 60);
		s = s - m * 60;
	}
	if (m > 59) {
		h = Math.floor(m / 60);
		m = m - h * 60;
	}
	return (h > 0 ? (h < 10 ? '0' + h : h) + ':' : '') + (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
}

function jDialog(message, options)
{
    var defaults = {
        modal : true,
        draggable : true,
        resizable : false,
        width : 600
    };

    if (options.open == null) {
        defaults.open = function () {
            jQuery(this).html(message);
        }
    }

    options = jQuery.extend(true, defaults, options);

    if (jQuery('#dialog').size() == 0) {
        jQuery('<div id="dialog"></div>').appendTo('body');
    }

    var dialog = jQuery('#dialog').dialog(options);
    if (options['autoOpen']) {
        dialog.dialog("open");
    }
    return dialog;
}

$(document).ready(function(){
    $("form").validationEngine();

    $("#user-info, #admin-toolbox").click(function(){
        $(this).find(".dropdown-box").toggle();
        return false;
    });

    $("body").click(function(){
        $(".dropdown-box").hide();
    });

    $(".dropdown-box").click(function(e){
        $(this).hide();
        e.stopPropagation();
    });

    $.extend($.ui.dialog.prototype.options, {
        modal : true,
        width : "auto"
    });

    $(".ui-dialog-content").live("dialogclose", function(e){
        $(this).find("form").validationEngine("hide");
    });

    $(".messages").css("left", ($(window).width() - $(".messages").width()) / 2)
                  .show();

    $("body").one("click", function(e){
        $(".messages").slideUp();
    });

    $(".button").button();

    $("a[title], li[title], span.r[title]").tooltip({
        showURL : false
    });

    var loading = $(".loading").hide();

    $.ajaxSetup({
        beforeSend : function(jqXHR){
            loading.show();
        },
        complete : function(jqXHR, textStatus){
            loading.hide();
        }
    });

    $('a[rel^="prettyPhoto"]').prettyPhoto({
        modal : true,
        social_tools : false,
        default_width : 854,
        default_height : 480
    });

    $('.admin_param_tabs_menu').delegate('li','click',function(){
	$('.admin_param_tabs_menu .admin_param_tabs_menu_active').removeClass('admin_param_tabs_menu_active');
	$(this).addClass('admin_param_tabs_menu_active');
	$('.admin_param_tabs_menu_item').hide();
	$('#'+$(this).attr('rel')).show();
    });

    $('.admin_param_tabs_menu li').eq(0).click();



});