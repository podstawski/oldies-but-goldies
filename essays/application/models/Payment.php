<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_Payment extends GN_Model_Payment
{
    const CUSTOM_TYPE_DOMAIN = 1;
    const CUSTOM_TYPE_USER = 2;

    protected $_rowClass = 'Model_PaymentRow';
}
