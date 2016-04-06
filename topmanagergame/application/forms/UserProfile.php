<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_UserProfile extends Form_Abstract
{
    /**
     * @var Model_UserRow
     */
    protected $_userRow;

    public function __construct(Model_UserRow $userRow)
    {
        $this->_userRow = $userRow;
        parent::__construct();
    }

    public function init()
    {
        $user = $this->_userRow;

        $username = new GN_Form_Element_PlainText('username');
        $username->setLabel('username')
            ->setIgnore(true);

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('email address')
            ->setDescription('email address desc')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_NotEmpty())
            ->addValidator(new Zend_Validate_EmailAddress())
            ->addValidator(new Zend_Validate_StringLength(0, 256));

//        $validator = new Zend_Validate_Db_RecordExists(
//            $user->select()
//                 ->where('id = ?', $user->id, Zend_Db::PARAM_INT)
//                 ->where('password = :value')
//        );
//
//        $validator->setMessage(
//            'Wrong password provided',
//            Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND
//        );

        $validator = new Zend_Validate_Callback(function($password) use ($user)
        {
            return Model_User::encryptPassword($password) === $user->password;
        });
        $validator->setMessage('Wrong password provided', Zend_Validate_Callback::INVALID_VALUE);

        $oldPassword = new Zend_Form_Element_Password('old_password');
        $oldPassword->setLabel('old password')
            ->setRequired(true)
            ->addValidator(new Zend_Validate_NotEmpty())
            ->addValidator($validator);

        $newPassword = new Zend_Form_Element_Password('new_password');
        $newPassword->setLabel('new password')
            ->addValidator(new Zend_Validate_NotEmpty())
            ->addValidator(new Zend_Validate_StringLength(6, 256));

        $retypePassword = new Zend_Form_Element_Password('retype_password');
        $retypePassword->setLabel('retype new password')
            ->addValidator(
            new Zend_Validate_Identical(array(
                                            'token'  => 'new_password',
                                            'strict' => true
                                        ))
        );

        $view = $this->getView();

        $html = $view->formSubmit('submit', $view->translate('save changes'), array('class' => 'btn-orange'))
                . '&nbsp;'
                . sprintf('<a href="%s" style="margin-left: 20px;">%s</a>', $view->url(array('action'     => 'account-removal-request',
                                                                                             'controller' => 'user'
                                                                                       ), null, true
                                                                          ), $view->translate('request account removal')
            );

        $actions = new GN_Form_Element_PlainText('actions');
        $actions->setValue($html)
            ->setIgnore(true);

        $this->addElements(array($username, $email, $oldPassword, $newPassword, $retypePassword, $actions));

        $this->setTableLayout();

        $actions->removeFilter('StripTags')
            ->setDecorators(
            array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag'     => 'td',
                                                        'class'   => 'text-center',
                                                        'colspan' => 2
                )
                ),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            )
        );

        $this->getElement('table_header')->setValue($view->translate('user profile'));

        $this->populate($user->toArray());
    }

    public function isValid($data)
    {
        if (!empty($data['new_password'])) {
            $this->getElement('retype_password')->setRequired(true);
            $valid = parent::isValid($data);
            $this->getElement('retype_password')->setRequired(false);
            return $valid;
        }

        return parent::isValid($data);
    }
}