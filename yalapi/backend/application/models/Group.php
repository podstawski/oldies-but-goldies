<?php

class Group extends AclModel
{
    static $use_view = true;
    
    static $has_many = array(
        array('group_users'),
        array('users', 'through' => 'group_users', 'select' => 'users.id, username, role_id, email, first_name, last_name, status, is_google'),
        array('courses')
    );

    static public function findAllCourseIds($group_id)
    {
        $cond = is_array($group_id) ? array('group_id=?',$group_id) : array('group_id IN (?)',$group_id);
        return self::getArrayOfFieldValues(Course::find('all',array('conditions' => $cond)));
    }

    public static function find_google_id(Group $group)
    {
        $base = $groupID = GN_User::cleanString($group->name, true);
        $counter = 1;
        while (1) {
            if (!Group::find_by_google_group_id($groupID)) {
                break;
            }
            $groupID = $base . '_' . $counter++;
        }
        return $groupID;
    }
}
