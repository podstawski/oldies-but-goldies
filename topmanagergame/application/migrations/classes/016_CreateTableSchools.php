<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableSchools extends Doctrine_Migration_Base
{
    private $_tableName1 = 'school';
    private $_tableName2 = 'school_class';
    private $_tableName3 = 'school_class_member';

    private $_fkName2 = 'fk_school_class_school';

    private $_fkName3a = 'fk_school_class_member_school_class';
    private $_fkName3b = 'fk_school_class_member_user';

    public function up()
    {
        $this->createTable($this->_tableName1, array(
            'id' => array(
                'type'  => 'int',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(255)',
                'notnull' => true
            ),
            'description' => array(
                'type' => 'text'
            ),
            'address' => array(
                'type' => 'text'
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createTable($this->_tableName2, array(
            'id' => array(
                'type' => 'int',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'school_id' => array(
                'type' => 'int',
                'notnull' => true
            ),
            'name' => array(
                'type' => 'varchar(255)',
                'notnull' => true
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createTable($this->_tableName3, array(
            'id' => array(
                'type' => 'int',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'class_id' => array(
                'type' => 'int',
                'notnull' => true
            ),
            'user_id' => array(
                'type' => 'int',
                'notnull' => true
            ),
            'is_teacher' => array(
                'type' => 'smallint',
                'notnull' => true,
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName2, $this->_fkName2, array(
            'local' => 'school_id',
            'foreign' => 'id',
            'foreignTable' => $this->_tableName1,
            'onDelete' => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName3, $this->_fkName3a, array(
            'local' => 'class_id',
            'foreign' => 'id',
            'foreignTable' => $this->_tableName2,
            'onDelete' => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName3, $this->_fkName3b, array(
            'local' => 'user_id',
            'foreign' => 'id',
            'foreignTable' => 'users',
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName3, $this->_fkName3b);
        $this->dropForeignKey($this->_tableName3, $this->_fkName3a);

        $this->dropForeignKey($this->_tableName2, $this->_fkName2);

        $this->dropTable($this->_tableName3);
        $this->dropTable($this->_tableName2);
        $this->dropTable($this->_tableName1);
    }
}
