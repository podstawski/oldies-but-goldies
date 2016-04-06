<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_CvParams extends Zend_Db_Table_Abstract
{
    protected $_name = 'cv_params';

    const FIELD_TRADE = 'trade';
    const FIELD_EDUCATION = 'education';
    const FIELD_LAST_EMPLOYER = 'last_employer';

    const FACE_IMAGES = 17;

    public function pickRandomValueFor($field)
    {
        $select = $this->select();
        $select->from($this->_name, array($field))
               ->where("$field IS NOT NULL")
               ->order('RANDOM()')
               ->limit(1);

        return $this->getAdapter()->fetchOne($select);
    }

    /**
     * @return string
     */
    public function pickRandomTrade()
    {
        return $this->pickRandomValueFor(self::FIELD_TRADE);
    }

    /**
     * @return string
     */
    public function pickRandomEducation()
    {
        return $this->pickRandomValueFor(self::FIELD_EDUCATION);
    }

    /**
     * @return string
     */
    public function pickRandomLastEmployer()
    {
        return $this->pickRandomValueFor(self::FIELD_LAST_EMPLOYER);
    }
}