<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_Employees extends Game_Grid
{
    public function init()
    {
        $type = $this->getOption('type');
        $companyId = $this->getOption('company_id');

        $select = Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('company_employee', array('id', 'fired'))
            ->join('employee_cv', 'employee_cv.id = company_employee.employee_cv_id', array('name', 'age', 'experience'))
            ->where('type = ?', $type, Zend_Db::PARAM_INT);

        if ($companyId) {
            $select->where('company_id = ?', $companyId, Zend_Db::PARAM_INT)
                   ->order('name ASC');

        } else {
            $select->where('company_id IS NULL')
                   ->order('RANDOM()')
                   ->limit(10);
        }

        $this->setSource(new Bvb_Grid_Source_Zend_Select($select));

        $view = $this->getView();

        $this->hideColumns('fired');

        $this->updateColumn('name', array(
            'order' => !!$companyId
        ));

        $this->updateColumn('age', array(
            'class' => 'text-right',
            'order' => !!$companyId
        ));

        $this->updateColumn('experience', array(
            'class' => 'text-right',
            'callback' => array(
                'function' => function ($experience) use ($view) {
                    return $experience ? $view->years($experience) : 'brak';
               },
               'params' => array('{{experience}}')
            ),
            'order' => !!$companyId
        ));

        $this->updateColumn('id', array(
            'class' => 'text-right',
            'order' => false,
            'position' => 4,
            'title' => 'CV',
            'callback' => array(
                'function' => function ($id) use ($view) {
                    return sprintf(
                        '<a href="%s"><img src="%s" alt="cv"/></a>',
                        $view->url(array(
                            'controller' => 'employee',
                            'action' => 'show-cv',
                            'id' => $id
                        ), null, true),
                        $view->baseUrl() . '/images/cv-ico.png');
               },
               'params' => array('{{id}}')
            )
        ));

        $checkbox = new Bvb_Grid_Extra_Column();
        $checkbox->name('checkbox')
                 ->position('left')
                 ->title('<input type="checkbox" id="check_all" />')
                 ->class('text-center')
                 ->callback(array(
                    'function' => function ($id, $fired) {
                        if (!$fired) {
                            return '<input type="checkbox" name="employee[' . $id . ']" value="' . $id . '" />';
                        }
                        return '';
                    },
                    'params' => array('{{id}}', '{{fired}}')
                 ));
        $this->addExtraColumn($checkbox);

        $this->getView()->jQuery()->addOnLoad(<<< JS

    $("#check_all").click(function(e){
        $("#grid input:checkbox").not(this).attr("checked", $(this).is(":checked"));
    });

JS
        );
    }
}