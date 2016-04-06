<?php

class Playgine_Task_Production extends Playgine_Task_Abstract
{
    /**
     * @var array
     */
    private $_types;

    public function init()
    {
        $workers = $this->getCompany()->getWorkers();
        if ($workers->busy == 0) {
            throw new Playgine_Exception('There are no employees assigned to production');
        }

        $cost = $this->getCompany()->getTotalProductionCost();
        $this->setCost($cost);

        $this->_types = array();
    }

    public function run()
    {
        $modelWarehouse = new Model_Warehouse();
        foreach ($this->getCompany()->getProducts() as $product) {
            if ($product->output > 0) {
                if ($warehouseRow = $modelWarehouse->fetchRow(array(
                    'company_id = ?' => $this->getCompany()->id,
                    'type = ?' => $product->type,
                    'technology = ?' => $product->technology,
                    'quality = ?' => $product->quality,
                ))
                ) {
                    $warehouseRow->amount += $product->output;
                } else {
                    $warehouseRow = $modelWarehouse->createRow();
                    $warehouseRow->company_id = $this->getCompany()->id;
                    $warehouseRow->type = $product->type;
                    $warehouseRow->technology = $product->technology;
                    $warehouseRow->quality = $product->quality;
                    $warehouseRow->amount = $product->output;
                    $warehouseRow->parts_cost = $product->getPartsCost();
                    $warehouseRow->price = 0;
                }
                $warehouseRow->status = Model_Warehouse::JUST_PRODUCED;
                $warehouseRow->save();

                $this->_types[$product->type] = $product->output;
            }
        }
    }

    public function getMessageParams()
    {
        $html = '';
        foreach ($this->_types as $type => $output) {
            $html .= '<li>' . $this->translate('ProductType:' . $type) . ': <strong>' . $output . ' szt.</strong></li>';
        }
        return array($html);
    }
}