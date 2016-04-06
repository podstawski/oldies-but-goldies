<?php
/**
 * @author Radosław Szczepaniak
 */
 
abstract class Form_Abstract extends Zend_Form
{
    protected $_defaultElementsDecorators = array(
        array('ViewScript', array('viewScript' => 'decoratorFormElement.phtml'))
    );

    public function addElement($element, $name = null, $options = null)
    {
        /**
         * @var $element Zend_Form_Element
         */
        if (!$element instanceof Zend_Form_Element_Hidden) {

            if ($element instanceof Zend_Form_Element_Text
			||  $element instanceof Zend_Form_Element_Textarea
			) {
			    $file = APPLICATION_PATH . '/language/' . Zend_Registry::get('Zend_Locale')->getLanguage() . '/censored.php';
			    if (file_exists($file) == true)
                    $element->addValidator(new GN_Validate_Censorship($file));
            }

        } else {
			$element->setDecorators(array('ViewHelper'));
			$element->setAttrib('class', 'hidden');
		}

		if (!$element instanceof GN_Form_Element_PlainText) {
		    $element->setFilters(array('StringTrim', 'StripTags'));
		}

        parent::addElement($element, $name, $options);

        return $this;
    }

    public function setTableLayout()
    {
        foreach (array_values($this->getElementsAndSubFormsOrdered()) as $k => $element) {
            $class = $k % 2 ? 'odd' : 'even';
            if ($k == 0) {
                $class .= ' first-row';
            }
            if ($element instanceof Zend_Form_Element_Submit) {
                $element->setDecorators(array(
                    'ViewHelper',
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'text-center', 'colspan' => 2)),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => $class)),
                ));
            } else {
                $element->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td', 'tagClass' => 'label', 'escape' => false)),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => $class)),
                ));
            }
        }

        $empty = new GN_Form_Element_PlainText('table_header');
        $empty->setDecorators(array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'), array('tag' => 'th', 'colspan' => 2)),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $empty->setOrder(0)
              ->setValue('&nbsp;')
              ->setIgnore(true);

        $this->addElement($empty);

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table', 'class' => 'table-area')),
            'Form',
        ));
    }
}
