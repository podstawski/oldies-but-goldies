gameData = $.parseJSON(gameData);

if (gameData.timeStarted > 0) {
	var startNumber = gameData.time - ((Math.floor((new Date()).getTime() / 1000)) - gameData.timeStarted);
	var timeStarted = gameData.timeStarted;
} else {
	var startNumber = gameData.time;
	gameData.timeLeft = gameData.time;
	var timeStarted = Math.floor((new Date()).getTime() / 1000);
	gameData.timeStarted = timeStarted;
}

function cut(str, cutStart, cutEnd){
  return str.substr(0,cutStart) + str.substr(cutEnd+1);
}

function showPoints(points) {
	var lifeBuoysPoints = 0;
	if ($('#lifeBuoys1 a').hasClass('on')) lifeBuoysPoints = lifeBuoysPoints + 5;
	if ($('#lifeBuoys2 a').hasClass('on')) lifeBuoysPoints = lifeBuoysPoints + 5;
	if ($('#lifeBuoys3 a').hasClass('on')) lifeBuoysPoints = lifeBuoysPoints + 5;
	if (lifeBuoysPoints > 0) {
		$('span.lifeBuoysPointsBlock').show();
		$('span.lifeBuoysPoints').html(lifeBuoysPoints);
	} else {
		$('span.lifeBuoysPointsBlock').hide();
		$('span.lifeBuoysPoints').html(lifeBuoysPoints);
	}
	$('span.koniecGryPunkty').html((points * 10) + lifeBuoysPoints);
	$('#testPoints ul li').removeClass('on');
	for (i = points; i > 0; i = i - 1) {
		x = 11 - i;
		$('#testPoints ul li:nth-child(' + x + ')').addClass('on');
	}
}

function secondsToTime(secs) {
	var hours = Math.floor(secs / (60 * 60));
	var divisor_for_minutes = secs % (60 * 60);
	var minutes = Math.floor(divisor_for_minutes / 60);
	var divisor_for_seconds = divisor_for_minutes % 60;
	var seconds = Math.ceil(divisor_for_seconds);
	var ret = '';
	if (minutes > 0) ret = minutes + ' min';
	if (seconds > 0) ret = ret + ' ' + seconds + ' sek';
	return ret;
}

function isset() {
	// !No description available for isset. @php.js developers: Please update the function summary text file.
	//
	// version: 1103.1210
	// discuss at: http://phpjs.org/functions/isset
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: FremyCompany
	// +   improved by: Onno Marsman
	// +   improved by: Rafał Kukawski
	// *	 example 1: isset( undefined, true);
	// *	 returns 1: false
	// *	 example 2: isset( 'Kevin van Zonneveld' );
	// *	 returns 2: true
	var a = arguments,
		l = a.length,
		i = 0,
		undef;

	if (l === 0) {
		throw new Error('Empty isset');
	}

	while (i !== l) {
		if (a[i] === undef || a[i] === null) {
			return false;
		}
		i++;
	}
	return true;
}

function randomFromTo(from, to) {
	return Math.floor(Math.random() * (to - from + 1) + from);
}

function fontSize() {
	$('ul#testAnswers li').each(function () {
		var element = $(this).children('a').children('span');
		$(element).css({
			'lineHeight' : '',
			'fontSize' : '',
		});
		var lines = $(element).height();
		lines = lines / 15;
		if (lines > 1) {
			var height = 53 / lines;
			if (lines > 2) {
				$(element).css({
					'fontSize' : '13px'
				});
			}
			if (lines > 3) {
				$(element).css({
					'fontSize' : '12px'
				});
			}
			if (lines > 4) {
				$(element).css({
					'fontSize' : '10px'
				});
			}
			$(element).css({
				'lineHeight' : height + 'px'
			});
		} else {
			$(element).css({
				'lineHeight' : '53px'
			});
		}
		if ($(element).height() < 53) {
			var newLines = $(element).height() / height;
			var newHeight = 53 / newLines;
			$(element).css({
				'lineHeight' : newHeight + 'px'
			});
		}
	});
}

