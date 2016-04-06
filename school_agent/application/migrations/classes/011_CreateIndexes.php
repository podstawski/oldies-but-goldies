<?php

class CreateIndexes extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idxu_protected_email'      => 'protected (email)',
        'idx_actions_fk'      => 'actions (domain_id)',
        'idx_action_steps_fk'      => 'action_steps (action_id)',
        'idxu_domains_name'      => 'domains (domain_name)',
        'idx_users_fk'      => 'users (domain_id)',
        'idxu_users_email'      => 'users (email)',
        
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
