<?php

class Playgine_Task_UpgradeQuality extends Playgine_Task_Abstract
{
    /**
     * @var Model_ProductRow
     */
    protected $_product;

    public function init()
    {
        $id = intval($this->getOption('id'));
        if (!$id) {
            throw new InvalidArgumentException('Missing product ID');
        }
        $this->_product = $this->getCompany()->getProduct($id);
        if (!$this->_product) {
            throw new Playgine_Exception('Could not find product');
        }
    }

    public function beforeRun()
    {
        if ($this->_product->canUpgradeQuality() == false) {
            throw new Playgine_Exception('You cannot further upgrade quality of this product');
        }

        $this->setCost($this->_product->getQualityUpgradeCost());
    }

    public function run()
    {
        $this->_product->quality++;
        $this->_product->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->translate('ProductType:' . $this->_product->type),
            $this->_product->quality
        );
    }
}