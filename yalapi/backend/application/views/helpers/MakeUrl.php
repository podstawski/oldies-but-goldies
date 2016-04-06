<?php

class Zend_View_Helper_MakeUrl extends Zend_View_Helper_Abstract
{
    public function makeUrl($action, $controller = null, array $params = null)
    {
        if (null === $controller) {
            $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        }

        $url = $controller . '/' . $action;

        if ('' !== ($baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl())) {
            $url = $baseUrl . '/' . $url;
        }

        if (null !== $params) {
            $paramPairs = array();
            foreach ($params as $key => $value) {
                $paramPairs[] = urlencode($key) . '=' . urlencode($value);
            }
            $paramString = implode('&', $paramPairs);
            $url .= '?' . $paramString;
        }

        $url = '/' . ltrim($url, '/');

        return $url;
    }
}