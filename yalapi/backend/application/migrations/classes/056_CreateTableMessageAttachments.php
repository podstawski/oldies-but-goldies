<?php

class CreateTableMessageAttachments extends Doctrine_Migration_Base
{
    private $_tableName = 'message_attachments';
    private $_fkName1 = 'fk_message_attachments_messages';
    private $_fkName2 = 'fk_message_attachments_files';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'message_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'file_id' => array(
                'type' => 'integer',
                'notnull' => true,
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'message_id',
             'foreign'       => 'id',
             'foreignTable'  => 'messages',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'file_id',
             'foreign'       => 'id',
             'foreignTable'  => 'files',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("SELECT create_acl_table('$this->_tableName')");
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute("SELECT drop_acl_table('$this->_tableName')");
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}
