<?php

class Message extends AclModel
{
    static $table_name = 'messages';
    static $use_view = true;
    
    static $has_many = array(
        array('message_users')
    );

    /**
     * @var User
     */
    private $_sender;

    public function sender()
    {
        if ($this->_sender == null) {
            $this->_sender = User::find($this->sender_id, array('select' => 'id, CASE is_google WHEN 1 THEN email ELSE username END AS username, first_name, last_name'));
        }
        return $this->_sender;
    }
}
