<?php

class Acl extends AclModel
{
    static $table_name = 'acl';
    static $connection = 'admin';

    static $last_change = null;

    public static function after_startup()
    {
        self::$last_change = self::connection()->query_and_fetch_one('SELECT updated FROM ' . self::$table_name . ' ORDER BY updated DESC LIMIT 1');
    }

    public static function before_end()
    {
        $configFull = parse_ini_file(__DIR__ . '/../configs/application.ini', true);
    
        if (file_exists(__DIR__ . '/../configs/local.ini')) {
            $localConfig = parse_ini_file(__DIR__ . '/../configs/local.ini');
            $configFull['production'] = array_merge($configFull['production'], $localConfig);
        }

        $config = $configFull['production'];
        
        $find_opt=array('group'=>'table_name','select'=>'table_name');
        if (!is_null(self::$last_change)) $find_opt['conditions'] = array("updated > '".self::$last_change."'");

        $acl=self::find('all',$find_opt);

        if (is_null($acl) || !count($acl)) return;
        
        foreach ($acl AS $a)
        {
            $table_name=$a->table_name;
            $sql="TRUNCATE ${table_name}_acl;";
            $sql.="INSERT INTO ${table_name}_acl (username) VALUES ('".$config['db.username']."');";
            $sql.="DELETE FROM acl WHERE table_name='${table_name}' AND object_id>0 AND object_id NOT IN (SELECT $table_name.id FROM $table_name WHERE $table_name.id=acl.object_id);";
            $sql.="INSERT INTO ${table_name}_acl (object_id,username,_select,_update,_insert,_delete) SELECT object_id,CURRENT_DATABASE()||'_'||username,_select,_update,_insert,_delete FROM acl WHERE table_name='$table_name';";


            self::connection()->connection->exec($sql);

        }
    }
    
    public static function truncate()
    {
        self::connection()->connection->exec('DELETE FROM acl WHERE object_id=0');
    }
    
    public static function recreateDefault()
    {
        self::truncate();

        foreach (AclRules::getRules('all') AS $role_id=>$rules)
        {
            $users=Role::findAllUserIds($role_id);
            foreach (array_keys($rules) AS $table)
            {
                self::grant($role_id,$users,0,$table);
            }
        }
    }
}
