<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Grid_ClassMembers extends Game_Grid
{
    public function init()
    {
        $classID = $this->getOption('class_id');

        $select = Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('users', array('id', 'username'))
            ->join('school_class_member', 'school_class_member.user_id = users.id', array('status'))
            ->join('company', 'company.user_id = users.id', array('company_name' => 'name'))
            ->joinLeft('rank', 'rank.company_id = company.id', array('rank_company_lp' => 'id', 'rank_company_score' => 'score'))
            ->where('class_id = ?', $classID, Zend_Db::PARAM_INT)
            ->where('is_teacher = 0');

        $this->setSource(new Bvb_Grid_Source_Zend_Select($select));

        $this->hideColumns('status');

        $this->updateColumn('id', array(
            'class' => 'text-right'
        ));

        $this->updateColumn('rank_company_lp', array(
            'class' => 'text-right'
        ));

        $this->updateColumn('rank_company_score', array(
            'class' => 'text-right',
            'format' => array('number')
        ));

        $checkbox = new Bvb_Grid_Extra_Column();
        $checkbox->name('checkbox')
                 ->position('right')
                 ->title('Potwierdzony')
                 ->class('text-center')
                 ->callback(array(
                    'function' => function ($id, $status) {
                        return sprintf('<input type="checkbox" class="member-status" value="%d" %s/>', $id, $status ? 'checked' :'');
                    },
                    'params' => array('{{id}}', '{{status}}')
                 ));
        $this->addExtraColumn($checkbox);

        $actions = new Bvb_Grid_Extra_Column();
        $actions->name('actions')
                ->position('right')
                ->callback(array(
                    'function' => array($this, 'getActions'),
                    'params'   => array('{{id}}', '{{status}}')
                ));

        $this->addExtraColumns($actions);

        $view = $this->getView();

        $view->jQuery()->addOnLoad(<<< JS

    $("input.member-status").click(function(e){
        var checkbox = $(this);
        var status   = $(this).is(":checked") ? 1 : 0;
        $.post(BASE_URL + "/teacher/set-school-class-member-status", {
            user_id : checkbox.val(),
            status : status
        }, function(){
            checkbox.parent().next().find("a.icon-preview").toggle(status);
        });
    });

JS
        );
    }

    public function getActions($id, $status)
    {
        $actions = array();
        $actions[] = sprintf('<a href="%s" title="%s" class="icon icon-preview" %s></a>', $this->getView()->url(array(
            'controller' => 'user',
            'action' => 'remote-login',
            'user-id' => $id
        )), $this->__('do remote login'), $status ? '' : 'style="display: none;"');

        return implode('', $actions);
    }
}