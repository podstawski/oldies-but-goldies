<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class AppModel extends ActiveRecord\Model
{
    static $after_construct = array('after_construct');

    /**
     * Stores changed values. 'field_name' => array([initial_value], [modified_value])
     * @var array
     */
    protected $_diff = array();

    public function after_construct()
    {
        if ($this->is_new_record() == false) {
            $this->_diff = array();
        }
    }

    /**
     * Overwrites default assing_attrubute method, saving attribute state before and after change.
     * @param $name
     * @param $value
     * @return mixed
     */
    public function assign_attribute($name, $value)
    {
        //RB don't record chenges on primary key
        if ($name == $this->get_primary_key(true)) {
            return parent::assign_attribute($name, $value);
        }

        $beforeValue = isset($this->$name) ? $this->__get($name) : null;
        $value = parent::assign_attribute($name, $value);
        $this->register_field_change($name, $beforeValue, $value);
        return $value;
    }

    /**
     * Determine, if given attribute value has changed.
     * @param $key
     * @return bool
     */
    public function field_has_changed($key)
    {
        return array_key_exists($key, $this->_diff) && $this->_diff[$key][0] != $this->_diff[$key][1];
    }

    /**
     * Registering field change.
     * Needs to be invoked, when using setters to change field values and not using assign_attribute
     *
     * @see http://www.phpactiverecord.org/projects/main/wiki/Utilities#attribute-setters
     * @param $key
     * @param $before
     * @param $after
     */
    public function register_field_change($key, $before, $after)
    {
        $this->_diff[$key] = array($before, $after);
    }


    /**
     * Return pair of values changed for that key: array(initial, current)
     * Usage: list($before, $initial) = $model->get_field_change('field_name);
     * @param $key
     * @return null
     */
    public function get_field_change($key)
    {
        return $this->field_has_changed($key) ? $this->_diff[$key] : null;
    }

    /**
     * Return true if any field has changed.
     * @return bool
     */
    public function any_field_has_changed()
    {
        foreach (array_keys($this->_diff) as $key) {
            if ($this->field_has_changed($key)) {
                return true;
            }
        }
        return false;
    }
}