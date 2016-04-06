$('.popup .title').append($('<a href="#" class="close">&times;</a>'));
$('.popup .cancel.btn, .popup .title .close').click(function(e) {
	e.preventDefault();
	hidePopup($(this).parents('.popup'));
});

