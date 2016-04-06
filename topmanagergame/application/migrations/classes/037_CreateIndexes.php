<?php

class CreateIndexes extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idx_company_employee_fk' => 'company_employee (company_id,employee_cv_id)',
        'idx_balance_fk' => 'balance (company_id)',
        'idx_commitment_fk' => 'commitment (company_id)',
        'idxu_company_fk' => 'company (user_id)',
        'idx_edu_params_self' => 'edu_params (parent_id)',
        'idx_employee_fk' => 'employee (company_id)',
        'idxu_game_data_name' => 'game_data (key)',
        'idxu_game_server_name' => 'game_server (name,url)',
        'idx_loan_fk' => 'loan (company_id)',
        'idxu_map_params_name' => 'map_params (type)',
        'idx_message_fk' => 'message (sender_id)',
        'idxu_message_users_fk' => 'message_users (message_id,user_id)',
        'idx_product_fk' => 'product (company_id)',
        'idx_queue_fk' => 'queue (company_id)',
        'idx_rank_fk' => 'rank (company_id)',
        'idx_rank_school_fk' => 'rank_school (school_id)',
        'idx_sale_report_fk' => 'sale_report (warehouse_id)',
        'idx_school_class_fk' => 'school_class (school_id)',
        'idx_school_class_member_fk' => 'school_class_member (class_id,user_id)',
        'idx_tax_fk' => 'tax (company_id)',
        'idx_user_to_company_fk' => 'user_to_company (company_id,user_id)',
        'idx_warehouse_fk' => 'warehouse (company_id)',
    );

    public function up()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            $uniqe = substr($idxName, 0, 4) == 'idxu' ? 'UNIQUE' : '';
            Doctrine_Manager::connection()->exec('CREATE ' . $uniqe . ' INDEX ' . $idxName . ' ON ' . $idxTable);
        }
    }

    public function down()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            Doctrine_Manager::connection()->exec('DROP INDEX IF EXISTS ' . $idxName);
        }
    }
}
