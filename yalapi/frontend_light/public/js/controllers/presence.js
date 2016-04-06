/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
$(function()
{
	$('#presence-form td input[type=checkbox]').change(function()
	{
		$('#presence-form').submit();
	});
	$('#presence-form th input[type=checkbox]').change(function()
	{
		var index = $(this).parent().index();
		var checked = $(this).is(':checked');
		var subject = $('#presence-form tbody td:nth-child(' + (index + 1).toString() + ') input[type=checkbox]');
		subject.attr('checked', checked);
		$('#presence-form').submit();
	});

	$('#content .op select').change(function()
	{
		$(this).parents('form').submit();
	});
	$('#content .op form').submit(function()
	{
		var url = $('#base-url').text() + '/' + $('#controller-name').text() + '/' + $('#action-name').text();
		var data = $(this).serializeArray();
		for (var i in data)
		{
			var key = data[i]['name'];
			var value = data[i]['value'];
			url = url + '/' + key + '/' + value;
		}
		url = url + '/';
		window.location.href = url;
		return false;
	});
	$(window).resize();
});

$(window).resize(function()
{
	var t1 = $('#presence-table1 table');
	var t2 = $('#presence-table2 table');
	for (var i = 0; i < $('td', t1).length; i ++)
	{
		var row1 = $('tr:nth-child(' + i + ')', t1);
		var row2 = $('tr:nth-child(' + i + ')', t2);
		var height1 = row1.height();
		var height2 = row2.height();
		var height = height1;
		if (height2 > height)
		{
			height = height2;
		}
		row1.css('height', height);
		row2.css('height', height);
	}
});
