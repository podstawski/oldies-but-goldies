<?php

class Zend_View_Helper_AdminTabs extends Zend_View_Helper_Abstract
{

    public function AdminTabs($user = 3, $controller = 'nauczyciel')
    {
        $html = '';

        $html .= '<ul class="clearfix">';
        $html .= $this->view->tutorial();

        if ($user === 3 || $user === 4) {
            $html .= '<li class="active">'
                . '<a href="' . $this->view->baseUrl('nauczyciel') . '">'
                . $this->view->translate('Panel nauczyciela')
                . '</a>'
                . '</li>';
        } elseif ($user === 5) {
            $html .= '<li' . ($controller === 'nauczyciel' ? ' class="active"' : '') . '>'
                . '<a href="' . $this->view->baseUrl('nauczyciel') . '">'
                . $this->view->translate('Panel nauczyciela')
                . '</a>'
                . '</li>'

                . '<li' . ($controller === 'administrator' ? ' class="active"' : '') . '>'
                . '<a href="' . $this->view->baseUrl('administrator') . '">'
                . $this->view->translate('Panel administratora')
                . '</a>'
                . '</li>';
        }
        $html .= '</ul>';

        echo $html;
    }
}
