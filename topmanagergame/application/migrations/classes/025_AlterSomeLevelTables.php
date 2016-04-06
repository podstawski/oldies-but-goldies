<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class AlterSomeLevelTables extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE product ALTER COLUMN technology SET DEFAULT 0');
        Doctrine_Manager::connection()->execute('ALTER TABLE product ALTER COLUMN quality SET DEFAULT 0');
        Doctrine_Manager::connection()->execute('ALTER TABLE employee ALTER COLUMN skill_level SET DEFAULT 0');
    }

    public function down()
    {
        Doctrine_Manager::connection()->execute('ALTER TABLE product ALTER COLUMN technology SET DEFAULT 1');
        Doctrine_Manager::connection()->execute('ALTER TABLE product ALTER COLUMN quality SET DEFAULT 1');
        Doctrine_Manager::connection()->execute('ALTER TABLE employee ALTER COLUMN skill_level SET DEFAULT 1');
    }
}