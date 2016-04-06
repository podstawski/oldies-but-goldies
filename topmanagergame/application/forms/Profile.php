<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_Profile extends Form_Company
{
    /**
     * @var int
     */
    protected $_userID;

    /**
     * @var array
     */
    protected $_userData;

    public function init()
    {
        parent::init();

        if ($this->_userID == null) {
            $this->_userID = Model_Player::getUserId();
        }

        $db = Zend_Db_Table::getDefaultAdapter();

        $schools = $db->fetchPairs(
            $db->select()->from('school', array('id', 'name'))
        );

        $school = new Zend_Form_Element_Select('school_id');
        $school->setLabel('profile school')
               ->setAttrib('id', 'school-id')
               ->setMultiOptions(array('' => '-- wybierz szkołę --') + $schools);

        $class = new Zend_Form_Element_Select('class_id');
        $class->setLabel('profile class')
              ->setAttrib('id', 'class-id')
              ->setRegisterInArrayValidator(false)
              ->setMultiOptions(array('' => '-- wybierz klasę --'));

        $this->addElements(array($school, $class));

        $this->name->setOrder(1);
        $this->school_id->setOrder(2);
        $this->class_id->setOrder(3);
        $this->buttons->setOrder(4);

        $view = $this->getView();
        $this->setTableLayout();
        $this->getElement('table_header')->setValue($view->translate('user profile'));

        $view->jQuery()->addOnLoad(<<< JS

    var change_school = function(e) {
        $("#class-id").find("option").not(":first").remove();

        var school_id = parseInt($("#school-id").val());
        if (school_id) {
            $.get(BASE_URL + "/office/get-classess-from-school", { id : school_id }, function (data) {
                data = data || {};
                for (var val in data) {
                    $("<option></option>").val(val).text(data[val]).appendTo("#class-id");
                }
                $("#class-id").attr("disabled", false);
            }, "json");
        } else {
            $("#class-id").attr("disabled", true);
        }
    }

    $("#school-id").change(change_school);

JS
        );

        $this->_userData = $db->query('SELECT username, company.id company_id, company.name, school.id school_id, school_class.id class_id, school_class_member.status member_status
            FROM users
            INNER JOIN company ON company.user_id = users.id
            LEFT JOIN school_class_member ON school_class_member.user_id = users.id
            LEFT JOIN school_class ON school_class.id = school_class_member.class_id
            LEFT JOIN school ON school.id = school_class.school_id
            WHERE users.id = ?', array($this->_userID)
        )->fetch(Zend_Db::FETCH_ASSOC);

        $this->name->getValidator('Db_NoRecordExists')->setExclude(array(
            'field' => 'id',
            'value' => $this->_userData['company_id']
        ));

        if (isset($this->_userData['school_id']) && !empty($this->_userData['school_id'])) {
            $classess = $db->fetchPairs(
                $db->select()->from('school_class', array('id', 'name'))->where('school_id = ?', $this->_userData['school_id'], Zend_Db::PARAM_INT)
            );
            $this->class_id->setMultiOptions(array('' => '-- wybierz klasę --') + $classess);
        } else {
            $view->jQuery()->addOnLoad('change_school();');
        }

        if (isset($this->_userData['member_status']) && $this->_userData['member_status']) {
            $school->setAttrib('disabled', true);
            $class->setAttrib('disabled', true);
        }

        $this->populate($this->_userData);
    }

    public function save()
    {
        $data = $this->getValues();

        $modelCompany = new Model_Company();
        $company = $modelCompany->find($this->_userData['company_id'])->current();
        $company->setFromArray($data)->save();

        $modelSchoolClassMember = new Model_SchoolClassMember();
        if ($data['class_id']) {
            $schoolClassMember = $modelSchoolClassMember->fetchRow(array(
                'user_id = ?' => $this->_userID
            ));

            if ($schoolClassMember == null) {
                $schoolClassMember = $modelSchoolClassMember->createRow();
                $schoolClassMember->user_id = $this->_userID;
            }

            $schoolClassMember->class_id = $data['class_id'];
            $schoolClassMember->is_teacher = 0;
            $schoolClassMember->save();
        } else {
            $modelSchoolClassMember->delete(array(
                'user_id = ?' => $this->_userID
            ));
        }
    }
}