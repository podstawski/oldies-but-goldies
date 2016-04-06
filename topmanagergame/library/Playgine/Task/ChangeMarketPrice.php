<?php

class Playgine_Task_ChangeMarketPrice extends Playgine_Task_Abstract
{
    /**
     * @var Model_WarehouseRow
     */
    protected $_warehouseRow;

    /**
     * @var int
     */
    protected $_oldPrice;

    public function init()
    {
        $filters = array(
            'new_price' => array(
                new GN_Filter_Float()
            )
        );

        $validators = array(
            'item_id'   => array(
                new Zend_Validate_Digits(),
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            ),
            'new_price' => array(
                new GN_Validate_Float(),
                new GN_Validate_GreaterThan(0),
                Zend_Filter_Input::PRESENCE => Zend_Filter_Input::PRESENCE_REQUIRED
            )
        );

        $options = new Zend_Filter_Input($filters, $validators, $this->getOptions());

        if (!$options->isValid('item_id')) {
            throw new Playgine_Exception('invalid item id');
        }

        if (!$options->isValid('new_price')) {
            throw new Playgine_Exception('price must be a positive float value');
        }

        $modelWarehouse = new Model_Warehouse();
        $this->_warehouseRow = $modelWarehouse->fetchRow(array(
                                                             'id = ?'         => $options->item_id,
                                                             'company_id = ?' => $this->getCompany()->id
                                                         )
        );

        if ($this->_warehouseRow == null) {
            throw new Playgine_Exception('could not find item row');
        }

        $this->setOption('item_id', $options->item_id);
        $this->setOption('new_price', $options->new_price);

        $this->setCost(0);
    }

    public function run()
    {
        $this->_oldPrice = $this->_warehouseRow->price;
        $this->_warehouseRow->price = $this->getOption('new_price');
        $this->_warehouseRow->status = Model_Warehouse::ON_MARKET;
        $this->_warehouseRow->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->translate('ProductType:' . $this->_warehouseRow->type),
            $this->currency($this->_oldPrice),
            $this->currency($this->_warehouseRow->price)
        );
    }
}