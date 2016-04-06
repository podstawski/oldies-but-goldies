<?php

class Zend_View_Helper_PagerLinks extends Zend_View_Helper_Abstract
{
    public function pagerLinks(Zend_Paginator $pager)
    {
        $params         = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $actionName     = $params['action'];
        $controllerName = $params['controller'];
        unset($params['action'], $params['controller']);

        $pagerLinks = array();
        $current    = $pager->getCurrentPageNumber();
        $pageCount  = $pager->getPages()->pageCount;

        for ($pageID = 1; $pageID <= $pageCount; $pageID++) {
            $params['pageID'] = $pageID;
            if ($pageID == $current) {
                $pagerLinks[] = $pageID;
            } else {
                $pagerLinks[] = '<a href="' . $this->view->makeUrl($actionName, $controllerName, $params) . '">' . $pageID . '</a>';
            }
        }

        return implode($pagerLinks, ' | ');
    }
}