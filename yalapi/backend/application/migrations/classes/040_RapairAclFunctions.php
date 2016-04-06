<?php

class RapairAclFunctions extends Doctrine_Migration_Base
{

    public function up()
    {
        $sql=file_get_contents(preg_replace('/\.php$/','_up.sql',__FILE__));
        Doctrine_Manager::connection()->exec($sql);
        
    }
    public function down()
    {
 
        
    }
}



