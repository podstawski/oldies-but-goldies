<?php

class CreateProfiles extends Doctrine_Migration_Base
{
    private $_tableName = 'profiles';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'data' => array(
                'type' => 'text'
            )
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ADD COLUMN created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
