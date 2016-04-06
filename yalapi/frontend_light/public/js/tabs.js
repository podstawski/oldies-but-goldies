/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */
$(function()
{
	$('.tab-switcher').each(function()
	{
		var i = 0;
		$('li', this).each(function()
		{
			$(this).data('index', i);
			i ++;
		}).click(function(e)
		{
			var tabSwitcher = $($(this).parents('.tab-switcher'));
			var tabs = tabSwitcher.next('.tabs');
			var i = $(this).data('index');
	
			$('li.active', tabSwitcher).removeClass('active');
			$(this).addClass('active');
	
			$('.tab.active', tabs).removeClass('active');
			$($('.tab', tabs).get(i)).addClass('active');
		});
		
		$('li', this).first().trigger('click');
	});
});
