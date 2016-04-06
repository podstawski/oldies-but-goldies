var baseUrl = $('body').attr('data-base-url');
function refreshList() {
	var id = $('h1').attr('data-contact-group-id');
	$.get(baseUrl + '/contacts/edit/id/' + id, [], function(response) {
		$('#menu').replaceWith($('#menu', response));
		$('table.members').replaceWith($('table.members', response));
		$('h1.title').replaceWith($('h1.title', response));
		$.getScript(baseUrl + '/js/menu.js');
	});
}

$('#members-form').live('submit', function(e) {
	e.preventDefault();
	var url = $(this).attr('action');
	var data = $(this).serialize();
	$('#member-email').val(null);
	$.post(url, data, function(response) {
		if (response['message']) {
			alert(response['message']);
		} else {
			refreshList();
		}
	});
});

$('.members .delete').live('click', function(e) {
	e.preventDefault();
	$(this).parents('tr').addClass('working');
	var url = $(this).attr('href');
	$.get(url, [], function(response) {
		if (response['message']) {
			alert(response['message']);
		} else {
			refreshList();
		}
	});
});

/* autocomplete */
$('input[name=email]').live('focus', function(e) {
	$(this).autocomplete( {
		source: function (request, response) {
			$.ajax( {
				url: baseUrl + '/index/ajax-user-autocomplete',
				dataType: 'json',
				data: {
					'filter': request.term,
				},
				success: function (data) {
					var datas = [];
					for (var i in data['folks']) {
						var email = data['folks'][i]['email'];
						datas.push({label: email, value: email});
					}
					response(datas);
				},
			} );
		},
		select: function(e, ui) {
			$('input[name=email]').val(ui.item.value).autocomplete('close');
			$('#members-form').submit();
			e.preventDefault();
			return false;
		},
		width: 300,
		max: 10,
		delay: 100,
		cacheLength: 1,
		scroll: false,
		highlight: false
	});
});
