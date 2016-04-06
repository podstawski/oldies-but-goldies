<?php

class Form_Simple extends Zend_Form
{
    public function render(Zend_View_Interface $view = null)
    {
        foreach ($this->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Hidden) {
                $element->setDecorators(array('ViewHelper'));
            }
        }
        return parent::render($view);
    }
}