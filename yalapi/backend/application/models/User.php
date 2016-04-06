<?php
/**
 * Description
 * @author Radosław Benkel 
 */
 
class User extends AclModel
{
    const PASSWORD_SALT = '8bfcc556e078c8eb8b8c3d64bdbb855d';

    static $belongs_to = array('role');

    static $has_many = array(
        array('group_users'),
        array('project_leaders', 'class_name' => 'ProjectLeaders'),
    );

    static $validates_presence_of = array(
        array('username'),
        array('role_id')
    );

    public static function getUsersWithoutAdmin($username)
    {
        $users = self::find('all',array('conditions' => array('username <> \'' . $username . '\''),'from'=>'users'));
        array_walk($users, function(&$user){
            $user = $user->to_array();
        });
        
        return $users;
    }

    public function get_full_name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function get_full_name_with_login()
    {
        $username = $this->is_google
                  ? $this->email
                  : $this->username;

        return sprintf('%s (%s)', $this->get_full_name(), $username);
    }

    public function to_array(array $options = array())
    {
        $userData = parent::to_array($options);

        if (isset($this->is_google) && $this->is_google) {
            $userData['domain'] = @array_pop(explode('@', $this->email));
        } else {
            $userData['domain'] = null;
        }

        return $userData;
    }
    
    static public function getRoleId($id)
    {
        $result=self::find_by_pk($id,array('from'=>'users'));
        return $result->role_id;       
    }

    static public function getUsername($id)
    {
        if (is_array($id))
        {
            if (!count($id)) return array();
            $users=self::find('all',array('conditions' => array('id IN (?)',$id),'from'=>'users'));
            return self::getArrayOfFieldValues($users,'username');
        }
        else
        {
            $result=self::find_by_pk($id,array('from'=>'users'));
            return $result->username;
        }
    }

    static $after_save = 'RunAcl';


    public function RunAcl($force=false)
    {
        $profile=UserProfile::first(array('conditions'=>array('user_id = ?',$this->id)));
        if ($profile) {
            $this->grant($this->role_id,$this->id,$profile->id,'user_profile');
        }
        
        if ($force || $this->field_has_changed('role_id')) {
            if ($force) {
                $old=null;
                $new=$this->role_id;
            } else {
                list($old,$new)=$this->get_field_change('role_id');
            }

            if ($old && is_array(AclRules::getRules('all',$old))) {
                foreach (array_keys(AclRules::getRules('all',$old)) AS $table) {
                    $this->revoke($old,$this->id,0,$table);
                }
            }

            if (is_array(AclRules::getRules('all',$new))) {
                foreach (array_keys(AclRules::getRules('all',$new)) AS $table) {
                    $this->grant($new,$this->id,0,$table);             
                }
            }
        }
    }

    /**
     * @static
     * @param array $userData
     * @param int $role_id
     * @return User
     * @throws Exception
     */
    static public function createUser(array $userData, $role_id = Role::USER)
    {
        if (Yala_User::getRoleId() != Role::ADMIN) {
            throw new Exception('you do not have rights to create users');
        }

        $input = new Zend_Filter_Input(array(
            '*' => 'StripTags',
        ), array(
            'username' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL,
            ),
            'plain_password' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL,
            ),
            'email' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL,
                Zend_Filter_Input::ALLOW_EMPTY => true,
                new Zend_Validate_EmailAddress()
            ),
            'first_name' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'last_name' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED,
            ),
            'is_google' => array(
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_OPTIONAL,
                Zend_Filter_Input::ALLOW_EMPTY => true,
                Zend_Filter_Input::DEFAULT_VALUE => 0,
            )
        ), $userData);

        $input->process();
        
        $username       = $input->username ?: strtolower(array_shift(explode('@', $input->email)));
        $plain_password = $input->plain_password ?: self::generatePassword($input->email);
        $is_google      = $input->is_google ? 1 : 0;
        $flag           = $input->is_google || $role_id == Role::ADMIN ? 'FALSE' : 'TRUE';

        $values = array(
            $username, $plain_password, $input->getUnescaped('first_name'), $input->getUnescaped('last_name'), $role_id, Yala_User::getUsername()
        );

        self::connection()->query("SELECT create_user(?, ?, ?, ?, $flag, ?) WHERE acl_has_right('users', 0, 'insert', ?)", $values);
        
        $userRow = self::find_by_username($username);
        if (!$userRow) {
            throw new Exception('you do not have rights to create users');
        }
        if ($input->email) {
            $userRow->email = $input->email;
        }
        $userRow->is_google = $is_google;
        // SIM save with no validation, coz email may not be saved...
        $userRow->save(false);
        $userRow->RunAcl(true);
        return $userRow;
    }

    public static function generatePassword($phrase, $length = 8)
    {
        return substr(sha1($phrase . self::PASSWORD_SALT), 0, $length);
    }
}
