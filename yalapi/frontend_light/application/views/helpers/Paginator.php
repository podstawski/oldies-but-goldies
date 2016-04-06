<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Zend_View_Helper_Paginator extends Zend_View_Helper_Abstract
{
	public function paginator($currentPage, $totalRecords, $recordsPerPage)
	{
		if ($totalRecords == 0)
		{
			$from = 0;
			$to = 0;
		}
		else
		{
			$from = ($currentPage - 1) * $recordsPerPage + 1;
			$to = min($currentPage * $recordsPerPage, $totalRecords);
		}

		$ret = '';
		$ret .= '<div class="paginator right">';
		$ret .= '<p>' . $from . '-' . $to . ' z ' . $totalRecords . '</p>';

		$ret .= '<ul>';
		$link = new Zend_View_Helper_Url();

		if ($currentPage > 1)
		{
			$ret .= '<li class="icon-previous">' .
				'<a href="' . $link->url(array('page' => $currentPage - 1), null, false) . '">' .
				'Poprzednia strona' .
				'</a>' .
				'</li>';
		}
		else
		{
			$ret .= '<li class="icon-previous disabled">' .
				'Poprzednia strona' .
				'</li>';
		}

		if ($currentPage < ceil($totalRecords / $recordsPerPage))
		{
			$ret .= '<li class="icon-next">' .
				'<a href="' . $link->url(array('page' => $currentPage + 1), null, false) . '">' .
				'Następna strona' .
				'</a>' .
				'</li>';
		}
		else
		{
			$ret .= '<li class="icon-next disabled">' .
				'Następna strona' .
				'</li>';
		}

		//for ($i = 1; $i <= ceil($totalRecords / $recordsPerPage); $i ++)
		//{
		//	if ($i == $currentPage)
		//	{
		//		$ret .= '<li>' .
		//			'<strong>' .
		//			$i .
		//			'</strong>' .
		//			'</li>';
		//	}
		//	else
		//	{
		//		$ret .= '<li>' .
		//			'<a href="' . $link->url(array('page' => $i), null, false) . '">' .
		//			$i .
		//			'</a>' .
		//			'</li>';
		//	}
		//}

		$ret .= '</ul>';

		$ret .= '</div>';

		return $ret;
	}
}
?>
