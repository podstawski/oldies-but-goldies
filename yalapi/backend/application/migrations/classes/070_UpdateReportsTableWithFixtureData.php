<?php

class UpdateReportsTableWithFixtureData extends Doctrine_Migration_Base
{
    private $_tableName = 'reports';

    public function up()
    {
        Doctrine_Manager::connection()->execute("TRUNCATE TABLE $this->_tableName");
    }

    public function postUp()
    {

        $data = include __DIR__ . '/../fixtures/reports.php';

        for ($i = 1, $length = count($data); $i < $length; $i++) {
            $row = $data[$i];
            $sql = sprintf("INSERT INTO %s (id, name, path, description) VALUES (%d, '%s', '%s', '%s')",
                           $this->_tableName, $i, $row[1], $row[2], $row[4]);

            Doctrine_Manager::connection()->execute($sql);
        }

        $sql = sprintf("SELECT setval('%s', %d)", $this->_tableName . '_id_seq', $length - 1);
        Doctrine_Manager::connection()->execute($sql);
    }

    public function down()
    {
    }
}
