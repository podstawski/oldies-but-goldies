<?php

class ReplaceAclFunctions extends Doctrine_Migration_Base
{
    public function up()
    {
        Doctrine_Manager::connection()->exec(
            file_get_contents(preg_replace('/\.php$/', '_up.sql', __FILE__))
        );
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec(
            file_get_contents(preg_replace('/\.php$/', '_down.sql', __FILE__))
        );
    }
}
