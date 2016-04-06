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
	/**
	 * Expandable divy
	 */
	
	/*
	$('.expand-trigger').click(function()
	{
		$('.expand-target', $(this).parents('.expandable')).toggle('fast');
		$(this).toggleClass('active');
		return false;
	});
	*/
	

	
	$('#accordion .rowbox li').bind('click', function(event){
		if (event.target.nodeName!='INPUT'){
			if ($(this).hasClass('rowbox_active'))
				$(this).removeClass('rowbox_active');
			else {
				$('.rowbox_active').removeClass('rowbox_active');
				$(this).addClass('rowbox_active');
			}	
		}
	});
	
	if ($('#login_advpoints')){
		$('#login_advpoints .advpoints_green').fadeIn('slow', function(){
			$('#login_advpoints .advpoints_blue').fadeIn('slow', function(){
				$('#login_advpoints .advpoints_red').fadeIn('slow', function(){
					$('#login_advpoints .advpoints_yellow').fadeIn('slow');
				});
			});
		});
	}
});
