$(function()
{
	$('#group-form').submit(function(e)
	{
		var users = $.JSON.encode($('#users-to').data('users'))
		$('input[name=users]').val(users);
		return true;
	});

	if ($('#groups-edit, #groups-create').length)
	{
		$(document).ready(function()
		{
			function doAjax()
			{
				var search = $('#search-box input').val();
				utils.showThrobber();
				$.ajax(
				{
					url: $('#base-url').text() + '/users/ajax/search/' + search,
					dataType: 'json',
					success: function(data)
					{
						utils.hideThrobber();
						var p = $('#users-from');
						var users = data['records'];
						for (var i in users)
						{
							users[i]['name'] = users[i]['firstName'] + ' ' + users[i]['lastName'];
						}
						$('#search-box').prop('disabled', users.length <= data['total']);
						$(p).data('users', users);
						$(p).trigger('update');
					}
				});
			}
			$('#search-box input').keypress(function(e)
			{
				if (e.which != 13)
				{
					return true;
				}
				doAjax();
				return false;
			});
			doAjax();
		});
	}

	//dodaj usera do listy userów
	$('#user-add').click(function()
	{
		var usersFrom = $('#users-from').data('users');
		var usersTo = $('#users-to').data('users');
		for (var i in usersFrom)
		{
			if (!usersFrom[i]['checked'])
			{
				continue;
			}
			var exists = false;
			for (var j in usersTo)
			{
				if (usersTo[j]['id'] == usersFrom[i]['id'])
				{
					exists = true;
					break;
				}
			}
			if (!exists)
			{
				usersFrom[i]['checked'] = false;
				usersTo.push(usersFrom[i]);
			}
		}
		$('#users-to').trigger('update');
		return false;
	});

	//usuń usera z listy userów
	$('#user-del').click(function(e)
	{
		var usersTo = $('#users-to').data('users');
		for (var ii = usersTo.length, i = usersTo.length - 1; ii; ii --, i --)
		{
			if (!usersTo[i]['checked'])
			{
				continue;
			}
			usersTo.splice(i, 1);
		}
		$('#users-to').trigger('update');
		return false;
	});
});
