<?php
/**
 * @author Radosław Benkel
 */
 
abstract class Playgine_TaskFactory extends Playgine_TaskFactoryAbstract
{
    protected static $_type = 'Task';
    
    /**
     * Maps id form database to correct task class
     * @var array
     */
    protected static $_classMap = array(
//        1   => 'HireEmployee',
        2   => 'FireEmployee',
        3   => 'TrainEmployee',
        4   => 'UpgradeTechnology',
        5   => 'UpgradeQuality',
        6   => 'AssignEmployee',
//        7   => 'RevokeEmployee',
        8   => 'Production',
        9   => 'PutOnMarket',
        10  => 'NpcBuy',
        11  => 'CalculateCommitments',
        12  => 'PaySocialInsurance',
        13  => 'PayIncomeTax',
        14  => 'PayVatTax',
        15  => 'PayEmployeePayment',
        16  => 'PayBankLoan',
        17  => 'PayFixedCosts',
//        18  => 'PayOtherCosts',
//        19  => 'PayPenalty',
//        20  => 'PayAdditionalCosts',
        21  => 'CalculateInterests',
        22  => 'FireFiredEmployees',
        23  => 'AnalystSummary',
        24  => 'CreateBankLoan',
        25  => 'PayCommitments',
        26  => 'CountCostsAndIncome',
        27  => 'ChangeMarketPrice',
        28  => 'UpdateRank',
        29  => 'Recruit',
        30  => 'PaySocialInsurancePenalty',
        31  => 'PayIncomeTaxPenalty',
        32  => 'PayVatTaxPenalty',
        33  => 'PayEmployeePaymentPenalty',
        34  => 'PayBankLoanPenalty',
        35  => 'PayFixedCostsPenalty',
//        36  => 'PayOtherCostsPenalty',
//        37  => 'PayAdditionalCostsPenalty',
        38  => 'ProductionOutput',
        39  => 'MidnightCleanup',
        40  => 'PayAllPenalty',
    );
}
