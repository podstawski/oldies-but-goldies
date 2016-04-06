<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

abstract class Model_Abstract extends Zend_Db_Table
{

    public function __call($name, $args)
    {
        $name = strtolower($name);

        if (substr($name, 0, 12) == 'find_one_by_') {
            $res = call_user_func_array(array($this, 'find_by_' . substr($name, 12)), $args);
            return $res->current();
        }

        if (substr($name, 0, 8) == 'find_by_') {
            $what = substr($name, 8);
            $where = array();
            foreach ($args AS $arg) {
                $where[] = "$what='$arg'";
            }
            return $this->fetchAll(implode(' OR ', $where));
        }

        die ('Unknown function ' . $name);
    }

    public function createAndSave($array)
    {
        $rowSet = $this->createRow($array);
        $rowSet->save();
        return $rowSet;
    }

}