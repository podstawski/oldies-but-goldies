<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_MessageUser extends Zend_Db_Table_Abstract
{
    protected $_name = 'message_users';
    protected $_rowClass = 'Model_MessageUserRow';

    protected $_referenceMap = array(
        'Model_User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Model_User',
            'refColumns' => 'id'
        ),
        'Model_Message' => array(
            'columns' => 'message_id',
            'refTableClass' => 'Model_Message',
            'refColumns' => 'id'
        )
    );

    /**
     * @return Zend_Db_Select
     */
    public function selectForUser()
    {
        return $this->select(true)
            ->setIntegrityCheck(false)
            ->join('message', 'message.id = message_users.message_id', array('subject', 'body', 'send_date', 'recipient_list'))
            ->join('users', 'users.id = message.sender_id', array('sender_username' => 'username'))
            ->where('message_users.user_id = ?', Model_Player::getUserId(), Zend_Db::PARAM_INT);
    }

    /**
     * @param int $messageId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function fetchMessageById($messageId)
    {
        $message = $this->fetchRow(
            $this->selectForUser()
                 ->where('message.id = ?', $messageId, Zend_Db::PARAM_INT)
        );
        if ($message) {
            $message->setReadOnly(false);
            if (empty($message->read_date)) {
                $tmp = clone $message;
                $tmp->read_date = date('c');
                $tmp->save();
            }
        }
        return $message;
    }

    /**
     * @param int $folder
     * @param bool $onlySelect
     * @return Zend_Db_Select|Zend_Db_Table_Rowset_Abstract
     */
    public function fetchMessagesByFolder($folder, $onlySelect = false)
    {
        $select = $this->selectForUser()
            ->where('message_users.folder = ?', $folder, Zend_Db::PARAM_INT)
            ->order('message.send_date DESC');
        if ($onlySelect) {
            return $select;
        }
        return $this->fetchAll($select);
    }

    public function getNewMessagesCount()
    {
        $select = $this->selectForUser()
            ->where('folder = ?', Model_Message::INBOX, Zend_Db::PARAM_INT)
            ->where('read_date IS NULL');

        return $this->fetchAll($select)->count();
    }
}
