$('.active-link .expand').unbind('click').click(function(e) {
	$('.left-links').toggle();
});
var cls = $('body').attr('data-controller');
var target = $('.left-links li.' + cls + ' a');
if (target.length == 0) {
	target = $('.left-links li:first-child a');
}
$('.active-link span').text(target.text());

$('.sub li').addClass('subject');
$('.sub li').each(function() {
	var descendants = [];
	var li = $(this);
	var ul = li.find('ul').first();
	//todo: klikanie w zagniezdzenia > 1
	if (ul.length > 0) {
		var link = $('<a class="expand"/>');
		ul.bind('change', function() {
			if (ul.hasClass('collapsed')) {
				link.html('<i class="icon-chevron-right"></i>');
			} else {
				link.html('<i class="icon-chevron-down"></i>');
			}
			var collapse = ul.hasClass('collapsed');
			if (collapse) {
				ul.hide();
			} else {
				ul.show();
			}
		}).trigger('change');
		link.click(function() {
			var baseUrl = $('body').attr('data-base-url');
			var url = baseUrl + '/index/save-expanded/';
			var data = {'key': ul.attr('data-id'), 'state': ul.hasClass('collapsed') ? 1 : 0};
			var link = $(this);
			$.get(url, data, function(response) {
				ul.toggleClass('collapsed');
				ul.trigger('change');
			});
		});
		li.addClass('expandable').prepend(link);
	}
});
