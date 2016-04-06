<?php
/**
 * Keep changes in field values.
 * @author Radosław Benkel
 */

abstract class AclModel extends AppModel
{
    protected static function getArrayOfFieldValues($ar_array, $field = 'id')
    {
        $ids = array();
        if (is_array($ar_array)) foreach ($ar_array AS $a) $ids[] = $a->$field;
        return $ids;
    }
    
    protected static function _debug($debug,$revoke=null)
    {
      
        if (!is_null($revoke)) $debug=($revoke?'REVOKE ':'GRANT ').$debug;
        $debug.="\n";
        
        //echo $debug;
        //$plik=fopen('/tmp/acl_debug','a');fwrite($plik,$debug); fclose($plik);        
    }

    /**
     * @param $role
     * @param $user_id integer|array (of ids)
     * @param $object_id integer|array (of ids)
     * @param string $table_name
     * @param bool $revoke
     * @return mixed
     */
    public static function grant($role, $user_id, $object_id, $table_name = null, $revoke = false)
    {
        if (!$object_id && $object_id !== 0) return;
        if (!$table_name) $table_name = self::table_name();
        if ($table_name == 'acl') return; //do tej tabeli nie ma praw

        $token = (is_array($object_id) || $object_id > 0) ? 'specific' : 'all';

        if (is_array($user_id) && !count($user_id)) return;

        if (!$user_id) $user_id = Yala_User::getUid();
        if (!$user_id) return;

        if (is_array($user_id))
        {
            $username=array();
            foreach ($user_id AS $uid) $username[]=User::getUsername($uid);
        }
        else
            $username = array(User::getUsername($user_id));
            

        if (!is_array($object_id)) $object_id = array($object_id);

        $rights = is_null($role) ? AclRules::$ALL_RIGHTS : AclRules::getRules($token, $role, $table_name);

        if (is_array($rights)) return;

        $moreRightsAnd = '';
        if (strstr($rights, 'C')) $moreRightsAnd .= ' AND _insert';
        if (strstr($rights, 'R')) $moreRightsAnd .= ' AND _select';
        if (strstr($rights, 'U')) $moreRightsAnd .= ' AND _update';
        if (strstr($rights, 'D')) $moreRightsAnd .= ' AND _delete';


        foreach ($username AS $user)
        {
            foreach ($object_id AS $id)
            {
                if ($id > 0) {
                    $acl = Acl::find('first', array(
                        'conditions' => array(
                            'table_name = ? AND username = ? AND object_id = 0' . $moreRightsAnd,
                            $table_name, $user
                        )
                    ));
                    if (!is_null($acl)) continue;
                }

                $acl = Acl::find('first', array(
                    'conditions' => array(
                        'table_name = ? AND username = ? AND object_id = ?',
                        $table_name, $user, $id
                    )
                ));

                $crud = false;
                if (!is_null($acl)) {
                    $crud = $acl->_select && $acl->_insert && $acl->_update && $acl->_delete;

                }

    
                self::_debug("$rights ON $table_name ($id), user: $user".($crud?', CRUD=true':''),$revoke);
                

                if ($crud && $id > 0) continue;

                if (is_null($acl) && $revoke) continue;

                if (is_object($acl)) $acl->delete();

                if ($revoke) continue;

                $acl_attributes = array('table_name' => $table_name, 'username' => $user, 'object_id' => $id);

                if (!strlen($rights)) continue;
                if (!strstr($rights, 'C')) $acl_attributes['_insert'] = 'false';
                if (!strstr($rights, 'R')) $acl_attributes['_select'] = 'false';
                if (!strstr($rights, 'U')) $acl_attributes['_update'] = 'false';
                if (!strstr($rights, 'D')) $acl_attributes['_delete'] = 'false';

                Acl::create($acl_attributes);

            }
        }
    }

    public function specificRightsCount($username, $table_name)
    {

        $sql = "SELECT count(*) FROM acl WHERE table_name='$table_name' AND username='$username' AND object_id>0";
        return $this->connection()->query_and_fetch_one($sql);
    }

    public static function revoke($role, $user_id, $object_id, $table_name = null)
    {
        return self::grant($role, $user_id, $object_id, $table_name, true);
    }


    static $after_create = 'AfterCreateAcl';

    public function AfterCreateAcl()
    {
        $this->grant(null, null, $this->id);
    }


    public static function getTableName()
    {
        $table_name = isset(static::$table_name) ? static::$table_name : static::table()->table;
        if (isset(static::$use_view) && static::$use_view) $table_name .= '_view';
        return $table_name;
    }


    public static function find()
    {
        if (!isset(static::$use_view) || !static::$use_view) return call_user_func_array('parent::' . __FUNCTION__, func_get_args());
        $table_name = isset(static::$table_name) ? static::$table_name : static::table()->table;
        $args = func_get_args();


        if (count($args) == 1 && is_array($args[0]) && isset($args[0]['id'])) {
            $args = array($args[0]['id'], array());
        }

        foreach ($args AS $k => $v)
        {
            if (is_array($v)) {
                if (isset($v['from'])) {
                    if (!strstr($v['from'], $table_name . '_view')) $args[$k]['from'] = str_replace($table_name, $table_name . '_view', $args[$k]['from']);
                }
                else $args[$k]['from'] = $table_name . '_view';

                if (!isset($v['select'])) $args[$k]['select'] = $table_name . '_view.*';


                foreach (array('select', 'joins', 'order', 'group', 'conditions') AS $token)
                {
                    if (isset($v[$token])) {
                        if (is_array($v[$token]) && isset($v[$token][0])) {
                            foreach ($v[$token] AS $i => $subtoken)
                            {
                                if (!is_array($subtoken)) $args[$k][$token][$i] = str_replace($table_name . '.', $table_name . '_view.', $args[$k][$token][$i]);
                            }
                        }
                        else
                        {
                            $args[$k][$token] = str_replace($table_name . '.', $table_name . '_view.', $args[$k][$token]);
                        }
                    }
                }

            }

        }
        //static $count; if ($table_name=='lessons') {echo '<pre>'.print_r($args,true).'</pre>'; if (++$count==1) die();}

        return call_user_func_array('parent::' . __FUNCTION__, $args);


    }

}
