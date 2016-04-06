<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Zend_View_Helper_DrawEduTree extends Zend_View_Helper_Abstract
{
    public function drawEduTree($parentId = null)
    {
        static $modelEduTree;
        $modelEduTree = new Model_EduParams();

        $html = '';
        $params = array();

        if (empty($parentId)) {
            $params[] = 'parent_id IS NULL';
        } else {
            $params['parent_id = ?'] = $parentId;
        }

        $treeItems = $modelEduTree->fetchAll($params);

        if ($treeItems->count() > 0)
        {
            $html .= '<ul>';
            foreach ($treeItems as $treeEntry) {
                $html .= '<li id="' . $treeEntry->id . '"><a href="#">' . $treeEntry->label . '</a>';
                $html .= $this->drawEduTree($treeEntry->id);
                $html .= '</li>';
            }
            $html .= '</ul>';
        }

        return $html;
    }
}