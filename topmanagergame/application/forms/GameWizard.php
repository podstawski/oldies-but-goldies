<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_GameWizard extends Form_Abstract
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('game server name')
             ->setDescription('company name desc')
             ->setRequired(true)
             ->addValidator(new Zend_Validate_NotEmpty(), true)
             ->addValidator(new Zend_Validate_Alnum(false), true)
             ->addValidator(new Zend_Validate_StringLength(6, 256), true);

        $tmp = new Zend_Validate_Db_NoRecordExists(array(
            'table' => 'game_server',
            'field' => 'name',
        ));
        $tmp->setMessage("game '%value%' already exists", Zend_Validate_Db_RecordExists::ERROR_RECORD_FOUND);
        $name->addValidator($tmp);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Create game')
               ->setIgnore(true);

        $this->addElements(array($name, $submit));
        $this->setTableLayout();
    }
}