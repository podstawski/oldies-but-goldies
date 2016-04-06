<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class AddForeignKeysToGroupUsersTable extends Doctrine_Migration_Base
{
    private $_tableName = 'group_users';
    private $_fkName1 = 'user_groups_fk';
    private $_fkName2 = 'groups_user_fk';
    
    public function up()
    {
        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
            'local'         => 'user_id',
            'foreign'       => 'id',
            'foreignTable'  => 'users',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
            'local'         => 'group_id',
            'foreign'       => 'id',
            'foreignTable'  => 'groups',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
    }
}
