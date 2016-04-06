<?php
/**
 * Description
 * @author Radosław Benkel
 */
 
class CreateTableCourseUnits extends Doctrine_Migration_Base
{
    private $_tableName = 'course_units';
    private $_fkName1 = 'fk_course_units_courses';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'hour_amount' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'course_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
            'local'         => 'course_id',
            'foreign'       => 'id',
            'foreignTable'  => 'courses',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
