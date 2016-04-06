<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_View_Helper_DrawYamlParams extends Zend_View_Helper_Abstract
{
    const TRANSLATE_PREFIX = 'yaml';

    /**
     * @param array|string $data
     * @param array $parents
     * @param array $inputTypes
     * @return string
     */
    public function drawYamlParams($data, $parents = array(), $inputTypes = array())
    {
        $html = array();

        $level = count($parents);
        if ($level == 0) {
            $html[] = '<div class="ui-tabs">';
            $html[] = '<ul>';
            foreach ($data as $key => $value) {
                $html[] = sprintf('<li><a href="#tabs-%s">%s</a></li>', $key, $this->_translateKey($key));
            }
            $html[] = '</ul>';
            foreach ($data as $key => $value) {
                $html[] = sprintf('<div id="tabs-%s">', $key);
                $html[] = '<ul>';
                $html[] = $this->drawYamlParams($value, array($key), $inputTypes);
                $html[] = '</ul>';
                $html[] = '</div>';
            }
            $html[] = '</div>';
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $path = $parents;
                $path[] = $key;
                if (is_array($value)) {
                    $html[] = sprintf('<li class="level-%s">%s</li>', $level, $this->_translateKey($path));
                    $html[] = $this->drawYamlParams($value, $path, $inputTypes);
                } else {
                    $html[] = sprintf('<li class="level-%s">%s</li>', $level, $this->_createInput($path, $value, $inputTypes));
                }
            }
        } else {
            $html[] = sprintf('<li class="level-%s">%s</li>', $level, $this->_createInput($parents, $data, $inputTypes));
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string|array $key
     * @return string
     */
    private function _translateKey($key)
    {
        if (is_array($key))
            $key = implode('.', $key);

        return $this->view->translate(self::TRANSLATE_PREFIX . ':' . $key);
    }

    /**
     * @param array $path
     * @param mixed $value
     * @param array $inputTypes
     * @return string
     */
    private function _createInput($path, $value, $inputTypes = array())
    {
        $name = '[' . implode('][', $path) . ']';
        $id = implode('_', $path);
        $key = implode('.', $path);
        $checked = $value ? 'checked' : '';

        $label = sprintf('<label for="%s">%s:</label>', $id, $this->_translateKey($path));

        if (isset($inputTypes[$key]))
            $input = $inputTypes[$key];
        else
            $input = '<input type="text" name="data{name}" id="{id}" value="{value}" />';

        $input = str_replace(array('{name}', '{id}', '{value}', '{checked}'), array($name, $id, $value, $checked), $input);

        return $label . $input;
    }
}