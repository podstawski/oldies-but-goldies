<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class MapController extends Game_Controller
{
    public function indexAction()
    {
        $modelMapParams = new Model_MapParams();
        $this->view->flashVars = $modelMapParams->getMapFlashVars();
    }
}