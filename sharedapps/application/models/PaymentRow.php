<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_PaymentRow extends GN_Model_PaymentRow
{
    /**
     * @return Model_UsersRow[]
     */
    public function getUsers()
    {
        return Model_Payment::getUsersFromCustom($this->getCustomData());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        if ($users = $this->getUsers()) {
            $data['users_info'] = array();
            $data['users_data'] = array();
            foreach ($this->getUsers() as $user) {
                $expire = date_format(new DateTime($user->expire), 'Y-m-d');
                $data['users_info'][] = sprintf('user: %s, fee: %s, expire: %s', $user->email, $user->fee_type == Model_Payment::FEE_MONTH ? 'month' : $user->fee_type == Model_Payment::FEE_YEAR ? 'year' : '??', $expire);
                $data['users_data'][] = array(
                    'email' => $user->email,
                    'type' => $user->fee_type,
                    'expire' => $expire,
                );
            }
        }
        return $data;
    }
}
