<?php

class CreateTableFiles extends Doctrine_Migration_Base
{
    private $_tableName1 = 'files';
    private $_fkName1 = 'fk_files_users';

    private $_tableName2 = 'survey_results';
    private $_tableName3 = 'survey_users';
    private $_fkName2 = 'survey_results_fk';
    private $_fkName3 = 'survey_users_fk';

    public function up()
    {
        $this->createTable($this->_tableName1, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'hash' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'size' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'created_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'downloads' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'filename' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => false,
            )
        ));

        $this->createForeignKey($this->_tableName1, $this->_fkName1, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName2, $this->_fkName2, array(
            'local'         => 'survey_id',
            'foreign'       => 'id',
            'foreignTable'  => 'surveys',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName3, $this->_fkName3, array(
            'local'         => 'survey_id',
            'foreign'       => 'id',
            'foreignTable'  => 'surveys',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_tableName1 . " ALTER COLUMN created_date SET DEFAULT NOW()");
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_tableName1 . " ALTER COLUMN downloads SET DEFAULT 0");
        Doctrine_Manager::connection()->execute("SELECT create_acl_table('$this->_tableName1')");
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute("SELECT drop_acl_table('$this->_tableName1')");
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_tableName1 . " ALTER COLUMN created_date DROP DEFAULT");
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_tableName1 . " ALTER COLUMN downloads DROP DEFAULT");
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName3, $this->_fkName3);
        $this->dropForeignKey($this->_tableName2, $this->_fkName2);

        $this->dropForeignKey($this->_tableName1, $this->_fkName1);
        $this->dropTable($this->_tableName1);
    }
}
