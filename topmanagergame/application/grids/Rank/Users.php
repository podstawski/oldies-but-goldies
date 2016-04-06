<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

/**
 * @method Zend_Form getForm
 */
class Grid_Rank_Users extends Game_Grid
{
    public function init()
    {
        $select = Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('rank', array('rank_lp' => 'id', 'company_score' => 'score', 'employee_amount'))
            ->join('company', 'company.id = rank.company_id', array('company_name' => 'name', 'company_status' => 'status'))
            ->join('users', 'users.id = company.user_id', array('company_owner' => 'username'))
            ->order('rank_lp ASC');

        $this->setSource(new Bvb_Grid_Source_Zend_Select($select));

        $this->hideColumns('company_status');

        $this->updateColumn('rank_lp', array(
            'class' => 'text-right',
            'position' => 0
        ));

        $this->updateColumn('company_name', array(
            'position' => 1
        ));

        $this->updateColumn('company_owner', array(
            'position' => 2,
            'callback' => array(
                'function' => function ($owner) {
                    if (strpos($owner, '@') !== false)
                        list ($owner) = explode('@', $owner);

                    return $owner;
                },
                'params' => array('{{company_owner}}')
            )
        ));

        $view = $this->getView();
        $formatter = new Bvb_Grid_Formatter_Number;

        $this->updateColumn('company_score', array(
            'class' => 'text-right',
            'position' => 3,
            'callback' => array(
                'function' => function ($score, $status) use ($formatter, $view) {
                    if ($status == Model_Company::STATUS_BANKRUPT)
                        return $view->translate('rank company bankrupt');

                    return $formatter->format($score);
                },
                'params' => array('{{company_score}}', '{{company_status}}')
            )
        ));

        $this->updateColumn('employee_amount', array(
            'class' => 'text-right',
            'position' => 4,
            'format' => array('number')
        ));
    }
}