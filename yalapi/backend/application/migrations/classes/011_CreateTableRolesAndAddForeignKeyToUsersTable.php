<?php
/**
 * Description
 * @author Radosław Benkel 
 */

class CreateTableRolesAndAddForeignKeyToUsersTable extends Doctrine_Migration_Base
{
    private $_tableName1    = 'roles';
    private $_tableName2    = 'users';
    private $_fkName        = 'fk_users_roles';
    
    public function up()
    {
        $this->createTable($this->_tableName1, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
        ));

        $this->addColumn($this->_tableName2, 'role_id', 'integer', null, array(
            'notnull' => false,
        ));

        $this->createForeignKey($this->_tableName2, $this->_fkName, array(
            'local'         => 'role_id',
            'foreign'       => 'id',
            'foreignTable'  => 'roles',
            'onDelete'      => 'SET NULL',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec("
            INSERT INTO {$this->_tableName1} VALUES
                (1, 'admin'),
                (2, 'user'),
                (3, 'project leader'),
                (4, 'center leader'),
                (5, 'trainer')
        ");
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName2, $this->_fkName);
        $this->removeColumn($this->_tableName2, 'role_id');
        $this->dropTable($this->_tableName1);
    }
}
