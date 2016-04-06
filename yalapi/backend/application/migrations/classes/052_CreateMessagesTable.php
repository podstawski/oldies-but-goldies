<?php

class CreateMessagesTable extends Doctrine_Migration_Base
{
    private $_table1 = 'messages';
    private $_table2 = 'message_users';

    private $_fk1 = 'fk_messages_users';
    private $_fk2 = 'fk_messages_users_messages';
    private $_fk3 = 'fk_messages_users_users';

    public function up()
    {
        $this->createTable($this->_table1, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'subject' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'body' => array(
                'type' => 'text',
                'notnull' => true,
            ),
            'send_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'sender_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'recipient_list' => array(
                'type' => 'text',
                'notnull' => true
            )
        ));

        $this->createForeignKey($this->_table1, $this->_fk1, array(
             'local'         => 'sender_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createTable($this->_table2, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'message_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'read_date' => array(
                'type' => 'timestamp',
                'notnull' => false,
            ),
            'folder' => array(
                'type' => 'smallint',
                'notnull' => true
            )
        ));

        $this->createForeignKey($this->_table2, $this->_fk2, array(
             'local'         => 'message_id',
             'foreign'       => 'id',
             'foreignTable'  => 'messages',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_table2, $this->_fk3, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_table1 . " ALTER COLUMN send_date SET DEFAULT NOW()");
        Doctrine_Manager::connection()->execute("ALTER TABLE " . $this->_table2 . " ALTER COLUMN folder SET DEFAULT 1");

        Doctrine_Manager::connection()->execute("SELECT create_acl_table('" . $this->_table1 . "')");
        Doctrine_Manager::connection()->execute("SELECT create_acl_table('" . $this->_table2 . "')");

        Doctrine_Manager::connection()->execute("SELECT create_acl_table('exams')");
        Doctrine_Manager::connection()->execute("SELECT create_acl_table('exam_grades')");
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute("SELECT drop_acl_table('" . $this->_table1 . "')");
        Doctrine_Manager::connection()->execute("SELECT drop_acl_table('" . $this->_table2 . "')");
    }

    public function down()
    {
        $this->dropForeignKey($this->_table2, $this->_fk3);
        $this->dropForeignKey($this->_table2, $this->_fk2);
        $this->dropTable($this->_table2);

        $this->dropForeignKey($this->_table1, $this->_fk1);
        $this->dropTable($this->_table1);
    }
}
