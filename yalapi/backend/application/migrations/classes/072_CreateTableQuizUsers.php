<?php

class CreateTableQuizUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'quiz_users';
    private $_fkName1 = 'fk_quiz_users_quizzes';
    private $_fkName2 = 'fk_quiz_users_users';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
               'type' => 'integer',
               'primary' => true,
               'autoincrement' => true,
            ),
            'quiz_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'quiz_id',
             'foreign'       => 'id',
             'foreignTable'  => 'quizzes',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_table(\'' . $this->_tableName . '\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_table(\'' . $this->_tableName . '\')');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
