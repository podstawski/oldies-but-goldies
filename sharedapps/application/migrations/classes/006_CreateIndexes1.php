<?php

class CreateIndexes1 extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idx_users_fk'      => 'users (domain_id)',
        'idxu_users_email'      => 'users (email)',
       	'idx_labels_fk'		=> 'labels(user_id)',
       	'idx_messages_fk'	=> 'messages(user_id)',
       	'idxu_messages_id'	=> 'messages(message_id)',
       	'idxu_message_labels_fk'=> 'message_labels(label_id,message_id)',
       	'idxu_user_labels_fk'	=> 'user_labels(label_id,user_id)',
       	'idxu_user_messages_fk'	=> 'user_messages(user_id,message_id)',
	
	 
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
