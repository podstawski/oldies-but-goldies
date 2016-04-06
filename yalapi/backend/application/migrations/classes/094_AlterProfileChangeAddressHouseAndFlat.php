<?php

class AlterProfileChangeAddressHouseAndFlat extends Doctrine_Migration_Base
{
    private $_tableName = 'user_profile';

    public function preUp()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN address_house_nr TYPE VARCHAR(20)');
        Doctrine_Manager::connection()->execute('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN address_flat_nr TYPE VARCHAR(20)');
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'' . $this->_tableName . '\')');
    }

    public function down()
    {

    }

    public function postDown()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'' . $this->_tableName . '\')');
    }
}
