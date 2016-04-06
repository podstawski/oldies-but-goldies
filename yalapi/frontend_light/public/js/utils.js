var utils = new function()
{
	this.confirm = function(message)
	{
		return confirm(message);
	}

	this.togglePopup = function(element, shouldShow)
	{
		var dim = $('#dim');
		if (typeof shouldShow == 'undefined')
		{
			//determine automatically whether shouldShow or not
			shouldShow = dim.length == 0;
		}
		if (shouldShow)
		{
			//dim page
			dim = $('<div id="dim"></div>');
			//dim.click(function() { togglePopup(element); });
			//centerize element
			element.css
				(
				 {
				 'left': (($(window).width() - element.width()) >> 1) + 'px',
				 'top': (($(window).height() - element.height()) >> 1) + 'px'
				 }
				);
			$(document.body).append(dim);
			element.show();
		}
		else
		{
			dim.remove();
			element.hide();
		}
		//trigger an event for the element
		if (shouldShow)
		{
			element.trigger('show');
		}
		else
		{
			element.trigger('hide');
		}
	}

	this.showThrobber = function()
	{
		$('#throbber').show();
		clearInterval($('#throbber').data('interval-id'));
		var id = setInterval(function()
		{
			var totalWidth = $('#throbber img').innerWidth();
			var chunkWidth = $('#throbber div').width();
			var actualPosition = parseInt($('#throbber img').css('left').replace('px', ''));
			actualPosition -= chunkWidth;
			if (- actualPosition >= totalWidth)
			{
				actualPosition = 0;
			}
			$('#throbber img').css('left', actualPosition + 'px');
		}, 80);
		$('#throbber').data('interval-id', id);
		return true;
	}

	this.hideThrobber = function()
	{
		$('#throbber').hide();
		clearInterval($('#throbber').data('interval-id'));
	};
};

$(function()
{	
	$('.debug-content').hide();
	$('.debug-clicker').click(function()
	{
		$('.debug-content', $(this).parent()).toggle();
	});
	$('.debug .expandable-content .expandable-content').hide();
	$('.expandable-clicker').click(function()
	{
		$('.expandable-content', $(this).parent().parent()).toggle();
	});


	$('form [type=submit]').click(function(e)
	{
		$(this).attr('triggerer', '1');
	});
	$('form').bind('submit', function()
	{
		var triggerer = $($('[triggerer=1]', this).get(0));
		var submit = true;
		if (!triggerer.hasClass('skip-validation'))
		{
			$('.required', this).each(function(e)
			{
				if ($(this).val() == '')
				{
					submit = false;
					$(this).focus();
					$(this).attr('title', 'To pole jest wymagane.');
					//$(this).tooltip();
					return false;
				}
			});
		}
		if (!submit)
		{
			alert('Nie wypełniono wszystkich pól.');
			return false;
		}
		return true;
	});

	$('.action-delete').on('click', function(e)
	{
		if (utils.confirm('Czy na pewno chcesz usunąć ten obiekt?'))
		{
			return true;
		}
		return false;
	});

	$('[class*=\'action-\'], a').on('click', utils.showThrobber);
	$('form').on('submit', utils.showThrobber);
});
