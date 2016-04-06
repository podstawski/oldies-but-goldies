<?php

class MessageAttachment extends AclModel
{
    static $table_name = 'message_attachments';
    static $use_view = true;
    
    static $after_save = 'RunAcl';
    
    public function RunAcl()
    {
        foreach (MessageUser::getUsers($this->message_id) AS $user_id)
        {
            $this->grant(User::find($user_id)->role_id,$user_id,$this->id);
        }
    }    
    
}
