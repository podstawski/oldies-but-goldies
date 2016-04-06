<?php

class CreateTableProjects extends Doctrine_Migration_Base
{
    private $_tableName = 'projects';

    private $_addColTable1 = 'courses';
    private $_addColName1 = 'project_id';
    private $_fkName1 = 'fk_courses_project_id';

    private $_addColTable2 = 'reports';
    private $_addColName2 = 'project_id';
    private $_fkName2 = 'fk_reports_project_id';

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
            'code' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'text',
                'notnull' => false,
            ),
            'is_active' => array(
                'type' => 'smallint',
                'notnull' => true,
            ),
            'created_date' => array(
                'type' => 'date',
                'notnull' => true,
            ),
        ));

        $this->addColumn($this->_addColTable1, $this->_addColName1, 'integer', null, array(
            'notnull' => true
        ));

        $this->createForeignKey($this->_addColTable1, $this->_fkName1, array(
             'local'         => $this->_addColName1,
             'foreign'       => 'id',
             'foreignTable'  => $this->_tableName,
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->addColumn($this->_addColTable2, $this->_addColName2, 'integer', null, array(
            'notnull' => false
        ));

        $this->createForeignKey($this->_addColTable2, $this->_fkName2, array(
             'local'         => $this->_addColName2,
             'foreign'       => 'id',
             'foreignTable'  => $this->_tableName,
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
        $this->dropForeignKey($this->_addColTable1, $this->_fkName1);
        $this->dropForeignKey($this->_addColTable2, $this->_fkName2);
        $this->removeColumn($this->_addColTable1, $this->_addColName1);
        $this->removeColumn($this->_addColTable2, $this->_addColName2);
        $this->dropTable($this->_tableName);
    }
}
