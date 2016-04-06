<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_NameParams extends Zend_Db_Table_Abstract
{
    const MALE   = 'M';
    const FEMALE = 'F';

    const FIRST_NAME = 0;
    const LAST_NAME  = 1;

    protected $_name = 'name_params';

    public function pickRandomSex()
    {
        return rand(0, 100) < 70 ? self::MALE : self::FEMALE;
    }

    /**
     * @param string $sex
     * @return string
     */
    public function pickRandomName($sex)
    {
        $firstName = $this->fetchRow(array(
            'type = ?' => self::FIRST_NAME,
            'sex = ?' => $sex,
        ), 'RANDOM()');

        $lastName = $this->fetchRow(array(
            'type = ?' => self::LAST_NAME,
        ), 'RANDOM()');

        if ($sex == self::FEMALE && $lastName->sex == self::MALE) {
            $lastName->name = substr($lastName->name, 0, - 1) . 'a';
        }

        return $firstName->name . ' ' . $lastName->name;
    }
}