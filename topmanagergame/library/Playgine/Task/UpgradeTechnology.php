<?php

class Playgine_Task_UpgradeTechnology extends Playgine_Task_Abstract
{
    /**
     * @var Model_ProductRow
     */
    protected $_product;

    public function init()
    {
        $id = intval($this->getOption('id'));
        if (!$id) {
            throw new Playgine_Exception('Missing product ID');
        }
        $this->_product = $this->getCompany()->getProduct($id);
        if (!$this->_product) {
            throw new Playgine_Exception('Could not find product');
        }
    }

    public function beforeRun()
    {
        if ($this->_product->canUpgradeTechnology() == false) {
            throw new Playgine_Exception('You cannot further upgrade technology of this product');
        }

        $this->setCost($this->_product->getTechnologyUpgradeCost());
    }

    public function run()
    {
        $this->_product->technology++;
        $this->_product->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->translate('ProductType:' . $this->_product->type),
            $this->_product->technology
        );
    }
}