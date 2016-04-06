<?php

class AddPefsReport extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->execute("INSERT INTO reports (id, name, description, path) VALUES (11, 'Dane do PEFS', 'Dane do PEFS', 'pefs_for_all.jrxml')");
    }

    public function down()
    {

    }
}
