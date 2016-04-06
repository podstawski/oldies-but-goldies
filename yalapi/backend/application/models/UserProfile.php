<?php

class UserProfile extends AppModel
{
    static $table_name = 'user_profile';

    static $before_save = array('set_update_date');

    public function set_update_date()
    {
        $this->assign_attribute('update_date', date('Y-m-d H:i:s'));
    }
}
