<?php

class CreateIndexes extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idx_exams_fk'      => 'exams (domain_id,user_id)',
        'idx_exam_managers_fk'      => 'exam_managers (user_id,exam_id)',
        'idx_exam_participants_fk'      => 'exam_participants (user_id,group_id,exam_id)',
        'idx_groups_fk'      => 'groups (domain_id)',
        'idx_competence_standards_fk'      => 'competence_standards (competence_id,standard_id)',
        'idx_project_competencies_fk'      => 'project_competencies (project_id,competence_id)',
        'idx_user_groups_fk'      => 'user_groups (user_id,group_id)',
        'idx_users_fk'      => 'users (domain_id)',
        'idx_questions_fk'      => 'questions (competence_id)',
        'idx_answers_fk'      => 'answers (user_id,question_id,exam_id)',
        
    );

    public function up()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            $uniqe=substr($idxName,0,4)=='idxu'?'UNIQUE':'';
            Doctrine_Manager::connection()->exec('CREATE '.$uniqe.' INDEX ' . $idxName . ' ON ' . $idxTable);
        }
    }

    public function down()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            Doctrine_Manager::connection()->exec('DROP INDEX IF EXISTS ' . $idxName);
        }
    }
}
