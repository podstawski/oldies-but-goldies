<?php
/**
 * @author Radosław Szczepaniak <radoslaw.szczepaniak@gammanet.pl>
 */

class AlterPaymentAddPayerId extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('payment', 'payer_id', 'integer', null, array('notnull' => false));

        $this->createForeignKey('payment', 'fk_payment_payer', array(
            'local' => 'payer_id',
            'foreign' => 'id',
            'foreignTable' => 'users',
            'onDelete' => 'SET NULL',
        ));

        $this->addIndex('payment', 'idx_payment_payer', array(
            'fields' => array('payer_id')
        ));
    }

    public function down()
    {
        $this->removeColumn('payment', 'payer_id');
    }
}