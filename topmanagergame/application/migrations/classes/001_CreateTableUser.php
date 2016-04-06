<?php

class CreateTableUser extends Doctrine_Migration_Base
{
    private $_tableName = 'users';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'role' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'username' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'email' => array(
                'type' => 'varchar(256)',
                'notnull' => true
            ),
            'password' => array(
                'type' => 'varchar(32)',
                'notnull' => true
            ),
            'activation_code' => array(
                'type' => 'varchar(32)',
                'notnull' => false
            ),
            'other_code' => array(
                'type' => 'varchar(32)',
                'notnull' => false
            ),
            'is_hidden' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'identity' => array(
                'type' => 'varchar(256)',
                'notnull' => false
            )
        ), array(
            'type' => 'InnoDB'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER role SET DEFAULT 1');
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER is_hidden SET DEFAULT 0');

//        Doctrine_Manager::connection()->exec('INSERT INTO ' . $this->_tableName . ' (role, username, email, password) VALUES (?, ?, ?, ?)', array(
//            2,
//            'Administrator',
//            'ipkadmin@gammanet.pl',
//            md5('1pkj3st5up3r')
//        ));
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}