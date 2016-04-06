<?php
class CreateTableGoogleTokens extends Doctrine_Migration_Base
{
    private $_tableName = 'google_tokens';
    private $_fkName1 = 'fk_tokens__user';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'scope' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'token' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
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
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
