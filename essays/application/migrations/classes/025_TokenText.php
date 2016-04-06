<?php
class TokenText extends Doctrine_Migration_Base
{

    public function up()
    {
        Doctrine_Manager::connection()->exec("ALTER TABLE users ALTER token TYPE Text");
        Doctrine_Manager::connection()->exec("ALTER TABLE domains ALTER oauth_token TYPE Text");
    }
    
    public function postUp()
    {
        Doctrine_Manager::connection()->exec("UPDATE users SET token=NULL");
        Doctrine_Manager::connection()->exec("UPDATE domains SET oauth_token=NULL");
    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("ALTER TABLE users ALTER token TYPE Varchar(256)");
        Doctrine_Manager::connection()->exec("ALTER TABLE domains ALTER oauth_token TYPE Varchar(256)");
    }
}

