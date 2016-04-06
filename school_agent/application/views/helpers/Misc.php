<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Zend_View_Helper_Misc extends Zend_View_Helper_Abstract
{
	public function misc()
	{
		die();
	}

	private static function intvalize(array $input)
	{
		return array_map(function($a) { return intval($a); }, $input);
	}

	public static function convertDateTime($date)
	{
		if (strpos($date, ' ') !== false)
		{
			list ($datePart, $timePart) = explode(' ', $date);
		}
		else
		{
			$datePart = $date;
		}
		if (isset($datePart))
		{
			list ($day, $month, $year) = Zend_View_Helper_Misc::intvalize(explode('-', $datePart));
			if (strlen($day) == 4)
			{
				$tmp = $day;
				$day = $year;
				$year = $tmp;
			}
		}
		else
		{
			list ($day, $month, $year) = Zend_View_Helper_Misc::intvalize(explode('-', date('d-m-Y')));
		}
		if (isset($timePart))
		{
			list ($hour, $minute, $second) = Zend_View_Helper_Misc::intvalize(explode(':', $timePart));
		}
		else
		{
			$hour = 0;
			$minute = 0;
			$second = 0;
		}

		$unixTime = mktime($hour, $minute, $second, $month, $day, $year);
		return $unixTime;
	}

		public function progressScript() {
			$html = '
			<script type="text/javascript">
				$(function() {
					//progress
					function updateThrobber(sender, progressID) {
						//updatuj progressbar
						var url = "' . htmlspecialchars($this->view->url(array('controller' => 'progress', 'action' => 'ajax-get'), null, true)) . '";
						url += "?" + Math.round((new Date()).getTime()/* / 1000*/);
						url += "&progress-id=" + progressID;
						$.ajax ({
							url: url,
							success: function(response) {
								var text = sender.attr("data-throbber-text");
								if (!text) {
									text = "' . addslashes($this->view->translate('misc_progress_text')) . '";
								}
								text += " (";
								if (response["percent"]) {
									text += response["percent"];
								} else {
									text += "?";
								}
								text += "%)";
								$("#throbber-text").html(text);

								if (response["finished"]) {
									console.log(response);
									utils.hideThrobber();
									window.clearInterval(interval);
									var refreshUrl;
									if (response["success"]) {
										refreshUrl = sender.attr("data-return-url");
									} else {
										refreshUrl = "' . addslashes($this->view->url(array('controller' => 'dashboard', 'action' => 'index'), null, true)) . '";
									}
									window.location.href = refreshUrl;
								}
							}
						});
					}

					$("form").submit(function() {
						var form = $(this);
						var data = form.serialize();
						var url = form.attr("action");
						var progressID = Math.random();
						utils.showThrobber();
						data += "&progress-id=" + progressID;
						//alert(data);
						//return false;
						interval = window.setInterval(function() { updateThrobber(form, progressID); }, 1500);
						updateThrobber(form, progressID);
						$.post (
							url,
							data,
							function(response) { }
						);
						return false;
					});
				});
			</script>';
			return $html;
		}

}
