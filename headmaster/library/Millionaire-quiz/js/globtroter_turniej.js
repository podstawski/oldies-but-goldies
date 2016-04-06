$(document).ready(function () {
    odliczanieCzasu();
	$('div.cell').jScrollPane({
		verticalDragMinHeight: 10,
		verticalDragMaxHeight: 10,
		horizontalDragMinWidth: 10,
		horizontalDragMaxWidth: 10
	});
    $('#warningOverlay>div').click(function () {
        $('#warningOverlay').addClass('hidden');
    });
    $('#odpowiedzi li div.clickRegion').click(function () {
		var answerbutton = $(this).parent('li');
        $(answerbutton).addClass('odpowiedzActive');
        var clicked_answer = $(this).attr('rel');
        var elem = $(this).closest('.item');
        $.myConfirm({
            'title' : 'Czy jesteś pewien?',
            'buttons' : {
                'Tak' : {
                    'class' : 'tak',
                    'action' : function () {
                        $.ajax({
                            'type' : "POST",
                            'url' : BASE_URL + '/gra/answer',
                            'data' : {
                                'answer' : clicked_answer,
                                'question_hash' : CURRENT_QUESTION,
                                'question_id' : CURRENT_QUESTION_MD5_ID
                            },
                            'dataType' : 'json',
                            'beforeSend' : function() {
                                $.myConfirm({
                                    'title' : 'Proszę czekać...',
                                    'message' : 'Weryfikowanie odpowiedzi...'
                                });
                            },
                            'success' : function (json) {
                                if (json.is_correct == 1) {
                                    var odpowiedzi = $('#odpowiedzi li');
                                    odpowiedzi.each(function (index, item) {
                                        if ($(item).hasClass('odpowiedzActive')) {
                                            $(item).removeClass('odpowiedzActive');
                                            $(item).addClass('correctAnswer');
                                        } else if (!$(item).hasClass('odpowiedzActive')) {
                                            $(item).animate({'opacity' : 0.5});
                                        }
                                    });
                                    $.myConfirm({
                                        'title' : 'Brawo, odpowiedź prawidłowa!',
                                        'message' : 'Kliknij tutaj by wybrać nastepne pytanie.',
                                        'action' : function () {
                                            window.location.href = BASE_URL + '/startuj-w-turnieju'
                                        }
                                    });
                                } else if(json.is_correct == null && json.error != null) {
									alert(json.error);
									window.location.href = BASE_URL + '/startuj-w-turnieju';
                                } else {
                                    var odpowiedzi = $('#odpowiedzi li');
                                    odpowiedzi.each(function (index, item) {
                                        if ($(item).attr('rel') == json.correct_answer) {
                                            if ($(item).hasClass('odpowiedzActive')) $(item).removeClass('odpowiedzActive');
                                            $(item).addClass('correctAnswer');
                                        } else if (!$(item).hasClass('odpowiedzActive')) {
                                            $(item).animate({'opacity' : 0.5});
                                        }
                                    });
                                    $.myConfirm({
                                        'title' : 'Odpowiedź błędna!',
                                        'message' : 'Kliknij tutaj by wybrać nastepne pytanie.',
                                        'action' : function () {
                                            window.location.href = BASE_URL + '/startuj-w-turnieju'
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                'Nie' : {
                    'class' : 'nie',
                    'action' : function () {
                        $(answerbutton).removeClass('odpowiedzActive');
                    }
                }
            }
        });
    });
    $('ul#helpers li').click(function () {
		if($(this).hasClass('active')||$(this).hasClass('active2')) {
        var helperid = $(this).attr('id');
        $('#' + helperid).removeClass('active');
        $('#' + helperid).addClass('inactive');
        if (helperid == 'helper1') {
            $.ajax({
                'type' : "POST",
                'url' : BASE_URL + '/gra/new-question',
                'data' : {
                    'new_cat' : 0,
                    'question_hash' : CURRENT_QUESTION,
                    'question_id' : CURRENT_QUESTION_MD5_ID
                },
                'dataType' : 'json',
                'success' : function (json) {
                    if (json.error != null && json.error > 0) {
                        $.myConfirm({
                            'title' : 'Niestety!',
                            'message' : 'Nie udało się wylosować nowego pytania.'
                        });
                    } else if (json.id != null && json.hash != null) {
                        $.myConfirm({
                            'title' : 'Wylosowano nowe pytanie!',
                            'message' : 'Kliknij tutaj by je wyświetlić.',
                            'action' : function () {
                                window.location.href = BASE_URL + '/turniej/' + json.id + '/' + json.hash;
                            }
                        });
                    }
                }
            });
        }
        ;
        if (helperid == 'helper2') {
            $.ajax({
                'type' : "POST",
                'url' : BASE_URL + '/gra/new-question',
                'data' : {
                    'new_cat' : 1,
                    'question_hash' : CURRENT_QUESTION,
                    'question_id' : CURRENT_QUESTION_MD5_ID
                },
                'dataType' : 'json',
                'success' : function (json) {
                    if (json.error != null && json.error > 0) {
                        $.myConfirm({
                            'title' : 'Niestety!',
                            'message' : 'Nie udało się wylosować nowego pytania.'
                        });
                    } else if (json.id != null && json.hash != null) {
                        $.myConfirm({
                            'title' : 'Wylosowano nowe pytanie!',
                            'message' : 'Kliknij tutaj by je wyświetlić.',
                            'action' : function () {
                                window.location.href = BASE_URL + '/turniej/' + json.id + '/' + json.hash;
                            }
                        });
                    }
                }
            });
        }
        ;
        if (helperid == 'helper3') {
            var answers = [
                $('#odpowiedz1 p').attr('rel'),
                $('#odpowiedz2 p').attr('rel'),
                $('#odpowiedz3 p').attr('rel'),
                $('#odpowiedz4 p').attr('rel')
            ];
            $.ajax({
                'type' : "POST",
                'url' : BASE_URL + '/gra/lifebuoy',
                'data' : {
                    'answers' : answers,
                    'question' : CURRENT_QUESTION,
                    'helperid' : helperid
                },
                'dataType' : 'json',
                'success' : function (json) {
                    for (x in json) {
                        $('#odpowiedzi li.odpowiedz').each(function () {
                            var odpowiedz = $(this);
                            if ($(odpowiedz).attr('rel') == json[x]) {
                                $(odpowiedz).fadeOut();
                            }
                        });
                    }
                }
            });
        }
		}		
        return false;
    });
});