function fontSizeElement(parent) {
	var element = $(parent).children('span');
	$(element).css({
		'lineHeight' : '',
		'fontSize' : ''
	});
	var lines = $(element).height();
	lines = lines / 15;
	if (lines > 1) {
		var height = 53 / lines;
		if (lines > 2) {
			$(element).css({
				'fontSize' : '13px'
			});
		}
		if (lines > 3) {
			$(element).css({
				'fontSize' : '12px'
			});
		}
		if (lines > 4) {
			$(element).css({
				'fontSize' : '10px'
			});
		}
		$(element).css({
			'lineHeight' : height + 'px'
		});
		if ($(element).height() < 53) {
			var newLines = $(element).height() / height;
			var newHeight = 53 / newLines;
			$(element).css({
				'lineHeight' : newHeight + 'px'
			});
		}
	} else {
		$(element).css({
			'lineHeight' : '53px'
		});
	}
}

function odliczanieCzasu() {
	// odliczanie czasu
	if (gameData.time > 0) {
		// start
		$('#timeLeft').countDown({
			callBack : function (me) {
				$('#testTimer h2').fadeOut(
					'slow',
					function () {
						if (CAN_I_COUNT) {
							$(this).text(TXT_TIME_IS_UP).css('color', '#FF3300').css('fontWeight', 'bold').fadeIn();
							$.post(BASE_URL + '/gra/ajax-time-left/', {
								'sessionHash' : gameData.sessionHash,
								'testPass' : gameData.testPass,
								'timeFinished' : gameData.currentTime,
								'timeLeft' : 0,
								'status' : 0
							}, function (json) {
								showPreloader(true);
								$('#czasMinal').fadeIn();
								if (CAN_I_SOUND) {
									$("#sound_tictac").jPlayer('pause');
									if(TICTAC_ALWAYS == 1) {
										$("#sound_tictac").jPlayer('play');
									}
								} else {
									$("#sound_tictac").jPlayer('pause');
								}
							}, 'json');
						}
					}
				);
			}
		});
	}
}

function zliczPunktyZaKola() {
}

function sprawdzIloscCzyKoniec() {
	if (gameData.step >= 10) {
		$.post(BASE_URL + '/gra/ajax-time-left/', {
			'sessionHash' : gameData.sessionHash,
			'testPass' : gameData.testPass,
			'timeFinished' : gameData.currentTime,
			'timeLeft' : 0,
			'status' : 0
		}, function (json) {
			czas = $('#timeLeft').text();
			$('#timeLeft').remove()
			$('#testTimer h2').append('<div id="timeStopped">' + czas + '</div>');
			if (CAN_I_SOUND) {
				$("#sound_tictac").jPlayer('pause');
			}
			zliczPunktyZaKola();
			$('#koniecGryFull').fadeIn();
		}, 'json');
	}
}

function ukryjKolaRatunkowe() {
	if (gameData.lifeBuoys[1] == 1) $('#lifeBuoys1 a').removeClass('on');
	if (gameData.lifeBuoys[2] == 1) $('#lifeBuoys2 a').removeClass('on');
	if (gameData.lifeBuoys[3] == 1) $('#lifeBuoys3 a').removeClass('on');
}

function groupModeRefresh() {
	if (gameData.modePlayers == 2) {
		$('#groupMode').removeClass('hidden');
		$.post(BASE_URL + '/gra/ajax-get-groupmode-data/', {
			'testPass' : gameData.testPass
		}, function (json) {
			$('.groupModeItem').removeClass('you');
			for (var i in json) {
				i = parseInt(i);
				var player_lifebuoys = 15;
				var player_steps = 0;
				for (a = 1; a < 4; a = a + 1) {
					if (isset(json[i].lifebuoys[a])) {
						$('#groupModeItem_' + (i + 1) + ' ul.lifeBuoys>li:nth-child(' + a + ')').css('opacity', 1);
						player_lifebuoys = player_lifebuoys - 5;
					}
				}
				y = json[i].step + 1;
				for (z = y; z > 0; z = z - 1) {
					x = 11 - z;
					// $('#groupModeItem_'+(i+1)+'>ul.points>li:nth-child('+x+')').addClass('on');
				}
				for (a = 0; a < json[i].step; a = a + 1) {
					x = 10 - a;
					if (json[i].answers[a].correct == 0) {
						$('#groupModeItem_' + (i + 1) + '>ul.points>li:nth-child(' + x + ')').addClass('on').addClass('wrong');
						player_steps = player_steps + 1;
					}
					if (json[i].answers[a].correct == 1) {
						$('#groupModeItem_' + (i + 1) + '>ul.points>li:nth-child(' + x + ')').addClass('on').addClass('correct');
						player_steps = player_steps + 1;
					}
				}
				if (player_steps < 10) player_lifebuoys = 0;
				$('#groupModeItem_' + (i + 1)).fadeIn();
				$('#groupModeItem_' + (i + 1) + ' h2').text(json[i].nick);
				$('#groupModeItem_' + (i + 1) + ' p').text((json[i].points + player_lifebuoys) + ' ' + TXT_POINTS);
				$('#groupModeItem_' + (i + 1) + ' ul li').removeClass('on');
				if (json[i].nick == gameData.nick) {
					$('#groupModeItem_' + (i + 1)).addClass('you');
				}
			}
		}, 'json');
	}
}

