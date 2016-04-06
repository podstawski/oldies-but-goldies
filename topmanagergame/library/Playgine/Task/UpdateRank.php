<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gmail.com>
 */

class Playgine_Task_UpdateRank extends Playgine_Task_Abstract
{
    /**
     * @var bool
     */
    protected $_storeMessage = false;

    public function run()
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $db->query('TRUNCATE rank');
        $db->query('ALTER SEQUENCE rank_id_seq RESTART WITH 1');

        $roles = array();
        $roles[] = Model_Player::ROLE_USER;
        if (Model_Param::get('ranking.include_admins'))
            $roles[] = Model_Player::ROLE_ADMIN;

        $taxVat = Model_Param::get('tax.vat');

        $db->query($q = 'INSERT INTO rank (company_id, score, employee_amount, quiz_score) SELECT
                company.id,
                CASE company.status WHEN ' . Model_Company::STATUS_BANKRUPT. ' THEN -1 ELSE COALESCE(CAST(company.balance + COALESCE(parts_score / (1 + ' . $taxVat . '), 0) + COALESCE(spendings_score / (1 + ' . $taxVat . '), 0) - COALESCE(commitments_score, 0) - COALESCE(loans_score, 0) AS INT), 0) END score,
                CASE company.status WHEN ' . Model_Company::STATUS_BANKRUPT. ' THEN 0 ELSE COALESCE(employees_amount, 0) END employees_amount,
                0 quiz_score
            FROM company
            INNER JOIN users ON users.id = company.user_id
            LEFT JOIN school_class_member ON school_class_member.user_id = users.id
            LEFT JOIN (
                SELECT company_id, SUM(parts_cost * amount) AS parts_score
                FROM warehouse
                GROUP BY company_id
            ) parts ON parts.company_id = company.id
            LEFT JOIN (
                SELECT company_id, -1 * SUM(amount) AS spendings_score
                FROM balance
                WHERE ' . $db->quoteInto('type IN (?)', array(
                    Playgine_TaskFactory::getTaskTypeByName('UpgradeQuality'),
                    Playgine_TaskFactory::getTaskTypeByName('UpgradeTechnology'),
                ), Zend_Db::PARAM_INT) . '
                GROUP BY company_id
            ) spendings ON spendings.company_id = company.id
            LEFT JOIN (
                SELECT company_id, SUM(cost) AS commitments_score
                FROM commitment
                WHERE ' . $db->quoteInto('type NOT IN (?)', array(
                    Playgine_TaskFactory::getTaskTypeByName('PayBankLoan'),
                ), Zend_Db::PARAM_INT) . '
                GROUP BY company_id
            ) commitments ON commitments.company_id = company.id
            LEFT JOIN (
                SELECT company_id, SUM(single_installment_amount * (months_amount - months_paid)) AS loans_score
                FROM loan
                WHERE months_paid < months_amount
                GROUP BY company_id
            ) loans ON loans.company_id = company.id
            LEFT JOIN (
                SELECT SUM(amount) as employees_amount, company_id
                FROM employee
                GROUP BY company_id
            ) employees ON employees.company_id = company.id
            WHERE users.is_hidden = 0
            AND ' . $db->quoteInto('users.role IN (?)', $roles, Zend_Db::PARAM_INT) . '
            ' . (Model_Param::get('ranking.include_teachers') == false ? ' AND COALESCE(school_class_member.is_teacher, 0) = 0' : '') . '
            ORDER BY score DESC, employees_amount DESC, quiz_score DESC
        ');

        $db->query('TRUNCATE rank_school');
        $db->query('ALTER SEQUENCE rank_school_id_seq RESTART WITH 1');

        $db->query('INSERT INTO rank_school (school_id, score, employee_amount, quiz_score) SELECT
                school.id,
                COALESCE(SUM(rank.score), 0) score,
                COALESCE(SUM(rank.employee_amount), 0) employee_amount,
                COALESCE(SUM(rank.quiz_score), 0) quiz_score
            FROM school
            LEFT JOIN school_class ON school_class.school_id = school.id
            LEFT JOIN school_class_member ON school_class_member.class_id = school_class.id
            LEFT JOIN users ON users.id = school_class_member.user_id AND is_teacher = 0 AND status = 1
            LEFT JOIN company ON company.user_id = users.id
            LEFT JOIN rank ON rank.company_id = company.id
            GROUP BY school.id
            ORDER BY score DESC, employee_amount DESC, quiz_score DESC
        ');

        Model_GameData::setData(Model_GameData::LAST_RANK_UPDATE, time());
    }
}