<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

/**
 * @method Zend_Form getForm
 */
class Grid_Admin_Users extends Game_Grid
{
    public function init()
    {
        $select = Zend_Db_Table::getDefaultAdapter()
            ->select()
            ->from('users', array('id', 'role', 'username', 'email'))
            ->joinLeft('school_class_member', 'school_class_member.user_id = users.id', null)
            ->joinLeft('school_class', 'school_class.id = school_class_member.class_id', array('class_name' => 'name'))
            ->joinLeft('school', 'school.id = school_class.school_id', array('school_name' => 'name'));

        $this->setSource(new Bvb_Grid_Source_Zend_Select($select));

        $this->setNoFilters(false);

        $form = new Game_Grid_Form();
        $form->setEdit(true);
        $form->setInputsType(array('role' => 'select', 'is_hidden' => 'checkbox'));
        $form->setAllowedFields(array('username', 'email', 'role', 'activation_code', 'other_code', 'is_hidden'));
        $this->setForm($form);

        $subform = $this->getForm(1);
        $subform->role->setMultiOptions(array(
            Model_Player::ROLE_USER  => 'user',
            Model_Player::ROLE_ADMIN => 'admin',
        ))->setAttrib('size', null);

        $subform->username->setAttrib('readonly', 'readonly')->setIgnore(true);

        $this->hideColumns('id', 'email');

        $this->updateColumn('role', array(
            'callback' => array(
                'function' => function($role) {
                    return Model_Player::$roles[$role];
                },
                'params' => array('{{role}}')
            )
        ));

        $actions = new Bvb_Grid_Extra_Column();
        $actions->name('actions')
            ->position('right')
            ->callback(array(
                           'function' => array($this, 'getActions'),
                           'params'   => array('{{id}}')
                       )
        );

        $this->addExtraColumns($actions);
    }

    public function getActions($id)
    {
        $actions = array();
        $actions[] = '<a href="' . $this->getView()
            ->url(array(
                      'controller' => 'user',
                      'action'     => 'remote-login',
                      'user-id'    => $id
                  )
        ) . '" title="' . $this->__('do remote login') . '" class="icon icon-preview"></a>';

        $actions[] = '<a href="' . $this->getView()
            ->url(array(
                      'controller' => 'admin',
                      'action'     => 'edit-user-profile',
                      'id'         => $id
                  )
        ) . '" title="' . $this->__('admin edit user profile') . '" class="icon icon-edit"></a>';

        $actions[] = '<a href="' . $this->getView()
            ->url(array(
                      'controller' => 'admin',
                      'action'     => 'delete-user',
                      'id'         => $id
                  )
        ) . '" title="' . $this->__('do delete user') . '" class="icon icon-delete"></a>';

        return implode('', $actions);
    }
}