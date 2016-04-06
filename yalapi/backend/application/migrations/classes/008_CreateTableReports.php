<?php
/**
 * Description
 * @author Tomasz Walotek
 */

class CreateTableReports extends Doctrine_Migration_Base
{
    private $_tableName = 'reports';
    private $_fkReportTemplates = 'fk_self_report_templates';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            // if not set, that means, its base template
            'parent_id' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'description' => array(
                'type' => 'varchar',
                'length' => 256,
                'notnull' => false,
            ),
            'path' => array(
                'type' => 'varchar(256)',
                'notnull' => false,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkReportTemplates, array(
            'local'         => 'parent_id',
            'foreign'       => 'id',
            'foreignTable'  => $this->_tableName,
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkReportTemplates);
        $this->dropTable($this->_tableName);
    }
}
