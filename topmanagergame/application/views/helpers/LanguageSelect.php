<?php
class Zend_View_Helper_LanguageSelect extends Zend_View_Helper_Abstract
{
    public function languageSelect()
    {
        $session  = new Zend_Session_Namespace('language');

        $languages = Zend_Registry::get('application_options');
        $languages = $languages['languages'];

        $options = array();
        foreach ($languages as $language => $locale)
            $options[$language] = $this->view->translate('lang:' . $language);

        return $this->view->formSelect(
            'lang',
            $session->language,
            array(
                'onchange' => 'document.location = \'' . htmlspecialchars($this->view->url()) . '?lang=\' + this.value'
            ),
            $options
        );
    }
}
