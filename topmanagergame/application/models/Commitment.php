<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_Commitment extends Zend_Db_Table_Abstract
{
    // ZUS
    const SOCIAL_INSURANCE = 12;
    // podatek dochodowy
    const INCOME_TAX = 13;
    // VAT
    const VAT_TAX = 14;
    // pensje pracowników
    const EMPLOYEE_PAYMENT = 15;
    // kredyt bankowy
    const BANK_LOAN = 16;
    // koszty stałe
    const FIXED_COST = 17;
    // inne koszty (?)
    const OTHER_COST = 18;
    // kary ogólne
    const PENALTY = 19;
    // dodatkowe koszty pracownicze
    const ADDITIONAL_EMPLOYEE_COST = 20;

    const PAY_ALL_PENALTY = 40;

    public static $commitmentTypes = array(
        self::SOCIAL_INSURANCE,
        self::EMPLOYEE_PAYMENT,
        self::INCOME_TAX,
        self::VAT_TAX,
        self::BANK_LOAN,
        self::FIXED_COST,
    );

    public static $penaltyTypes = array(
        30, 31, 32, 33, 34, 35
    );

    protected $_name = 'commitment';
    protected $_rowClass = 'Model_CommitmentRow';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}
