<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_EmployeeCv extends Zend_Db_Table_Abstract
{
    protected $_name = 'employee_cv';

    /**
     * @return Zend_Db_Table_Row_Abstract
     */
    public function generateCV()
    {
        $modelNameParams = new Model_NameParams();
        $modelCvParams = new Model_CvParams();

        $cv = $this->createRow();
        $cv->sex = $modelNameParams->pickRandomSex();
        $cv->name = $modelNameParams->pickRandomName($cv->sex);
        $cv->age = rand(23, 62);
        $cv->experience = max(0, $cv->age - 23 + rand(-5, 5));
        $cv->trade = $modelCvParams->pickRandomTrade();
        $cv->education = $modelCvParams->pickRandomEducation();
        $cv->last_employer = $cv->experience ? $modelCvParams->pickRandomLastEmployer() : '';
        $cv->face = rand(0, Model_CvParams::FACE_IMAGES - 1);
        $cv->save();

        return $cv;
    }
}