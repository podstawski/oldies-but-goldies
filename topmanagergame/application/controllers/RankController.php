<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class RankController extends Game_Controller
{
    public function indexAction()
    {
        $datagrid = new Grid_Rank_Users();
        $datagrid->deploy();

        $this->view->grid = $datagrid;
    }

    public function schoolsAction()
    {
        $datagrid = new Grid_Rank_Schools();
        $datagrid->deploy();

        $this->view->grid = $datagrid;

        $this->render('index');
    }
}