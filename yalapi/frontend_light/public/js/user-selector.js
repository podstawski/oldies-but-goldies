/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
$(function()
{
	//stwórz selectom odpowiednie opcje
	//@param p = div-właściciel
	function updateUsers(p)
	{
		var select = p.data('select').empty();
		var users = p.data('users');
		var id = p.data('id');
		var nothingSelected = true;
		for (var i in users)
		{
			var user = users[i];
			if (user['id'] == id)
			{
				nothingSelected = false;
			}
		}
		for (var i in users)
		{
			var user = users[i];
			var option = $('<option/>');
			option.text(user['firstName'] + ' ' + user['lastName']);
			option.data('id', user['id']);
			option.attr('value', user['id']);
			if ((nothingSelected && i == 0) || user['id'] == id)
			{
				option.attr('selected', 'selected');
				p.data('id', user['id']);
			}
			select.append(option);
		}
	}

	$('.user-selector').each(function()
	{
		var select = $('<select/>');
		$(this).append(select);
		var users = [];
		var id = $(this).data('id');
		if (!id)
		{
			id = -1;
		}
		$(this).data('users', users);
		$(this).data('id', id);
		$(this).data('select', select);
		updateUsers($(this));

		$.ajax($('#base-url').text() + '/users/ajax').done
		(
			function(where)
			{
				//zwróc funkcję done() która wie, co to jest where
				return function(data)
				{
					var p = where;
					p.data('users', data);
					updateUsers(p);
				}
			}($(this)) //wyinvokuj z where=$(this)
		);

		select.change(function()
		{
			var p = $(this).parents('.user-selector');
			p.data('id', $($('option:selected', p)[0]).data('id'));
		});


	});
});
