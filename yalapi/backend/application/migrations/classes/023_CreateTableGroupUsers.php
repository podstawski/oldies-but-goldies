<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableGroupUsers extends Doctrine_Migration_Base
{
    private $_tableName = 'group_users';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id'=>array(
                'type'=>'integer',
                'primary'=>true,
                'autoincrement' =>true
            ),

            'group_id' => array(
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
}
