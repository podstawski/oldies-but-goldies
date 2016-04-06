<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_MapParams extends Zend_Db_Table_Abstract
{
    const BUILDINGS_AMOUNT = 12;

    protected $_name = 'map_params';

    public function getMapFlashVars($adminMode = false)
    {
        $flashVars = array();
        $view = new Zend_View();
        if ($adminMode) {
            foreach (range(1, self::BUILDINGS_AMOUNT) as $type) {
                $vars = array();
                $row = $this->fetchRow(array('type = ?' => $type));
                if ($row) {
                    $vars = $row->toArray();
                    unset($vars['id'], $vars['type']);
                } else {
                    $vars['bname'] = 'Budynek #' . $type;
                }

                $vars['bhint'] = 'edytuj';
                $vars['burl']  = $view->url(array('action' => 'map-params', 'controller' => 'admin', 'type' => $type), null, true);

                $flashVars['b' . $type] = $vars;
            }
        } else {
            foreach ($this->fetchAll() as $row) {
                $vars = $row->toArray();
                $type = $vars['type'];
                unset($vars['id'], $vars['type']);

                if (empty($vars['bhint'])) {
                    unset($vars['bhint']);
                }

                if (empty($vars['burl'])) {
                    unset($vars['burl']);
                } else if (Zend_Uri::check($vars['burl']) == false) {
                    $vars['burl'] = $view->baseUrl($vars['burl']);
                }

                $flashVars['b' . $type] = $vars;
            }
        }

        return $flashVars;
    }
}