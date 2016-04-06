<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class Form_Abstract extends Zend_Form
{
    /**
     * @var GN_Validate_Censorship
     */
    private $_censorshipValidator;

    /**
     * @return GN_Validate_Censorship
     */
    private function _getCensorshipValidator()
    {
        if ($this->_censorshipValidator == null) {
            $tmp = new Zend_Session_Namespace('language');
            if (file_exists($tmp = APPLICATION_PATH . '/language/' . $tmp->language . '_censored.php')) {
                $this->_censorshipValidator = new GN_Validate_Censorship($tmp);
            }
        }
        return $this->_censorshipValidator;
    }

    /**
     * @param string|Zend_Form_Element $element
     * @param string $name
     * @param array $options
     * @return Form_Abstract
     */
    public function addElement($element, $name = null, $options = null)
    {
        /**
         * @var $element Zend_Form_Element
         */
        if (!($element instanceof Zend_Form_Element_Hidden)) {

            if ($element instanceof Zend_Form_Element_Text
			||  $element instanceof Zend_Form_Element_Textarea
			) {
			    if ($validator = $this->_getCensorshipValidator()) {
                    $element->addValidator($validator);
			    }
            }

        } else {
			$element->setDecorators(array('ViewHelper'))
			        ->setAttrib('class', 'hidden');
		}

		if (!($element instanceof GN_Form_Element_PlainText)) {
		    $element->setFilters(array('StringTrim', 'StripTags'));
		}

        parent::addElement($element, $name, $options);

        return $this;
    }
}