function groupModeRefreshLoop() {
	$('#groupMode').removeClass('hidden');
	$.ajax({
		'url' : BASE_URL + '/gra/ajax-get-groupmode-data/',
		'async' : false,
		'data' : {'testPass' : gameData.testPass},
		'type' : 'POST',
		'dataType' : 'json'
	}).done(function (json) {
			$('.groupModeItem').removeClass('you');
			for (var i in json) {
				i = parseInt(i);
				var player_lifebuoys = 15;
				var player_steps = 0;
				for (a = 1; a < 4; a = a + 1) {
					if (isset(json[i].lifebuoys[a])) {
						$('#groupModeItem_' + (i + 1) + ' ul.lifeBuoys>li:nth-child(' + a + ')').css('opacity', 1);
						player_lifebuoys = player_lifebuoys - 5;
					}
				}
				y = json[i].step + 1;
				for (z = y; z > 0; z = z - 1) {
					x = 11 - z;
					// $('#groupModeItem_'+(i+1)+'>ul.points>li:nth-child('+x+')').addClass('on');
				}
				for (a = 0; a < json[i].step; a = a + 1) {
					x = 10 - a;
					if (json[i].answers[a].correct == 0) {
						$('#groupModeItem_' + (i + 1) + '>ul.points>li:nth-child(' + x + ')').addClass('on').addClass('wrong');
						player_steps = player_steps + 1;
					}
					if (json[i].answers[a].correct == 1) {
						$('#groupModeItem_' + (i + 1) + '>ul.points>li:nth-child(' + x + ')').addClass('on').addClass('correct');
						player_steps = player_steps + 1;
					}
				}
				if (player_steps < 10) player_lifebuoys = 0;
				$('#groupModeItem_' + (i + 1)).fadeIn();
				$('#groupModeItem_' + (i + 1) + ' h2').text(json[i].nick);
				$('#groupModeItem_' + (i + 1) + ' p').text((json[i].points + player_lifebuoys) + ' ' + TXT_POINTS);
				$('#groupModeItem_' + (i + 1) + ' ul li').removeClass('on');
				if (json[i].nick == gameData.nick) {
					$('#groupModeItem_' + (i + 1)).addClass('you');
				}
				groupModeRefreshLoop();
			}
		});
}

function showPreloader($all) {
	if ($all) {
		$('#dialogBoxOverlay').fadeIn(function () {
			$('#preloader').fadeIn();
		});
	} else {
		$('#dialogBoxOverlay').fadeIn();
	}
}

function hidePreloader($all) {
	$('#preloader').fadeOut(function () {
		if ($all) {
			$('#dialogBoxOverlay').fadeOut();
		}
	});
}


function toMain() {
	window.location = BASE_URL + '/';
}

function clickToRedirect() {
	$('body').append('<div id="clickToRedirect" onclick="toMain()"></div>');
}

function explodeArray(item, delimiter) {
	tempArray = new Array(1);
	var Count = 0;
	var tempString = new String(item);
	while (tempString.indexOf(delimiter) > 0) {
		tempArray[Count] = tempString.substr(0, tempString.indexOf(delimiter));
		tempString = tempString.substr(tempString.indexOf(delimiter) + 1, tempString.length - tempString.indexOf(delimiter) + 1);
		Count = Count + 1
	}
	tempArray[Count] = tempString;
	return tempArray;
}

