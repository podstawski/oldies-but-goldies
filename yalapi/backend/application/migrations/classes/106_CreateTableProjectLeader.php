<?php
/**
 * Description
 * @author pudel
 */

class CreateTableProjectLeader extends Doctrine_Migration_Base
{
    private $_tableName = 'project_leaders';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id'=>array(
                'type'=>'integer',
                'primary'=>true,
                'autoincrement' =>true
            ),

            'project_id' => array(
                'type' => 'integer'
            ),
            'user_id' => array(
                'type' => 'integer'
            )

        ));

    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_table(\'' . $this->_tableName . '\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_table(\'' . $this->_tableName . '\')');
    }

}
