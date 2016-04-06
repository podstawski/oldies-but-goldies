<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_View_Helper_AbsoluteUrl extends Zend_View_Helper_Abstract
{
    public function absoluteUrl($urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $url = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
            $url .= 's';
        $url .= '://';
		if (!isset($_SERVER['HTTP_HOST'])) {
			$url .= 'localhost';
		} else {
			$url .= $_SERVER['HTTP_HOST'];
		}
        $url .= $this->view->url($urlOptions, $name, $reset, $encode);
        return $url;
    }
}
