<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableGameServerUser extends Doctrine_Migration_Base
{
    private $_tableName = 'game_server_user';
    private $_fk1 = 'fk_game_server_user_game_server';
    private $_fk2 = 'fk_game_server_user_users';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'game_server_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'create_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fk1, array(
            'local' => 'game_server_id',
            'foreign' => 'id',
            'foreignTable' => 'game_server',
            'onDelete' => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fk2, array(
            'local' => 'user_id',
            'foreign' => 'id',
            'foreignTable' => 'users',
            'onDelete' => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('ALTER TABLE ' . $this->_tableName . ' ALTER COLUMN create_date SET DEFAULT NOW()');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fk2);
        $this->dropForeignKey($this->_tableName, $this->_fk1);

        $this->dropTable($this->_tableName);
    }
}