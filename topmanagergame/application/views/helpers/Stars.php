<?php
/**
 * Creates a valid number of stars
 * @author Radosław Benkel
 */

class Zend_View_Helper_Stars extends Zend_View_Helper_Abstract
{
    const LENGTH = 5;
    
    private $_html;

    public function stars($howMany)
    {
        $this->_html = '<ul class="stars">';
        for ($i = 1; $i <= self::LENGTH ; $i++) {
            $this->_html .= '<li' . ($i <= $howMany ? ' class="active"' : '') . '></li>';
        }
        $this->_html .= '</ul>';
        return $this->_html;
    }
}