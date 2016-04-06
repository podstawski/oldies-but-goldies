<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_EduParams extends Zend_Db_Table_Abstract
{
    protected $_name = 'edu_params';

    /**
     * @param int $parent
     * @return array
     */
    public function getTreeData($parent = null)
    {
        $tree = array();

        $params = array();
        if (empty($parent)) {
            $params[] = 'parent_id IS NULL';
        } else {
            $params['parent_id = ?'] = $parent;
        }

        foreach ($this->fetchAll($params) as $treeEntry) {
            $tree[] = array(
                'id'       => $treeEntry->id,
                'label'    => $treeEntry->label,
                'children' => $this->getTreeData($treeEntry->id),
            );
        }

        return $tree;
    }
}