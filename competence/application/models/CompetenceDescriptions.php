<?php
/**
 * @author <srałtor>
 */

class Model_CompetenceDescriptions extends Model_Abstract
{
    protected $_name = 'competence_descriptions';
    
    
    public function find_on_min_max_competence($min,$max,$competence_id)
    {
        $res=$this->fetchAll("min=$min AND max=$max AND competence_id=$competence_id");
        
        return count($res)?$res[0]:false;
    }
    
    public function delete_descriptions_for_competence($competence_id)
    {
        return $this->delete("competence_id=$competence_id");
    }
}
