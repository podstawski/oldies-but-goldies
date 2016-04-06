<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_Payment extends GN_Model_Payment
{
    protected $_rowClass = 'Model_PaymentRow';

    public static function getUsersFromCustom($customData) {
        $users = array();
        if (count($customData)) {
            $modelUsers = new Model_Users;
            foreach ($customData as $tmp) {
                list ($user_id, $fee_type) = explode(':', $tmp);
                $user = $modelUsers->find($user_id)->current();
                if ($user) {
                    $user->fee_type = $fee_type;
                    $users[] = $user;
                }
            }
        }
        return $users;
    }
}
