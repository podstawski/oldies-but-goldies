$(function()
{
	function showTooltip(object, value)
	{
		var finalTooltip = false;
		$('.tooltip').each(function()
		{
			$(this).hide();
			var min = $(this).attr('data-min');
			var max = $(this).attr('data-max');
			if (value >= min && value <= max)
			{
				finalTooltip = $(this);
			}
		});
		if (finalTooltip != false)
		{
			finalTooltip.show();
			finalTooltip.position(
			{
				'of': $('.ui-slider-handle', object),
				'my': 'left top',
				'at': 'right bottom'
			});
		}
	}

	function hideTooltips()
	{
		$('.tooltip').hide();
	}



	//answering exams
	$('.question .slider').each(function()
	{
		$(this).slider(
		{
			range: 'min',
			value: $(this).attr('data-default-value'),
			min: 0,
			max: 100,
			start: function (event, ui)
			{
				showTooltip(this, ui.value);
			},
			slide: function (event, ui)
			{
				showTooltip(this, ui.value);
				$('.ui-slider-handle', $(this)).text(ui.value);
			},
			stop: function (event, ui)
			{
				hideTooltips();
				var url = $(this).parents('form').attr('action');
				var data =
				{
					'question-id': $(this).attr('data-question-id'),
					'answer-value': ui.value
				};
				utils.showThrobber();
				$.post(url, data, function(data)
				{
					utils.hideThrobber();
				});
				$(this).attr('data-answered', '1');
			}
		});
		hideTooltips();
		$('.ui-slider-handle', $(this)).text($(this).attr('data-default-value'));
	});

});

