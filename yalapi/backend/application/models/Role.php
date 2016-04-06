<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
class Role extends AclModel
{
    const SUPER_ADMIN = 0;
    const ADMIN = 1;
    const USER = 2;
    const PROJECT_LEADER = 3;
    const CENTER_LEADER = 4;
    const COACH = 5;
    
    public static function findAllUserIds($role_id)
    {
        $cond = is_array($role_id) ? array('role_id IN (?)',$role_id) : array('role_id=?',$role_id);
        
        $users=User::find('all',array('conditions' => $cond));
        $userIds=self::getArrayOfFieldValues($users);
    
        return $userIds;
    }
}
