var baseUrl = $('body').attr('data-base-url');
$.get(baseUrl + '/index/translations', function(translations) {
	$(document).on('click', '#rename-trigger', function(e) {
		e.preventDefault();
		showPopup($('#rename-popup'));
		$('#folder-name').focus();
	});

	$(document).on('submit', '#rename-popup form', function(e) {
		e.preventDefault();
		if (trim($('#folder-name').val()) == '') {
			alert(translations['common_error_no_folder_specified']);
			$('#folder-name').focus();
			return;
		}

		var url = $(this).attr('action');
		var data = $(this).serialize();
		disableForm($('#rename-popup form'));
		$.post(url, data, function(response) {
			enableForm($('#rename-popup form'));
			hidePopup($('#rename-popup'));
			refreshList();
			if (response['message']) {
				alert(response['message']);
			}
		});
	});
});
