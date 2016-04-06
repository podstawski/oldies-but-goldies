<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_GameServerUser extends Zend_Db_Table_Abstract
{
    protected $_name = 'game_server_user';
    protected $_rowClass = 'Model_GameServerUserRow';

    protected $_referenceMap = array(
        'Model_User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Model_User',
            'refColumns' => 'id'
        ),
        'Model_GameServer' => array(
            'columns' => 'game_server_id',
            'refTableClass' => 'Model_GameServer',
            'refColumns' => 'id'
        )
    );
}