<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_Loan extends Zend_Db_Table_Abstract
{
    const CACHE_DATA_NAME = 'bank_rate';

    protected $_name = 'loan';
    protected $_rowClass = 'Model_LoanRow';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns'       => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns'    => 'id'
        )
    );

    public function getLoanRateForBank($bankID)
    {
        $cache = Zend_Registry::get('cache');
        $cacheData = $cache->load(self::CACHE_DATA_NAME);

        if (empty($cacheData)) {
            foreach (Model_Param::get('bank') as $id => $bankParams) {
                $cacheData[$id] = rand($bankParams['rate']['min'], $bankParams['rate']['max']);
            }
            $cache->save($cacheData, self::CACHE_DATA_NAME);
        }

        return $cacheData[$bankID];
    }

    public function createLoanForCompany(Model_CompanyRow $company, array $data)
    {
        $singleInstallmentRate = $data['rate'] / 12 / 100;
        $singleInstallmentAmount = $data['amount'] / $data['duration'];
        $totalInstallmentsAmount = 0;
        foreach (range(1, $data['duration']) as $installment) {
            $totalInstallmentsAmount = $totalInstallmentsAmount
                                     + $singleInstallmentAmount
                                     + ($data['amount'] - ($installment - 1) * $singleInstallmentAmount) * $singleInstallmentRate;
        }
        $singleInstallmentAmount = round($totalInstallmentsAmount / $data['duration']);
        $this->createRow(
            array(
                 'company_id'                => $company->id,
                 'bank_id'                   => $data['bank_id'],
                 'amount'                    => $data['amount'],
                 'rate'                      => $data['rate'],
                 'first_day'                 => $company->getToday(),
                 'months_amount'             => $data['duration'],
                 'single_installment_amount' => $singleInstallmentAmount
            )
        )->save();
    }
}
