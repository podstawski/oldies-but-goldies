<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterProjectsAddIsDefault extends Doctrine_Migration_Base
{
    public function preUp()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'projects\')');
    }

    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE projects ADD COLUMN is_default SMALLINT NOT NULL DEFAULT 0');
    }

    public function postUp()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'projects\')');
    }

    public function preDown()
    {
        Doctrine_Manager::connection()->execute('SELECT drop_acl_view(\'projects\')');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE projects DROP COLUMN is_default');
    }

    public function postDown()
    {
        Doctrine_Manager::connection()->execute('SELECT create_acl_view(\'projects\')');
    }

}