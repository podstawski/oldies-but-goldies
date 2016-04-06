<?php

class Playgine_Task_PutOnMarket extends Playgine_Task_Abstract
{
    protected $_warehouseRow;

    public function beforeRun()
    {
        $this->_warehouseRow = $this->getOption('warehouseRow');
        if (!($this->_warehouseRow instanceof Model_WarehouseRow)) {
            $modelWarehouse = new Model_Warehouse();
            $this->_warehouseRow = $modelWarehouse->find($this->getOption('id'))->current();
        }

        if ($this->_warehouseRow->status == Model_Warehouse::ON_MARKET) {
            throw new Playgine_Exception('Product is already on market');
        }

        $filters = array(
            'price' => array(
                new GN_Filter_Float()
            )
        );

        $validators = array(
            'price' => array(
                new GN_Validate_Float(),
                new GN_Validate_GreaterThan(0),
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );

        $input = new Zend_Filter_Input($filters, $validators, $this->getOptions());
        $this->_warehouseRow->price = $input->price;
    }

    public function run()
    {
        $this->_warehouseRow->status = Model_Warehouse::ON_MARKET;
        $this->_warehouseRow->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->translate('ProductType:' . $this->_warehouseRow->type),
            $this->_warehouseRow->amount,
            $this->currency($this->_warehouseRow->price)
        );
    }
}