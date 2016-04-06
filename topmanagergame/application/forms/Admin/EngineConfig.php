<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Form_Admin_EngineConfig extends Form_Abstract
{
    public function init()
    {
        $runEvery = new Zend_Form_Element_Text(Model_GameData::ENGINE_RUN_EVERY);
        $runEvery->setLabel('engine run every label')
                 ->setValue(Model_GameData::getData(Model_GameData::ENGINE_RUN_EVERY))
                 ->addValidator(new Zend_Validate_Int())
                 ->addValidator(new GN_Validate_GreaterOrEqualThan(0));

        $runAt = new Zend_Form_Element_Text(Model_GameData::ENGINE_RUN_AT);
        $runAt->setLabel('engine run at label')
              ->setValue(Model_GameData::getData(Model_GameData::ENGINE_RUN_AT))
              ->addValidator(new Zend_Validate_Regex('/\d{2}:\d{2}/'));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Zapisz zmiany')
               ->setIgnore(true)
               ->setAttrib('class', 'btn-orange');

        $this->addElements(array($runEvery, $runAt, $submit));

        $this->setTableLayout();
    }
}