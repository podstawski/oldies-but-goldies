<?php

class CreateTableUserProfile extends Doctrine_Migration_Base
{
    private $_tableName = 'user_profile';
    private $_fkName1 = 'fk_user_profile_users';
    private $_fkName2 = 'fk_user_profile_poland';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'sex' => array(
                'type' => 'varchar(1)'
            ),
            'national_identity' => array(
                'type' => 'varchar(256)'
            ),
            'address_city' => array(
                'type' => 'varchar(256)'
            ),
            'address_zip_code' => array(
                'type' => 'varchar(256)'
            ),
            'address_street' => array(
                'type' => 'varchar(256)'
            ),
            'poland_id' => array(
                'type' => 'integer'
            ),
            'phone_number' => array(
                'type' => 'varchar(256)'
            ),
            'fax_number' => array(
                'type' => 'varchar(256)'
            ),
            'mobile_number' => array(
                'type' => 'varchar(256)'
            ),
            'birth_date' => array(
                'type' => 'date'
            ),
            'birth_place' => array(
                'type' => 'varchar(256)'
            ),
            'work_name' => array(
                'type' => 'varchar(256)'
            ),
            'work_city' => array(
                'type' => 'varchar(256)'
            ),
            'work_zip_code' => array(
                'type' => 'varchar(256)'
            ),
            'work_street' => array(
                'type' => 'varchar(256)'
            ),
            'work_tax_identification_number' => array(
                'type' => 'varchar(256)'
            ),
            'tax_identification_number' => array(
                'type' => 'varchar(256)'
            ),
            'tax_office' => array(
                'type' => 'varchar(256)'
            ),
            'tax_office_address' => array(
                'type' => 'varchar(256)'
            ),
            'identification_name' => array(
                'type' => 'varchar(256)'
            ),
            'identification_number' => array(
                'type' => 'varchar(256)'
            ),
            'identification_publisher' => array(
                'type' => 'varchar(256)'
            ),
            'father_name' => array(
                'type' => 'varchar(256)'
            ),
            'mother_name' => array(
                'type' => 'varchar(256)'
            ),
            'nfz' => array(
                'type' => 'varchar(256)'
            ),
            'bank' => array(
                'type' => 'varchar(256)'
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'poland_id',
             'foreign'       => 'id',
             'foreignTable'  => 'poland',
             'onDelete'      => 'NO ACTION',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_table(\'' . $this->_tableName . '\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_table(\'' . $this->_tableName . '\')');
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropTable($this->_tableName);
    }
}