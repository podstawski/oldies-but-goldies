<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CreateTableEmployee2 extends Doctrine_Migration_Base
{
    private $_tableName1 = 'employee_cv';
    private $_tableName2 = 'company_employee';

    private $_fkName2a = 'fk_company_employee_company';
    private $_fkName2b = 'fk_company_employee_cv';


    public function up()
    {
        $this->createTable($this->_tableName1, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'name' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'sex' => array(
                'type' => 'varchar(1)',
                'notnull' => true,
            ),
            'age' => array(
                'type' => 'smallint',
                'notnull' => true
            ),
            'experience' => array(
                'type' => 'smallint ',
                'notnull' => true
            ),
            'trade' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'education' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            ),
            'last_employer' => array(
                'type' => 'varchar(255)',
                'notnull' => true,
            )
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createTable($this->_tableName2, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'type' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'company_id' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
            'employee_cv_id' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
        ), array(
            'type' => 'InnoDB'
        ));

        $this->createForeignKey($this->_tableName2, $this->_fkName2a, array(
            'local' => 'company_id',
            'foreign' => 'id',
            'foreignTable' => 'company',
            'onDelete' => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName2, $this->_fkName2b, array(
            'local' => 'employee_cv_id',
            'foreign' => 'id',
            'foreignTable' => $this->_tableName1,
            'onDelete' => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName2, $this->_fkName2b);
        $this->dropForeignKey($this->_tableName2, $this->_fkName2a);

        $this->dropTable($this->_tableName2);
        $this->dropTable($this->_tableName1);
    }
}