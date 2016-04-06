<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AppsController extends GN_Controller
{
    public function rankAction()
    {
        $this->view->layout()->disableLayout();

        $datagrid = new Grid_Rank_Users();
        $datagrid->deploy();

        $this->view->grid = $datagrid;
    }
}