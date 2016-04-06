/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
$(function()
{
	$('.multiple-user-selector').each(function()
	{
		//utwórz tabelkę
		var table = $('<table>' +
			'<thead>' +
			'<tr>' + 
			'<th class="chk"><input type="checkbox"></th>' +
			'<th class="lp"><span>L.p.</span></th>' +
			'<th><span>Nazwa użytkownika</span></th>' +
			'</tr>' +
			'</thead>' +
			'<tbody>' +
			'</tbody>' +
			'</table>');
		$(this).append(table);

		//dla każdego checkboxa, zaktualizuj stan głównego checkboxa
		$('tbody input[type=checkbox]', $(this)).live('click', function()
		{
			var allChecked = true;
			$('tbody input[type=checkbox]', $(this).parents('table')).each(function()
			{
				if (!$(this).prop('checked'))
				{
					allChecked = false;
				}
			});
			$('thead input[type=checkbox]', $(this).parents('table')).prop('checked', allChecked);
		});

		//dla głównego checkboxa, po kliknięciu zaktualizuj wszystkie checkboxy poniżej
		$('thead input[type=checkbox]', $(this)).live('click', function()
		{
			var allChecked = $(this).prop('checked');
			$('tbody input[type=checkbox]', $(this).parents('table')).each(function()
			{
				$(this).prop('checked', allChecked).trigger('change');
			});
		});

		//po kliknięciu checkboxa zaktualizuj odpowiadające mu dane
		$('tbody input[type=checkbox]', $(this)).live('change', function()
		{
			var users = $(this).parents('.multiple-user-selector').data('users');
			var i = $(this).parents('tr').data('index');
			var checked = $(this).prop('checked');
			users[i]['checked'] = checked;
		});

		//utwórz wiersze z użytkownikami dla kontrolki
		$(this).bind('update', function()
		{
			var users = $(this).data('users');
			$('tbody', $(this)).empty();
			for (var i in users)
			{
				var user = users[i];
				var row = $('<tr>' +
					'<td class="chk"><input type="checkbox"></td>' +
					'<td class="lp">' + (parseInt(i) + 1) + '</td>' +
					'<td>' + user['name'] + '</td>' +
					'</tr>');
				row.data('index', i);
				$('tbody', $(this)).append(row);
			}
		});


		if (!$(this).data('users'))
		{
			$(this).data('users', []);
		}
		$(this).trigger('update');
	});
});
