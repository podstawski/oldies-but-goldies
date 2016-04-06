/**
 * @author marcin.kurczewski@gammanet.pl Marcin Kurczewski
 */

var utils = new function()
{
	this.confirm = function(message)
	{
		return confirm(message);
	}

	this.alert = function(message)
	{
		return alert(message);
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

	/**
	 * Taby
	 */
	this.tabs = new function()
	{
		this.activate = function(where, index)
		{
			var ul = $(where).children('ul');
			var tabs = $(where).children('div.tab');
			$('li', ul).removeClass('active');
			$('li', ul).eq(index).addClass('active');
			tabs.not(':eq(' + index + ')').hide().removeClass('active').trigger('hide');
			tabs.eq(index).show().addClass('active').trigger('show');
			$(where).trigger('change');
		}
		this.getActiveIndex = function(where)
		{
			var ul = $(where).children('ul');
			var index = $('li.active', ul).index();
			return index;
		}
		this.getActiveTab = function(where)
		{
			var ul = $(where).children('ul');
			var index = $('li.active', ul).index();
			return $(where).children('div.tab').eq(index);
		}
	}
}

$(function()
{
	/**
	 * Checkboxable tabelki
	 */
	$('table thead input[type=checkbox]').click(function()
	{
		var index = $(this).index() + 1;
		var table = $(this).parents('table');
		var checked = $(this).prop('checked');
		$('tbody tr>*:nth-child(' + index + ') input[type=\'checkbox\']', table).prop('checked', checked);
	});

	$('table tbody input[type=checkbox]').click(function()
	{
		var index = $(this).index() + 1;
		var table = $(this).parents('table');
		var allChecked = true;
		$('tbody tr>*:nth-child(' + index + ') input[type=\'checkbox\']', table).each(function()
		{
			if (!$(this).prop('checked'))
			{
				allChecked = false;
			}
		});
		$('thead tr>*:nth-child(' + index + ') input[type=\'checkbox\']', table).prop('checked', allChecked);
	});

	/**
	 * Wyrównuj labele w formach
	 */
	 $('fieldset').each(function()
	{
		var maxWidth = 0;
		var labels = $(this).children('label');
		labels.each(function()
		{
			var width = $(this).width();
			if (width > maxWidth)
			{
				maxWidth = width;
			}
		});
		labels.width(maxWidth);
	});

	/**
	 * Taby
	 */
	$('.tabs').each(function()
	{
		var ul = $(this).children('ul');
		var tabs = $(this).children('.tab');
		if ($('li.active', ul).length == 0)
		{
			var index = 0;
			//zaznacz tab na podstawie #asdasdas w urlu
			if (window.location.hash)
			{
				for (var i in tabs)
				{
					var tab = $(tabs[i]);
					if (tab.attr('id') == window.location.hash.replace(/^#/, ''))
					{
						index = i;
					}
				}
			}
			utils.tabs.activate($(this), index);
		}
		$('li', ul).click($.proxy(function(e)
		{
			utils.tabs.activate($(this), $(e.currentTarget).index());
			return false;
		}, this));
	});

	/**
	 * Competence-related code
	 */
	$('select[name=domain-id]').change(function()
	{
		$(this).parents('form').submit();
	});
	/*var styles = [];
	$('link[rel=stylesheet]').each(function()
	{
		styles.push($(this).attr('href'));
	});
	$('textarea').htmlarea({css: styles});*/
	$('textarea').width('55.5em').htmlarea({css: $('body').data('base-url') + '/css/jHtmlArea.Editor.css'});
});
