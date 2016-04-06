<?php

/**
* @author Radosław Szczepaniak <simer@gammanet.pl>
*/

class Model_Company extends Zend_Db_Table_Abstract
{
    const STATUS_OK       = 0;
    const STATUS_WARNING  = 1;
    const STATUS_BANKRUPT = 2;

    protected $_name = 'company';
    protected $_rowClass = 'Model_CompanyRow';

    protected $_dependentTables = array('Model_Employee', 'Model_Product', 'Model_Balance', 'Model_Warehouse', 'Model_Tax', 'Model_Commitment', 'Model_Loan', 'Model_UserToCompany');

    protected $_referenceMap = array(
        'Model_User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Model_User',
            'refColumns' => 'id'
        )
    );

    /**
     * @param array $companyData
     * @param Model_UserRow $user
     * @return Model_CompanyRow
     */
    public function createCompany(array $companyData, Model_UserRow $user)
    {
        $company = $this->createRow($companyData);
        $company->user_id = $user->id;
        $company->balance = Model_Param::get('company.initial_balance');
        $company->rounds_left = Model_Param::get('general.game_rounds');
        $company->status = Model_Company::STATUS_OK;
        $company->created = date('c');
        $company->save();

        $modelProduct = new Model_Product();
        foreach (Model_Product::$types as $type) {
            $modelProduct->insert(array(
                'company_id' => $company->id,
                'type'       => $type
            ));
        }

        $modelUserToCompany = new Model_UserToCompany;
        $userToCompany = $modelUserToCompany->createRow();
        $userToCompany->email = $user->email;
        $userToCompany->user_id = $user->id;
        $userToCompany->company_id = $company->id;
        $userToCompany->save();

        if ($user->role == Model_Player::ROLE_USER) {
            $modelRank = new Model_Rank;
            $rank = $modelRank->createRow();
            $rank->company_id = $company->id;
            $rank->score = $rank->employee_amount = $rank->quiz_score = 0;
            $rank->save();
        }

        return $company;
    }
}
