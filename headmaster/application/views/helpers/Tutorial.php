<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Zend_View_Helper_Tutorial extends Zend_View_Helper_Abstract
{
    public function tutorial()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $modelTutorial = new Model_Tutorial();
        foreach ($modelTutorial->fetchAll() as $row) {
            list ($controller, $action) = explode('/', $row->url);
            if ($controller == $request->getControllerName()
            && ($action == null || $action == $request->getActionName())
            ) {
                return '<a class="tutorial-yt" rel="prettyPhoto" href="' . $row->video . '">&nbsp;</a>';
            }
        }
    }
}