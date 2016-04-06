$.myConfirm = function (params) {
    var overlay = $('#confirmOverlay');
    if (overlay.length == 0) {
        overlay = $('<div id="confirmOverlay">'
            + '<div>'
            + '<h2 id="confirmTitle"></h2>'
            + '<p id="confirmMessage"></p>'
            + '<div id="confirmButtons"></div>'
            + '</div>'
        ).hide().appendTo('body');
    }

    $('#confirmTitle, #confirmMessage, #confirmButtons').empty();

    if (typeof params['title'] === 'string') {
        $('#confirmTitle').html(params['title']);
    }

    if (typeof params['message'] === 'string') {
        $('#confirmMessage').html(params['message']);
    }

    overlay.find('> div').attr('id', typeof params['id'] === 'string' ? params['id'] : 'confirmBox');

	var bodyHeight = $('body').height();
	var windowHeight = $(window).height();
	var scroll = $(window).scrollTop();
	overlay.css({'height':bodyHeight});
	$('#confirmBox').css({
		'top' : windowHeight/2-50+scroll
	});

    if (typeof params['icon'] !== 'undefined') {
		var icon = $('<div>' + name + '</div>').addClass('confirmBoxIcon').attr('id','confirmBoxIcon_'+params['icon']);
		icon.appendTo('#confirmBox');
		$('#confirmMessage').addClass('confirmMessage_joker');
	} 
    if (typeof params['buttons'] !== 'undefined') {
        $.each(params['buttons'], function (name, options) {
            var button = $('<div>' + name + '</div>').addClass('button');
            if (typeof options['class'] === 'string') {
                button.addClass(options['class']);
            }
            button.bind('click', function () {
                if (typeof options['action'] === 'function') {
                    options['action']();
                }
                overlay.hide();
                return false;
            }).appendTo('#confirmButtons');
        });
    } else {
        overlay.one('click', function (e) {
            if (typeof params['action'] === 'function') {
                params['action']();
            }
            overlay.hide();
            return false;
        });
    }

    overlay.show();
}

$.youTube = function (params) {
    $.myConfirm({
        'message' : '<iframe width="800" height="464" src="http://www.youtube.com/embed/'+params.movie+'" frameborder="0" allowfullscreen></iframe>',
        'id' : 'confirmYT'
    });
}

function myConfirmWrapper(url) {
    if (CASH != null) {
        if (CASH == 0 || (CASH - PRICE) < 0) {
            if (BYFEET == true) {
                $.myConfirm({
                    'title' : 'Nie masz na koncie wystarczającej liczby Koziołków i musisz podróżować pieszo.',
                    'buttons' : {
                        'idź pieszo' : {
                            'class' : 'onfoot',
                            'action' : function () {
                                window.location.href = BASE_URL + '/dalsza-podroz/region/' + url;
                            }
                        }
                    }
                });
            } else {
                $.myConfirm({
                    'title' : 'Podróżowałeś już dzisiaj pieszo. Następna pieszą podróż będzież mógł odbyć jutro.'
                });
            }
        } else {
            $.myConfirm({
                'title' : 'Podróż tutaj będzie kosztowała ' + PRICE + ' koziołków.',
                'message' : 'Kontynuować podróż?',
                'buttons' : {
                    'Tak' : {
                        'class' : 'tak',
                        'action' : function () {
                            window.location.href = BASE_URL + '/dalsza-podroz/region/' + url;
                        }
                    },
                    'Nie' : {
                        'class' : 'nie'
                    }
                }
            });
        }
    }
}

jQuery.fn.countDown = function (settings, to) {
    settings = jQuery.extend({
        duration : 1000,
        startNumber : START_NUMBER,
        endNumber : 0,
        callBack : function () {
        }
    }, settings);
    return this.each(function () {
        if (!to && to != settings.endNumber) {
            to = settings.startNumber;
        }
        // zlicznie w dół
        $(this).text(to);
        timer = to;
        // zapętlenie
        $(this).animate({'color' : $(this).css('color')}, settings.duration, '', function () {
            if (to > settings.endNumber + 1) {
                $('#formTime').val(to);
                currentTime = to - 1;
                $(this).text(currentTime).countDown(settings, to - 1);
                var animateTo = Math.floor((to - 1) * (198 / DEFAULT_START_NUMBER));
                $('div.pytanie div.progress div.progress_active').animate({'width' : animateTo});
                $.cookie("startNumber_" + CURRENT_QUESTION, to, { expires : 7 });
            }
            else {
                settings.callBack(this);
            }
        });
    });
};

function odliczanieCzasu() {
    // odliczanie czasu
    $('div.clock').countDown({
        callBack : function (me) {
            $('div.czas>div').animate({
                'opacity' : 0
            });
            $.ajax({
                'type' : "POST",
                'url' : BASE_URL + '/gra/time-out/',
                'data' : {
                    'question_hash' : CURRENT_QUESTION,
                    'question_id' : CURRENT_QUESTION_MD5_ID
                },
                'dataType' : 'json',
                'success' : function (json) {
                    $.myConfirm({
                        'title' : 'Czas minął!',
                        'message' : 'Kliknij tutaj, by wybrać nastepne pytanie.',
                        'action' : function () {
                            window.location.href = BASE_URL + '/startuj-w-turnieju'
                        }
                    });
                }
            });
        }
    });
}
