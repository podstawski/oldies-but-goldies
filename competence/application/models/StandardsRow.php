<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_StandardsRow extends Zend_Db_Table_Row
{


    public function add_competence($competence,$value)
    {
        $competence_id = is_integer($competence) ? $competence : $competence->id;

        $value=str_replace(',','.',$value);

        $db = $this->getTable()->getAdapter();

        $data = $this->getTable()->getAdapter()
            ->select()
            ->from('competence_standards')
            ->where('standard_id = ?', $this->id,Zend_Db::PARAM_INT)
            ->where('competence_id = ?', $competence_id, Zend_Db::PARAM_INT)
            ->query()
            ->fetchAll();

        if (empty($data)) {
            $db->insert('competence_standards', array(
                'standard_id' => $this->id,
                'competence_id' => $competence_id,
                'value' => $value
            ));
        }
        else {
            $db->update('competence_standards', array(
                'value'=>$value   
            ),'id='.$data[0]->id);
        }
    }
    
    public function delete_competencies_except($competence_ids_array=array())
    {
        $db = $this->getTable()->getAdapter();
        
        $sql="DELETE FROM competence_standards WHERE standard_id = ".$this->id;
        if (!empty($competence_ids_array)) $sql.=" AND competence_id NOT IN (".implode(',',$competence_ids_array).")";
        $db->exec($sql);
    }

}
