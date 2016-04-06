<?php

require_once 'RestController.php';

class MessagesController extends RestController
{
//    const FOLDER_DELETE = 0;
//    const FOLDER_INBOX  = 1;
//    const FOLDER_OUTBOX = 2;
//    const FOLDER_TRASH  = 3;

    protected $_autoPager = false;

    public function init()
    {
        parent::init();
        ActiveRecord\Serialization::$DATETIME_FORMAT = 'd-m-Y H:i';
//        ini_set("display_errors", 0);
        if ($this->getRequest()->getParam('pager')) {
            $this->_pagedData(function(&$item) {
                $sender = sprintf('%s %s (%s)', $item->first_name, $item->last_name, $item->username);
                $item = $item->to_array();
                $item['sender'] = $sender;
            });
        }
    }

    public function getAction()
    {
        try {
            $row = $this->_getById();
            MessageUser::find_by_message_id_and_user_id($row->id, Yala_User::getUid())->mark_as_read(true);
            $sender = $row->sender();
            $sender = sprintf('%s %s (%s)', $sender->first_name, $sender->last_name, $sender->username);
            $attachments = File::connection()->query("SELECT message_attachments.id, hash, filename, size
                FROM message_attachments
                INNER JOIN files ON files.id = file_id
                WHERE message_id = " . $row->id
            )->fetchAll(PDO::FETCH_OBJ);
            $this->setRestResponseAndExit(array_merge(
                $row->to_array(),
                array(
                    'sender' => $sender,
                    'attachments' => $attachments ?: null
                )
            ), self::HTTP_OK);
        } catch (ActiveRecord\RecordNotFound $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }

    public function postAction()
    {
        $postData    = $this->_getRequestData('POST');
        $senderID    = Yala_User::getUid();
        $attachments = GN_Upload::upload(array(
            'destination' => APPLICATION_PATH . '/../upload',
            'name' => 'file'
        ), $senderID);
        
        $postData['sender_id'] = $senderID;
        $postData['send_date'] = date('d-m-Y H:i:s');

        $groups  = $this->_getParamArray('groups', $postData);
        $users   = $this->_getParamArray('users', $postData);
        $forward = isset($postData['_forward']);

        unset($postData['groups'], $postData['users'], $postData['_forward']);

        try {
            $message = Message::create($postData);

            foreach (array_unique($groups) as $groupID) {
                if (($group = Group::find($groupID)) != null) {
                    foreach ($group->users as $user) {
                        $users[] = $user->id;
                    }
                }
            }

            if (!empty($users)) {
                foreach (array_unique($users) as $userID) {
                    MessageUser::create(array(
                        'message_id' => $message->id,
                        'user_id' => $userID,
                        'folder' => MessageUser::FOLDER_INBOX
                    ));
                }

                MessageUser::create(array(
                    'message_id' => $message->id,
                    'user_id' => $senderID,
                    'folder' => MessageUser::FOLDER_OUTBOX
                ));

                if ($forward && ($oldAttachments = MessageAttachment::find_by_message_id($message->id)) != null) {
                    $attachments = array_merge($attachments, $oldAttachments);
                }

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        MessageAttachment::create(array(
                            'message_id' => $message->id,
                            'file_id' => $attachment->id
                        ));
                    }
                }
            }

            $this->setRestResponseAndExit(null, self::HTTP_CREATED);
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function deleteAction()
    {
        try {
            $ids = $this->_getParamArray('id');

            foreach ($ids as $messageID) {
                if (($message = MessageUser::find($messageID)) != null) {
                    $message->delete();
                }
            }
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_ACCEPTABLE);
        }
    }

    protected function _getPagerOptionsForModel()
    {
        $this->_modelName = 'MessageUser';

        $options   = parent::_getPagerOptionsForModel();
        $tableName = $options['from'] = $this->_getTableNameFromModelClass($this->_modelName);

        if (!array_key_exists('total_records', $options)) {
            $options['select'] = "$tableName.id, message_id, sender_id, subject, send_date, recipient_list, read_date,
                                  CASE is_google WHEN 1 THEN email ELSE username END AS username, first_name, last_name, COALESCE((SELECT 1 FROM message_attachments WHERE message_id = $tableName.message_id LIMIT 1), 0) AS attachments";
        }

        $options['joins']  = "INNER JOIN messages ON messages.id = message_id
                              INNER JOIN users ON users.id = sender_id";

        if (array_key_exists('order', $options)) {
            $options['order'] = str_replace('sender ', 'username ', $options['order']);
        }

        $condition = 'user_id = ' . Yala_User::getUid();
        if (array_key_exists('conditions', $options)) {
            $options['conditions'] .= ' AND ' . $condition;
        } else {
            $options['conditions'] = $condition;
        }
        
        return $options;
    }
}

