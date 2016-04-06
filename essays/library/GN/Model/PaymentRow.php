<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Model_PaymentRow extends Zend_Db_Table_Row
{
    /**
     * @var array
     */
    public $payment_data;

    public function init()
    {
        parent::init();

        if ($this->data) {
            $this->payment_data = json_decode($this->data, true);
        }
    }

    public function getCustomData() {
        return unserialize($this->custom_data);
    }

    public function setCustomData($customData) {
        $this->custom_data = serialize($customData);
    }

    /**
     * @return mixed
     */
    public function save()
    {
        if ($this->payment_data)
            $this->data = json_encode($this->data);

        return parent::save();
    }

    /**
     * @return Model_UsersRow
     */
    public function getPayer()
    {
        return $this->findParentRow('Model_Users', 'Payer');
    }
}
