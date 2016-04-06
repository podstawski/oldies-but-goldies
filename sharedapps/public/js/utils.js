function trim(str) {
	var out = str;
	out = out.replace(/\s+$/, '');
	out = out.replace(/^\s+/, '');
	return out;
}


function toggleDisableForm(form) {
	$('input, select, textarea, button', $(form)).each(function() {
		$(this).prop('disabled', !$(this).prop('disabled'));
	});
}
function enableForm(form) {
	$('input, select, textarea, button', $(form)).prop('disabled', false);
}
function disableForm(form) {
	$('input, select, textarea, button', $(form)).prop('disabled', true);
}


function showPopup(target) {
	target.data('orig-parent', target.parent());
	$('#popup-wrapper').append(target).show();
	$(target).css('display', 'inline-block');
	$(target).fadeIn('fast');
	$('#dim').fadeIn('fast');
}

function hidePopup(target) {
	$(target).fadeOut('fast', function() {
		$('#popup-wrapper').hide();
		target.data('orig-parent').append(target);
	});
	$('#dim').fadeOut('fast');
}

function togglePopup(target) {
	if ($(target).is(':visible')) {
		hidePopup(target);
	} else {
		showPopup(target);
	}
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

$(function() {
	$('body').append($('<div id="popup-wrapper"></div>'));
	$(document).on('click', '.confirmable', function(e) {
		if (!confirm($(this).attr('data-confirm-text'))) {
			e.preventDefault();
		}
	});
});



$(function() {
	function updateMenuHeight() {
		var width = $(document).innerWidth();
		if ($('#menu').length > 0) {
			width -=$('#menu').outerWidth();
			width -= 20;
		}

		var topHeight = $('#top').height() + 22 + 22 + 1;
		var bottomHeight = $('#footer').height() + 22 + 22 + 1;
		var height = $(window).innerHeight() - topHeight - bottomHeight;
		$('#content').css('width', width + 'px');
		$('#content').css('height', height + 'px');

		if ($('#menu').length > 0) {
			var height = $('#content').height() - ($('#menu>.left_btns').outerHeight() + 30);
			$('#menu .scrollable').css('height', height + 'px');
		}
	}

	$(window).resize(function() { window.setTimeout(updateMenuHeight, 100); });

	updateMenuHeight();

	var baseUrl = $('body').attr('data-base-url');
	$('#fake-user-btn').click(function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		var data = {};
		$.get(baseUrl + '/index/translations', function(translations) {
			var email = prompt(translations['auth_fake_user_prompt']);
			if (email != null && email != '') {
				data['user-email'] = email;
				$.get(url, data, function(response) {
					if (!response) {
						alert(translations['auth_wrong_user_error']);
					} else {
						location.reload();
					}
				});
			}
		});
	});
});
