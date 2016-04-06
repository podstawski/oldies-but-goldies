<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Model_CompetenciesRow extends Zend_Db_Table_Row
{
	public function getAssociatedQuestions()
	{
		$modelQuestions = new Model_Questions();
		$select = $modelQuestions
			->select(true)
			->where('competence_id = ?', $this->id)
			->order('id ASC');
		return $modelQuestions->fetchAll($select);
	}

	public function getStandard($standardId)
	{
		$model = new Model_CompetenceStandards();
		$select = $model
			->select(true)
			->where('competence_id = ?', $this->id)
			->where('standard_id = ?', $standardId);
		return $model->fetchRow($select);
	}

	public function getSkills()
	{
		$model = new Model_Skills();
		$select = $model
			->select(true)
			->where('md5group = ?', $this->md5skill_id);
		return $model->fetchAll($select);
	}

	public function getStandardValue($standardId)
	{
		$standard = $this->getStandard($standardId);
		if ($standard === null)
		{
			return null;
		}
		return $standard->value;
	}
}
