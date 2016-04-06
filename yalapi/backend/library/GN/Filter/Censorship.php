<?php
/**
 * @author Radosław Benkel
 */
 
class GN_Filter_Censorship implements Zend_Filter_Interface
{
    private $_options = array('replacement' => '***');

    protected static $_defaultWordsPath = null;

    /**
     * Constructor
     * @param string|array|Zend_Config $options OPTIONAL
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $this->_options = $options->toArray();
        } elseif (!is_array($options)) {
            $options = func_get_args();
            if (!empty($options)) {
                $this->_options['words'] = array_shift($options);
            }
            if (!empty($options)) {
                $this->_options['replacement'] = array_shift($options);
            }
        } else {
            $this->_options = array_merge($options, $this->_options);
        }

        if (!isset($this->_options['words']) && is_string(self::$_defaultWordsPath)) {
            $this->_options['words'] = self::$_defaultWordsPath;
        }

        if (!isset($this->_options['words'])) {
            throw new InvalidArgumentException('Words option must be set');
        }

        if (is_string($this->_options['words'])) {
            $filter = new Zend_Filter_RealPath(false);
            $path = $filter->filter($this->_options['words']);
            if (file_exists($path)) {
                $this->_options['words'] = include $path;
            } else {
                throw new InvalidArgumentException('Words file ' . $path . ' not found in specified location');
            }
        } elseif (!is_array($this->_options['words'])) {
            throw new InvalidArgumentException('Words param must be path or array');
        }
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        return str_replace($this->_options['words'], $this->_options['replacement'], $value);
    }

    public static function setDefaultWordsPath($defaultWordsPath)
    {
        self::$_defaultWordsPath = $defaultWordsPath;
    }

    public static function getDefaultWordsPath()
    {
        return self::$_defaultWordsPath;
    }
}
