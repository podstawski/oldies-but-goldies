<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Zend_View_Helper_Tutorial extends Zend_View_Helper_Abstract
{
    const PDF = 'podrecznik_ipk.pdf';

    public function tutorial()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $html = '<a class="tutorial-pdf" target="_blank" href="' . $this->view->baseUrl() . '/media/' . self::PDF . '"  title="' . $this->view->translate('tutorial pdf tooltip') . '">&nbsp;</a>';
        $modelTutorial = new Model_Tutorial();
        foreach ($modelTutorial->fetchAll() as $row) {
            list ($controller, $action) = explode('/', $row->url);
            if ($controller == $request->getControllerName()
            && ($action == null || $action == $request->getActionName())
            ) {
                $html .= '<a class="tutorial-yt" rel="prettyPhoto" href="' . $row->video . '&hd=1&rel=1" title="' . $this->view->translate('tutorial movie tooltip') . '">&nbsp;</a>';
            }
        }
        return $html;
    }
}