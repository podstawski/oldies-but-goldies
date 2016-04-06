<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_Company extends Form_Abstract
{
    public function init()
    {
        $company = new Zend_Form_Element_Text('name');
        $company->setLabel('company name')
                ->setDescription('company name desc')
                ->setRequired(true)
                ->addValidator(new Zend_Validate_NotEmpty(), true)
                ->addValidator(new Zend_Validate_Alnum(true), true)
                ->addValidator(new Zend_Validate_StringLength(0, 256), true)
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'company',
                    'field' => 'name',
                )), true);

        $company->getValidator('Db_NoRecordExists')
                ->setMessage("company '%value%' already exists", Zend_Validate_Db_RecordExists::ERROR_RECORD_FOUND);

        $buttons = new GN_Form_Element_PlainText('buttons');
        $buttons->setAttrib('id', 'register-buttons');
        $buttons->setValue('<input class="btn-orange" type="submit" value="Zapisz" />&nbsp;<input class="btn-orange cancel" type="button" value="Anuluj" onclick="document.location = BASE_URL + \'/\';" />')
                ->setIgnore(true);

        $this->addElements(array($company, $buttons));
    }

    /**
     * @return Form_Company
     */
    public function setTableLayout()
    {
        parent::setTableLayout();

        $this->getElement('buttons')
             ->removeDecorator('Label')
             ->getDecorator('data')
             ->setOption('colspan', 2)
             ->setOption('class', 'buttons');

        $this->getElement('table_header')
             ->setValue($this->getView()->translate('form company table header'));

        return $this;
    }
}