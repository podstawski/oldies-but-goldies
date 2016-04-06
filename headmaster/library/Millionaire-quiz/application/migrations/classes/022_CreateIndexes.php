<?php

class CreateIndexes extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idx_answers_fk'      		=> 'answers (question_id)',
        'idx_attempts_fk'      	   => 'attempts (test_pass)',
        'idx_attempts_fk2'     	   => 'attempts (user_id)',
        'idx_categories_fk'      	   => 'categories (parent_id,category_type_id)',
        'idx_questions_fk'     	   => 'questions (author_id)',
        'idx_tests_fk'     	   => 'tests (author_id)',
        'idxu_tests_pass'     	   => 'tests (pass)',
        'idx_users_fk'     	   => 'users (domain_id)',
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
