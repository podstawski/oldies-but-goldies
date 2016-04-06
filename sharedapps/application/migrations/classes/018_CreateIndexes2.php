<?php

class CreateIndexes2 extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idx_contacts_fk'      => 'contacts (contact_group_id)',
       	'idx_contact_emails_fk'		=> 'contact_emails(contact_id)',
       	'idx_contact_email'	=> 'contact_emails(email)',
       	'idxu_user_contact_groups_fk'	=> 'user_contact_groups(user_id,contact_group_id)',
       	'idx_user_contact_group_contacts_fk'=> 'user_contact_group_contacts(user_contact_group_id,contact_id)',
	
	 
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
