<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_Message extends Zend_Db_Table_Abstract
{
    const INBOX  = 1;
    const OUTBOX = 2;

    protected $_name = 'message';
    protected $_rowClass = 'Model_MessageRow';

    protected $_dependentTables = array('Model_MessageUser');

    protected $_referenceMap = array(
        'Model_User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Model_User',
            'refColumns' => 'id'
        )
    );

    public static function isUnread($row)
    {
        return $row->folder == self::INBOX && empty($row->read_date);
    }
}
