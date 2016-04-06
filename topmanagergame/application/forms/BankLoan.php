<?php
/**
 * Description
 * @author Radosław Szczepaniak
 */

class Form_BankLoan extends Form_Abstract
{
    /**
     * @var int
     */
    private $_bankID;

    /**
     * @var int
     */
    private $_rate;

    public function __construct($bankID)
    {
        $this->_bankID = $bankID;

        $modelLoan = new Model_Loan();
        $this->_rate = $modelLoan->getLoanRateForBank($bankID);

        parent::__construct();
    }

    public function init()
    {
        $company = Model_Player::getCompany();

        $today  = (object) Model_Day::gameDayIntoGameDate($company->getToday());
        $params = Model_Param::get('bank');
        $params = $params[$this->_bankID];

        if ($params['max_amount'] > 0) {
            $maxAmount = $params['max_amount'];
        } else {
            $taxModel = new Model_Tax();
            $numbers = $taxModel->fetchforCompanyYearMonth($company->id, $today->year, $today->month - 1);
            // SIM probably this is players' first month in game
            if ($numbers === null) {
                $maxAmount = 0;
            } else {
                $maxAmount = ($numbers['income'] - $numbers['costs']) * $params['income_multiplier'];
            }
            $maxAmount = max($params['min_amount'], $maxAmount);
        }

        $rate = new GN_Form_Element_PlainText('rate');
        $rate->setLabel('Oprocentowanie')
             ->setValue($this->_rate);

        $initialAmount = $params['initial_amount'];

        $amount = new Zend_Form_Element_Text('amount');
        $amount->setLabel('Wysokość kredytu')
               ->setRequired(true)
               ->addValidator(new Zend_Validate_NotEmpty())
               ->addValidator(new Zend_Validate_Int())
               ->setValue($initialAmount);

        $validator = new GN_Validate_GreaterOrEqualThan(0);
        $validator->setMessage("Proszę podać wysokość kredytu", GN_Validate_GreaterOrEqualThan::NOT_GREATER_NOR_EQUAL);
        $amount->addValidator($validator);

        $validator = new GN_Validate_LessOrEqualThan($maxAmount);
        $validator->setMessage("Maksymalna wysokość kredytu to %max% zł", GN_Validate_LessOrEqualThan::NOT_LESS_NOR_EQUAL);
        $amount->addValidator($validator);

        $maxDuration = $params['max_duration'];
        
        $duration = new Zend_Form_Element_Text('duration');
        $duration->setLabel('Liczba rat')
                 ->setRequired(true)
                 ->addValidator(new Zend_Validate_NotEmpty())
                 ->addValidator(new Zend_Validate_Int())
                 ->addValidator(new Zend_Validate_GreaterThan(0))
                 ->setValue(1);

        $validator = new Zend_Validate_GreaterThan(0);
        $validator->setMessage("Minimalny okres kredytowania to 1 miesiąc", Zend_Validate_GreaterThan::NOT_GREATER);
        $duration->addValidator($validator);

        $validator = new GN_Validate_LessOrEqualThan($maxDuration);
        $validator->setMessage("Maksymalny okres kredytowania to %max% miesięcy", GN_Validate_LessOrEqualThan::NOT_LESS_NOR_EQUAL);
        $duration->addValidator($validator);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Weź kredyt')
               ->setIgnore(true);

        $this->addElements(array($rate, $amount, $duration, $submit));
        $this->setElementFilters(array('StringTrim', 'StripTags'));
    }

    public function isValid($data)
    {
        $data['rate'] = $this->_rate;
        return parent::isValid($data);
    }
}
