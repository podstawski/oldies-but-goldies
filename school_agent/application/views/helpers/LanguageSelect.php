<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gmail.com>
 */

class Zend_View_Helper_LanguageSelect extends Zend_View_Helper_Abstract
{
    public function languageSelect()
    {
        $options = array();
        foreach (Zend_Registry::get('Zend_Translate')->getAdapter()->getList() as $lang) {
            $options[$lang] = $this->view->translate('lang:' . $lang);
        }
        return $this->view->formSelect(
            'lang',
            Zend_Registry::get('Zend_Locale')->getLanguage(),
            array(
                'onchange' => 'document.location = \'' . htmlspecialchars($this->view->baseUrl('index')) . '?lang=\' + this.value'
            ),
            $options
        );
    }
}
