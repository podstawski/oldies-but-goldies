<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class CustomProjectFields extends Doctrine_Migration_Base
{
    public function preUp()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'projects\')');
    }

    public function up()
    {
        $this->addColumn('projects', 'extra_fields', 'text', null, array('notnull' => false));

        $this->createTable('user_profile_extra', array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'field_name' => array(
                'type' => 'varchar(32)',
                'notnull' => true,
            ),
            'field_value' => array(
                'type' => 'varchar(255)',
                'notnull' => false,
            ),
        ));

        $this->createForeignKey('user_profile_extra', 'fk_user_profile_extra_users', array(
            'local'         => 'user_id',
            'foreign'       => 'id',
            'foreignTable'  => 'users',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));

    }

    public function postUp()
    {
        Doctrine_Manager::connection()->exec('CREATE UNIQUE INDEX idx_user_profile_extra_user_field ON user_profile_extra(user_id, field_name)');
        Doctrine_Manager::connection()->exec(<<<SQL

CREATE OR REPLACE FUNCTION extra_field(integer, varchar) RETURNS varchar
AS $$
    SELECT field_value FROM user_profile_extra WHERE user_id = $1 AND field_name = $2;
$$
LANGUAGE sql;

SQL
        );

        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'projects\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'projects\')');
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec('DROP FUNCTION IF EXISTS extra_field(integer, varchar)');
        $this->dropTable('user_profile_extra');
        $this->removeColumn('projects', 'extra_fields');
    }

    public function postDown()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'projects\')');
    }
}