jQuery.fn.countDown = function (settings, to) {
	settings = jQuery.extend({
		duration : 1000,
		startNumber : startNumber,
		endNumber : 0,
		callBack : function () {
		}
	}, settings);
	return this.each(function () {
		if (!to && to != settings.endNumber) {
			to = settings.startNumber;
		}
		// zlicznie w dół
		$(this).text(secondsToTime(to));
		timer = to;
		// zapętlenie
		$(this).animate({'color' : $(this).css('color')}, settings.duration, '', function () {
			if (to > settings.endNumber + 1) {
				$('#formTime').val(to);
				currentTime = secondsToTime(to - 1);
				gameData.timeLeft = to - 1;
				if (CAN_I_COUNT) $(this).text(currentTime).countDown(settings, to - 1);
				animateTo = Math.floor(((to - 1) / gameData.time) * 137) + 5;
				if (animateTo > 142) animateTo = 142;
				$('#testProgress>div').animate({'width' : animateTo});
				groupModeRefresh();
				if (gameData.timeLeft % 10 === 0) {
					$.post(BASE_URL + '/gra/ajax-time-left/', {
						'sessionHash' : gameData.sessionHash,
						'testPass' : gameData.testPass,
						'timeFinished' : gameData.currentTime,
						'timeLeft' : gameData.timeLeft
					}, function (json) {
					}, 'json');
				}
			}
			else {
				settings.callBack(this);
			}
		});
	});
};

