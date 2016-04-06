<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Zend_View_Helper_Debug extends Zend_View_Helper_Abstract
{
	private $requests = array();

	public function addRequest($request)
	{
		$this->requests []= $request;
	}

	public function debug()
	{
		$ret = '';
		$ret .= '<div class="expandable debug">' .
			'<div class="expandable-header">' .
			'<span class="expandable-clicker"></span>' .
			'<span class="expandable-title">Debug info</span>' .
			'</div>' .
			'<div class="expandable-content">';
		foreach ($this->requests as $request)
		{
			$ret .= '<div class="expandable">' .
				'<div class="expandable-header">' .
				'<span class="expandable-clicker"></span>' .
				'<span class="expandable-title">' .  $request['type'] . ' <a target="_blank" href="' . $this->view->baseUrl('debug_url.php?url=') . urlencode($request['inputURL']).'">';
			$max = 100;
			if (strlen($request['inputURL']) > $max)
			{
				$ret .= substr($request['inputURL'], 0, $max - 5) . '(...)';
			}
			else
			{
				$ret .= $request['inputURL'];
			}
			$ret .= '</a></span>' .
				'</div>' .
				'<div class="expandable-content">';

			$ret .= '<ul class="tab-switcher">';
			$ret .= '<li>Basic</li>';
			if (!empty($request['inputGET']))
			{
				$ret .= '<li>Input GET</li>';
			}
			if (!empty($request['inputPOST']))
			{
				$ret .= '<li>Input POST</li>';
			}
			$ret .= '<li>Input headers</li>';
			$ret .= '<li>Output headers</li>';
			$ret .= '<li>Output body</li>';
			$ret .= '</ul>';

			$ret .= '<div class="tabs">';

			$ret .= '<div class="tab active">';
			$ret .= '<p>Misc:</p>' .
				'<table>' .
				'<tr><th>Request type</th><td>' . $request['type'] . '</td></tr>' .
				'<tr><th>Status code</th><td>' . $request['outputStatusCode'] . '</td></tr>' .
				'<tr><th>Input cookies</th><td>' . $request['inputCookies'] . '</td></tr>' .
				'<tr><th>Output cookies</th><td>' . $request['outputCookies'] . '</td></tr>' .
				'<tr><th>Request preparation time</th><td>' . $request['timePreparation'] . 's</tr>' .
				'<tr><th>Execution time</th><td>' . $request['timeExecution'] . 's</tr>' .
				'</table>';
			$ret .= '<p>CURL info:</p>';
			$ret .= '<table>';
			foreach ($request['curlInfo'] as $key => $val)
			{
					$ret .= '<tr>' .
						'<th>' . $key . '</th>' .
						'<td>' . $key . '</td>' .
						'</tr>';
			}
			$ret .= '</table>';
			$ret .= '</div>';

			if (!empty($request['inputGET']))
			{
				$ret .= '<div class="tab">';
				$ret .= '<table>';
				foreach ($request['inputGET'] as $key => $value)
				{
					$ret .= '<tr>' .
						'<th>' . $key . '</th>' .
						'<td>' . htmlspecialchars(print_r($value, true)) . '</td>' .
						'</tr>';
				}
				$ret .= '</table>';
				$ret .= '</div>';
			}

			if (!empty($request['inputPOST']))
			{
				$ret .= '<div class="tab">';
				$ret .= '<table>';
				foreach ($request['inputPOST'] as $key => $value)
				{
					$ret .= '<tr>' .
						'<th>' . $key . '</th>' .
						'<td>' . htmlspecialchars($value) . '</td>' .
						'</tr>';
				}
				$ret .= '</table>';
				$ret .= '</div>';
			}

			$ret .= '<div class="tab">';
			$ret .= '<p>Input headers:</p>' .
				'<table>';
			foreach ($request['inputHeaders'] as $key => $value)
			{
				$ret .= '<tr>' .
					'<th>' . $key . '</th>' .
					'<td>' . $value . '</td>' .
					'</tr>';
			}
			$ret .= '</table>';
			$ret .= '</div>';

			$ret .= '<div class="tab">';
			$ret .= '<p>Output headers:</p>' .
				'<table>';
			foreach ($request['outputHeaders'] as $key => $value)
			{
				$ret .= '<tr>' .
					'<th>' . $key . '</th>' .
					'<td>' . $value . '</td>' .
					'</tr>';
			}
			$ret .= '</table>';
			$ret .= '</div>';

			$ret .= '<div class="tab">';
			$ret .= '<p>Output (RAW):</p>';
			$ret .= '<pre>' . htmlspecialchars($request['outputRaw']) . '</pre>';
			$ret .= '<p>Output (JSON):</p>';
			$ret .= '<pre>';
			$ret .= htmlspecialchars(print_r($request['outputJSON'], true));
			$ret .= '</pre>';
			$ret .= '</div>';

			$ret .= '</div>';

			$ret .= '</div>';
			$ret .= '</div>';
		}
		$ret .= '</div>';
		$ret .= '</div>';
		return $ret;
	}
}
?>
