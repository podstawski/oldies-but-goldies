<?php
/**
 * Description
 * @author Radosław Benkel
 */
 
class CreateTableCourses extends Doctrine_Migration_Base
{
    private $_tableName = 'courses';
    private $_fkName = 'fk_courses_training_center';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'training_center_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'code' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'level' => array(
                'type' => 'smallint',
                'notnull' => false,
            ),
            'price' => array(
                'type' => 'int',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'created_date' => array(
                'type' => 'date',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey('courses', $this->_fkName, array(
             'local'         => 'training_center_id',
             'foreign'       => 'id',
             'foreignTable'  => 'training_centers',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_tableName . " ALTER COLUMN created_date SET DEFAULT NOW()");
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }
}
