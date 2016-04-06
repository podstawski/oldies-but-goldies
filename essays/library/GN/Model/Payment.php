<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Model_Payment extends Zend_Db_Table_Abstract
{
    protected $_name = 'payment';

    const TYPE_PAYPAL = 1;
    const TYPE_PAYU = 2;

    const FEE_MONTH = 1;
    const FEE_YEAR  = 2;

    /**
     * @var array
     */
    public static $types = array(
        self::TYPE_PAYPAL => 'paypal',
        self::TYPE_PAYU => 'payu',
    );


    protected $_referenceMap = array(
        'Payer' => array(
            'columns' => 'payer_id',
            'refTableClass' => 'Model_Users',
            'refColumns' => 'id'
        ),
    );

    /**
     * @param string $custom_id
     * @return Model_PaymentRow
     */
    public function findCustom($custom_id)
    {
        $tmp = array();
        $tmp['custom_id = ?'] = $custom_id;
        return $this->fetchRow($tmp);
    }

    /**
     * @param string $transaction_id
     * @return Model_PaymentRow
     */
    public function findTransaction($transaction_id, $status=null)
    {
        $tmp = array();
        $tmp['transaction_id = ?'] = $transaction_id;
        if ($status)
            $tmp['status = ?'] = $status;
        return $this->fetchRow($tmp);
    }

    public function newRow($custom,$type,$user_id,$amount)
    {
        $payment = $this->createRow();
        $payment->type = $type;
        $payment->payer_id = $user_id;
        $payment->amount = $amount;
        $payment->custom_id = md5(serialize($custom) . microtime(true) . mt_rand());
        $payment->setCustomData($custom);
        $payment->save();
        return $payment;
    }

}
