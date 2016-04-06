<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateMapParamsTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('map_params', array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true
            ),
            'type' => array(
                'type' => 'varchar(64)',
                'notnull' => true,
                'unique' => true
            ),
            'title' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'url' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            )
        ), array(
            'type' => 'InnoDB',
            'charset' => 'utf8',
        ));
    }

    public function down()
    {
        $this->dropTable('map_params');
    }
}