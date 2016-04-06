<?php

class AclPK extends Doctrine_Migration_Base
{
    private $_tableName = 'acl';

    public function up()
    {
    	Doctrine_Manager::connection()->exec('ALTER TABLE acl ADD PRIMARY KEY (id); DROP INDEX acl_key; CREATE UNIQUE INDEX acl_key ON acl (table_name, username, object_id); CREATE INDEX acl_updated_key ON acl(updated)');
    }

    public function down()
    {
    	Doctrine_Manager::connection()->exec('ALTER TABLE acl DROP CONSTRAINT acl_pkey;  DROP INDEX acl_key; CREATE INDEX acl_key ON acl (table_name, username); DROP INDEX acl_updated_key');
    }

}



