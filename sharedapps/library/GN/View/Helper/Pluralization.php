<?php

class GN_View_Helper_Pluralization extends Zend_View_Helper_Abstract
{
    /**
     * Return properly pluralized translation, based on passed number.
     *
     * @param  int    $n
     * @param  string $key1
     * @param  string $key2
     * @param  string $key3
     * @return string
     */
    public function pluralization($n, $key1, $key2, $key3 = null)
    {
        $value = null;
        if ($n == 1) {
            $value = $key1;
        } else if (null === $key3 || (!$this->_between($n, 10, 20) && $this->_between($n % 10, 2, 4))) {
            $value = $key2;
        } else {
            $value = $key3;
        }

        return $this->view->translate($value, $n);
    }

    protected function _between($n, $min, $max)
    {
        return $n >= $min && $n <= $max;
    }
}
