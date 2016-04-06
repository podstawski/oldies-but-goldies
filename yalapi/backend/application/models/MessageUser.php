<?php

class MessageUser extends AclModel
{
    const FOLDER_DELETE = 0;
    const FOLDER_INBOX  = 1;
    const FOLDER_OUTBOX = 2;
    const FOLDER_TRASH  = 3;

    static $table_name = 'message_users';
    static $use_view = false;

    static $before_create = array('check_read_date');

    static $after_save   = 'RunAcl';

    static $belongs_to = array(
        array('message')
    );
    
    static public function getUsers($message_id)
    {
        return getArrayOfFieldValues(self::find('all',array('conditions'=>array('message_id = ?', $message_id))), 'user_id');
    }
    
    public function RunAcl()
    {
        if ($this->user_id == Yala_User::getUid() ) return; // creator will get rights anyway
        $this->grant(Role::USER, $this->user_id, $this->message_id, 'messages');
        $this->grant(Role::USER, $this->user_id, $this->id);
    }

    public function mark_as_read($autosave = false)
    {
        if ($this->read_date == null) {
            $date = new \DateTime();
            $this->read_date = $date->format('Y-m-d H:i:s');

            if ($autosave) {
                $this->save();
            }
        }
    }

    public function check_read_date()
    {
        if ($this->folder == self::FOLDER_OUTBOX) {
            $this->mark_as_read();
        }
    }

    public function delete()
    {
        if ($this->folder == self::FOLDER_TRASH) {
            return parent::delete();
        } else {
            $this->folder = self::FOLDER_TRASH;
            return $this->save();
        }
    }
}
