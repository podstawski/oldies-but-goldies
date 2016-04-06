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


/* gify nie chciały się animować na firefoxie przy wychodzeniu ze strony */
function animateThrobber() {
	var frame = $('#throbber').data('frame');
	frame += 1;
	if (frame > 17) {
		frame = 0;
	}
	$('#throbber').data('frame', frame);
	var x = -64 * frame;
	var y = 0;
	$('#throbber div').css('background-position', x + 'px ' + y + 'px');
}

function showThrobber() {
	$('#throbber').data('frame', 0);
	var interval = window.setInterval(animateThrobber, 1000 / 20);
	$('#throbber').data('interval', interval);
	$('#throbber').show();
}
function hideThrobber() {
	var interval = $('#throbber').data('interval');
	window.clearTimeout(interval);
	$('#throbber').data('interval', null);
	$('#throbber').hide();
}


$(function()
{
	//fix dla chrome - załaduj obrazek zaraz po pokazaniu strony
	var preload = $('<div class="preload">preload</div>');
	preload.css('background-image', $('#throbber div').css('background-image'));
	preload.css('width', 0);
	preload.css('height', 0);
	$('#footer').append(preload);
	window.setTimeout(function() {
		$('.preload').remove();
	}, 100);

	if ($('.mainscreen_points')){
		$('.mainscreen_points .m_points_1').fadeIn('slow', function(){
			$('.mainscreen_points .m_points_2').fadeIn('slow', function(){
				$('.mainscreen_points .m_points_3').fadeIn('slow', function(){
					$('.mainscreen_points .m_points_4').fadeIn('slow');
				});
			});
		});
	}

	/*if ($.cookie('timezone_offset') == null)*/ {
		$.cookie('timezone_offset', (new Date).getTimezoneOffset() * -60);
	}

	$('form.throbberable').submit(showThrobber);
	$(document).on('click', 'a.throbberable', showThrobber);

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
