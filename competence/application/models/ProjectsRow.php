<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_ProjectsRow extends Zend_Db_Table_Row
{
	public function getCompetencies()
	{
        return $this->getTable()->getAdapter()
            ->select()
			->from('project_competencies')
			->join('competencies', 'competence_id = competencies.id')
			->where('project_id = ?', $this->id, Zend_DB::PARAM_INT)
			->query()
			->fetchAll();
	}

	public function getStandards()
	{
		return $this->getTable()->getAdapter()
			->select()
			->from('standards')
			->distinct('standards')
			->join('competence_standards', 'competence_standards.standard_id = standards.id', array())
			->join('project_competencies', 'project_competencies.competence_id = competence_standards.competence_id', array())
			->where('project_competencies.project_id = ?', $this->id)
			->query()
			->fetchAll();
	}

    public function add_competence($competence)
    {
        $competence_id = is_integer($competence) ? $competence : $competence->id;

        $db = $this->getTable()->getAdapter();

        $data = $this->getTable()->getAdapter()
            ->select()
            ->from('project_competencies')
            ->where('project_id = ?', $this->id,Zend_Db::PARAM_INT)
            ->where('competence_id = ?', $competence_id, Zend_Db::PARAM_INT)
            ->query()
            ->fetchAll();

        if (empty($data)) {
            $db->insert('project_competencies', array(
                'project_id' => $this->id,
                'competence_id' => $competence_id
            ));
        }
    }
    
    public function delete_competencies_except($competence_ids_array=array())
    {
        $db = $this->getTable()->getAdapter();
        
        $sql="DELETE FROM project_competencies WHERE project_id = ".$this->id;
        if (!empty($competence_ids_array)) $sql.=" AND competence_id NOT IN (".implode(',',$competence_ids_array).")";
        $db->exec($sql);
    }
}