$(document).ready(function () {

	// wykonaj funkcje sprawdzające na starcie
	// ukryjKolaRatunkowe();
	fontSize();
	groupModeRefresh();
	sprawdzIloscCzyKoniec();

	// ukryj timer jeżeli bez limitu czasu
	if (gameData.time === 0) {
		$('#testTimer').hide();
	}

	// pokaż liczbę punktów
	zliczPunktyZaKola();
	showPoints(gameData.points);
	$('#testProgress>div').animate({'width' : 142});
	$('#timeLeft').text(secondsToTime(gameData.time));
	$('#welcomeTime').text(secondsToTime(gameData.time));

	// sprawdz czy czas jest odpowiedni
	if (gameData.time > 0) {
		if (startNumber < 0) {
			$.post(BASE_URL + '/gra/ajax-new-game/', {
				'testPass' : gameData.testPass
			}, function (json) {
				window.location = BASE_URL + '/';
			}, 'json');
		}
	}

	// wyświetl powitanie
	if (gameData.timeLeft === gameData.time && gameData.step === 0) {
		showPreloader(true);
		// zapisz datę startu do sesji
		$.post(BASE_URL + '/gra/ajax-time/', {
			'sessionHash' : gameData.sessionHash,
			'testPass' : gameData.testPass,
			'timeStarted' : gameData.timeStarted,
			'timeLeft' : gameData.timeLeft,
			'currentTime' : Math.round((new Date()).getTime() / 1000)
		}, function (json) {
			hidePreloader(false);
			if (gameData.modePlayers == 1) {
				$('#welcome').fadeIn();
			} else if (gameData.modePlayers == 2) {
				$('#welcome_group').fadeIn();
			} else {
				$('#welcome').fadeIn();
			}
		}, 'json');
	} else {
		odliczanieCzasu();
	}

	// zamykanie dialogBoxow
	$('.dialogBoxWelcome').click(function () {
		$(this).fadeOut(function () {
			hidePreloader(true);
			odliczanieCzasu();
			if(CAN_I_SOUND && TICTAC_ALWAYS == 1) {				
				$("#sound_tictac").jPlayer("play");
			}
		});
	});

	$('.dialogBoxNewGame').click(function () {
		$.post(BASE_URL + '/gra/ajax-new-game/', {
			'testPass' : gameData.testPass
		}, function () {
			$('#czasMinal div').fadeOut(function () {
				hidePreloader(true);
				window.location = BASE_URL + '/';
			});
		});
	});
	$('.dialogBox').click(function () {
		$(this).fadeOut();
		hidePreloader(true);
		sprawdzIloscCzyKoniec();
		$('.currentQuestion').fadeOut(function () {
			$('#magnify_glass').remove();
			$('a.currentQuestion').each(function () {
				$(this).children('span').text($(this).next('a').children('span').text());
				$(this).attr('name', $(this).next('a').attr('name'));
			});
			$('#testIlustracja div#youtube_' + prev_question_id).remove();
			$('#testIlustracja div#youtube_' + question_id).show();
			if($('#testIlustracja a').hasClass('newQuestion') || $('#testIlustracja div').hasClass('youtube')) {
				$('#testIlustracja a.currentQuestion').remove();
			}
			$('#testIlustracja a.newQuestion img').fadeIn(function () {					
				$(this).parent('a').removeClass('newQuestion').addClass('currentQuestion');
			});
			if($('#testIlustracja a').hasClass('testIlustracjaBig')) {
				$('#testIlustracja').append('<div id="magnify_glass"></div>');
				$("a[rel^='prettyPhoto']").prettyPhoto({
					modal : true,
					social_tools : false,
					show_title: false,
					callback: function(){
					}
				});
				$('#magnify_glass').bind('click',function(){
					$.prettyPhoto.open($('.testIlustracjaBig').attr('href'));
				})
			}
			$('p.currentQuestion').text($('p.newQuestion').text());
			$('#testAnswers li').removeClass('select');
			$('#testAnswers li').removeClass('correct');
			$('ul#testAnswers li').animate({'opacity' : 1});
			$('#testText>h2>span').text(gameData.step + 1);
			zliczPunktyZaKola();
			showPoints(gameData.points);
			$(this).fadeIn(function () {
				fontSizeElement(this);
				CAN_I_CLICK = true;
			});		
		});
	});

	$('.dialogBoxLifeBuoy').click(function () {
		$(this).fadeOut();
		hidePreloader(true);
		CAN_I_CLICK = true;
	});

	function lifeBuoyShow(id) {
		// ekspert lub inne pytanie
		if (id == 'lifeBuoys1') {
			$('#box_lifeBuoys2').hide();
			$.post(BASE_URL + '/gra/ajax-life-buoy/', {
				'question_id' : question_id,
				'lifebuoy_type' : 1,
				'testPass' : gameData.testPass
			}, function (json) {
				if(json.lifebuoy != null) {
					$('#box_lifeBuoys1>div div.content').html('<p>' + json.lifebuoy + '</p>');
					$('#box_lifeBuoys1').fadeIn();
				} else if(json.question != null && json.answers != null){
					hidePreloader(true);
					CAN_I_CLICK = true;	
					$('p.newQuestion').text(json.question.question);
					prev_question_id = question_id;
					question_id = json.question.id;						
					if (json.question.media == '') {
						$.ajax({
							type : 'POST',
							dataType : 'json',
							data: {'filename':json.question.hash},
							url : BASE_URL + '/gra/image-files-exist',
							error : function () {
								$('#testIlustracja').append('<a class="newQuestion" href="#"><img src="' + BASE_URL + '/img/questions/default/' + randomFromTo(0, 38) + '.jpg" alt="' + json.question.hash + '"></a>');
								return false;
							},
							success : function (image) {
								if(image.thumbnail==1 && image.big == 1) {
									$('#testIlustracja').append('<a href="' + BASE_URL + '/uploads/' + json.question.hash + '_big.jpg" class="newQuestion testIlustracjaBig" rel="prettyPhoto"><img src="' + BASE_URL + '/uploads/' + json.question.hash + '.jpg" alt="" /></a>');
								} else if(image.thumbnail == 1) {
									$('#testIlustracja').append('<a href="#" class="newQuestion testIlustracjaBig"><img src="' + BASE_URL + '/uploads/' + json.question.hash + '.jpg" alt="" /></a>');
								} else {
									 $('#testIlustracja').append('<a class="newQuestion" href="#"><img src="' + BASE_URL + '/img/questions/default/' + randomFromTo(0, 38) + '.jpg" alt="' + json.question.hash + '"></a>');
								}
							}
						});
					} else {
						$('#testIlustracja').append('<div id="youtube_' + question_id + '" class="youtube" style="display:none;"><iframe width="560" height="348" src="http://www.youtube.com/embed/' + json.question.media + '?wmode=transparent" frameborder="0" allowfullscreen></iframe></div>')
					}
					for (var i = 0; i < 4; i++) {
						$('#testAnswers li:nth-child(' + (i + 1) + ') a').css({'fontSize' : '15px'});
						$('#testAnswers li:nth-child(' + (i + 1) + ') a.newQuestion').attr('name', json.answers[i].id);
						$('#testAnswers li:nth-child(' + (i + 1) + ') a.newQuestion span').text(json.answers[i].answer);
					}
					$('.currentQuestion').fadeOut(function () {
						$('#magnify_glass').remove();
						$('a.currentQuestion').each(function () {
							$(this).children('span').text($(this).next('a').children('span').text());
							$(this).attr('name', $(this).next('a').attr('name'));
						});
						$('#testIlustracja div#youtube_' + prev_question_id).remove();
						$('#testIlustracja div#youtube_' + question_id).show();
						if($('#testIlustracja a').hasClass('newQuestion') || $('#testIlustracja div').hasClass('youtube')) {
							$('#testIlustracja a.currentQuestion').remove();
						}
						$('#testIlustracja a.newQuestion img').fadeIn(function () {					
							$(this).parent('a').removeClass('newQuestion').addClass('currentQuestion');
						});
						if($('#testIlustracja a').hasClass('testIlustracjaBig')) {
							$('#testIlustracja').append('<div id="magnify_glass"></div>');
							$("a[rel^='prettyPhoto']").prettyPhoto({
								modal : true,
								social_tools : false,
								show_title: false,
								callback: function(){
								}
							});
							$('#magnify_glass').bind('click',function(){
								$.prettyPhoto.open($('.testIlustracjaBig').attr('href'));
							})
						}
						$('p.currentQuestion').text($('p.newQuestion').text());
						$('#testAnswers li').removeClass('select');
						$('#testAnswers li').removeClass('correct');
						$('ul#testAnswers li').animate({'opacity' : 1});
						zliczPunktyZaKola();
						showPoints(gameData.points);
						$(this).fadeIn(function () {
							fontSizeElement(this);
							CAN_I_CLICK = true;
						});		
					});
				} else {
					hidePreloader(true);
					CAN_I_CLICK = true;	
				}
			}, 'json');
		}
		if (id == 'lifeBuoys2') {
			$('#box_lifeBuoys1').hide();
			$.post(BASE_URL + '/gra/ajax-life-buoy/', {
				'question_id' : question_id,
				'lifebuoy_type' : 2,
				'testPass' : gameData.testPass
			}, function (json) {
				var ukryteOdpowiedzi = new Array();
				$('#testAnswers li').each(function () {
					ukryteOdpowiedzi.push($(this).css('opacity'));
				})
				for (var i in json) {
					if (ukryteOdpowiedzi[i] != 0) {
						$('#box_lifeBuoys2>div div.content').append('<p class="probability_' + json[i].letter + '"><strong>' + json[i].letter + ')</strong> ' + json[i].text + ': ' + json[i].probability + '%</p>');
					}
				}
				$('#box_lifeBuoys2').fadeIn();
			}, 'json');
		}
		if (id == 'lifeBuoys3') {
			$.post(BASE_URL + '/gra/ajax-life-buoy/', {
				'question_id' : question_id,
				'lifebuoy_type' : 3,
				'testPass' : gameData.testPass
			}, function (json) {
				$('ul#testAnswers li:nth-child(' + json[0] + ')').animate({'opacity' : 0});
				$('ul#testAnswers li:nth-child(' + json[1] + ')').animate({'opacity' : 0});
			}, 'json');
			hidePreloader(true);				
			CAN_I_CLICK = true;
		}
	}

	// koła ratunkowe
	$('#lifeBuoys a').click(function () {
		if (CAN_I_CLICK) {			
			if ($(this).attr('class') == 'on') {
				var id = $(this).parent('li').attr('id');
				var item = $(this);
				showPreloader(true);
				CAN_I_CLICK = false;
				$('#confirmLifeBuoy').fadeIn();
				if(CAN_I_SOUND && TICTAC_ALWAYS == 0) {
					$("#sound_tictac").jPlayer('stop');
					$("#sound_tictac").jPlayer('play');
				}
				$('#confirmLifeBuoyYes').bind("click", function () {
					$('#confirmLifeBuoy').fadeOut(function () {
						if(CAN_I_SOUND && TICTAC_ALWAYS == 0) $("#sound_tictac").jPlayer('stop');
						$(item).removeClass('on');
						lifeBuoyShow(id);
						$('#confirmLifeBuoyYes').unbind("click");
						$('#confirmLifeBuoyNo').unbind("click");				
					});
				});				
				$('#confirmLifeBuoyNo').bind("click", function () {
					$('#confirmLifeBuoy').fadeOut(function () {
						if(CAN_I_SOUND && TICTAC_ALWAYS == 0) $("#sound_tictac").jPlayer('stop');
						hidePreloader(true);
						CAN_I_CLICK = true;
						$('#confirmLifeBuoyYes').unbind("click");
						$('#confirmLifeBuoyNo').unbind("click");				
					});
				});				
			}
		}
	});

	// wybieranie odpowiedzi, zlicznie punktów i wczytywanie kolejnego pytania
	$('#testAnswers li a').click(function () {
		if (CAN_I_CLICK) {
			if(CAN_I_SOUND && TICTAC_ALWAYS == 0) {
				$("#sound_tictac").jPlayer('stop');
				$("#sound_tictac").jPlayer('play');
			}
			showPreloader(false);
			$('#testAnswers li').removeClass('select');
			$(this).parent('li').addClass('select');
			$('#confirm').fadeIn();
			SELECTED_ANSWER_ID = $(this).attr('name');
			CAN_I_CLICK = false;
			// potwierdzenie odpowiedzi
			$('#confirmYes').bind("click", function () {
				if(CAN_I_SOUND && TICTAC_ALWAYS == 0) $("#sound_tictac").jPlayer('stop');
				gameData.step = gameData.step + 1;
				$('#confirm').fadeOut();
				$('#preloader').fadeIn(function () {
					$.post(BASE_URL + '/gra/ajax/', {
						'question' : question_id,
						'answer' : SELECTED_ANSWER_ID,
						'timeStarted' : gameData.timeStarted,
						'timeLeft' : gameData.timeLeft,
						'currentTime' : Math.round((new Date()).getTime() / 1000)
					}, function (json) {
						hidePreloader(false);
						// prawidłowa odpowiedź na zielono
						if (json.correct_answer > 0) {
							$('a#testAnswer' + json.correct_answer).parent('li').addClass('correct');
						}
						;
						if (json.correct === 1) {
							gameData.points = gameData.points + 1;
							if (gameData.step >= 10) {
								CAN_I_COUNT = false;
								if (CAN_I_SOUND) {
									$("#sound_ok").jPlayer('stop');
									$("#sound_ok").jPlayer('play');
								}
								zliczPunktyZaKola();
								showPoints(gameData.points);
								$('#koniecGryPoprawna').fadeIn();
							} else {
								if (CAN_I_SOUND) {
									$("#sound_ok").jPlayer('stop');
									$("#sound_ok").jPlayer('play');
								}
								$('#poprawna').fadeIn();
							}
						}
						else {
							if (gameData.modeEnd == 1 || gameData.step >= 10) {
								$.post(BASE_URL + '/gra/ajax-time-left/', {
									'sessionHash' : gameData.sessionHash,
									'testPass' : gameData.testPass,
									'timeFinished' : gameData.currentTime,
									'timeLeft' : gameData.timeLeft,
									'status' : 0
								}, function (json) {
									CAN_I_COUNT = false;
									if (CAN_I_SOUND) {
										$("#sound_wrong").jPlayer('stop');
										$("#sound_wrong").jPlayer('play');
									}
									zliczPunktyZaKola();
									showPoints(gameData.points);
									$('#koniecGry').fadeIn();
								}, 'json');
							} else {
								if (CAN_I_SOUND) {
									$("#sound_wrong").jPlayer('stop');
									$("#sound_wrong").jPlayer('play');
								}
								$('#niePoprawna').fadeIn();
							}
						}
						// wrzuć tekst nowego pytania i nowe odpowiedzi
						$('p.newQuestion').text(json.question.question);
						prev_question_id = question_id;
						question_id = json.question.id;						
						if (json.question.media == '') {
							$.ajax({
								type : 'POST',
								dataType : 'json',
								data: {'filename':json.question.hash},
								url : BASE_URL + '/gra/image-files-exist',
								error : function () {
									$('#testIlustracja').append('<a class="newQuestion" href="#"><img src="' + BASE_URL + '/img/questions/default/' + randomFromTo(0, 38) + '.jpg" alt="' + json.question.hash + '"></a>');
									return false;
								},
								success : function (image) {
									if(image.thumbnail==1 && image.big == 1) {
										$('#testIlustracja').append('<a href="' + BASE_URL + '/uploads/' + json.question.hash + '_big.jpg" class="newQuestion testIlustracjaBig" rel="prettyPhoto"><img src="' + BASE_URL + '/uploads/' + json.question.hash + '.jpg" alt="" /></a>');
									} else if(image.thumbnail == 1) {
										$('#testIlustracja').append('<a href="#" class="newQuestion testIlustracjaBig"><img src="' + BASE_URL + '/uploads/' + json.question.hash + '.jpg" alt="" /></a>');
									} else {
										 $('#testIlustracja').append('<a class="newQuestion" href="#"><img src="' + BASE_URL + '/img/questions/default/' + randomFromTo(0, 38) + '.jpg" alt="' + json.question.hash + '"></a>');
									}
								}
							});
						} else {
							$('#testIlustracja').append('<div id="youtube_' + question_id + '" class="youtube" style="display:none;"><iframe width="560" height="348" src="http://www.youtube.com/embed/' + json.question.media + '?wmode=transparent" frameborder="0" allowfullscreen></iframe></div>')
						}
						for (var i = 0; i < 4; i++) {
							$('#testAnswers li:nth-child(' + (i + 1) + ') a').css({'fontSize' : '15px'});
							$('#testAnswers li:nth-child(' + (i + 1) + ') a.newQuestion').attr('name', json.answers[i].id);
							$('#testAnswers li:nth-child(' + (i + 1) + ') a.newQuestion span').text(json.answers[i].answer);
						}
					}, 'json');
				});
				$('#confirmYes').unbind("click");				
				$('#confirmNo').unbind("click");				
			});
			$('#confirmNo').bind("click", function () {
				$('#confirm').fadeOut(function () {
					if(CAN_I_SOUND && TICTAC_ALWAYS == 0) $("#sound_tictac").jPlayer('stop');
					hidePreloader(true);
					SELECTED_ANSWER_ID = 0;
					CAN_I_CLICK = true;
					$('#testAnswers li').removeClass('select');
					$('#confirmYes').unbind("click");				
					$('#confirmNo').unbind("click");				
				});
			});
		}
	});
	$('#soundSwitch').click(function () {
		if (CAN_I_SOUND) {
			CAN_I_SOUND = false;
			if(TICTAC_ALWAYS == 1) {
				$("#sound_tictac").jPlayer('pause');
			}
			$(this).children('div').addClass('off');
			$.cookie("CAN_I_SOUND", 'false', { expires : 7 });
		} else {
			CAN_I_SOUND = true;
			if(TICTAC_ALWAYS == 1) {
				$("#sound_tictac").jPlayer('play');
			}
			$(this).children('div').removeClass('off');
			$.cookie("CAN_I_SOUND", 'true', { expires : 7 });
		}
	});
	$('p.flagLink').click(function () {
		$('div#flag').fadeIn();
		return false;
	})
	$('p.stayLink').click(function () {
		hidePreloader(true);
		return false;
	})
	$('#flagCancel').click(function () {
		$('div#flag').fadeOut();
		$('div#flag textarea').val('');
	});
	$('#flagSend').click(function () {
		$('.flagBox').fadeOut(function () {
			$.post(BASE_URL + '/gra/ajax-flag/', {
				'id' : prev_question_id,
				'comment' : $('div#flag textarea').val(),
			}, function (json) {
				alert(TXT_FLAGSEND);
				$('div#flag textarea').val('');
				$('div#flag').fadeOut();
			}, 'json');
		});
	})
	$('.stayLink').click(function () {
		$.post(BASE_URL + '/gra/ajax-new-game/', {
			'testPass' : gameData.testPass
		}, function () {
			$('#czasMinal div').fadeOut(function () {
				CAN_I_CLICK = false;
				hidePreloader(true);
				$('#testTimer h2').fadeOut();
				$('#testProgress').fadeOut();
				$('#testText p').fadeOut();
				$('#testAnswers li a').fadeOut();
				$("#sound_tictac").jPlayer('pause');
				$('#soundSwitch').children('div').addClass('off');
				groupModeRefreshLoop();
				clickToRedirect();
			});
		});
		return false;
	});
});
