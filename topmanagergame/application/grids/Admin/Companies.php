<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_Admin_Companies extends Game_Grid
{
    public function init()
    {
        $select = Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('company', array('id', 'name', 'balance'))
            ->join('users', 'users.id = company.user_id', array('email'))
            ->joinLeft('rank', 'rank.company_id = company.id', array('score', 'rank_position' => 'id'));

        $this->setSource(new Bvb_Grid_Source_Zend_Select($select));

        $this->setNoFilters(false);

        $actions = new Bvb_Grid_Extra_Column();
        $actions->name('actions')
            ->position('right')
            ->callback(array(
                           'function' => array($this, 'getActions'),
                           'params' => array('{{id}}')
                       )
        );

        $this->addExtraColumns($actions);
    }

    public function getActions($id)
    {
        $actions = array();

        $actions[] = '<a href="' . $this->getView()
            ->url(array(
                      'controller' => 'admin',
                      'action' => 'edit-company-coowners',
                      'id' => $id
                  )
        ) . '" title="' . $this->__('admin edit company coownsers') . '" class="icon icon-edit"></a>';

        return implode('', $actions);
    }
}