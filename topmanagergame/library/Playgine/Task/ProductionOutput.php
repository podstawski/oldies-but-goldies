<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Playgine_Task_ProductionOutput extends Playgine_Task_Abstract
{
    /**
     * @var Model_ProductRow
     */
    protected $product;

    public function init()
    {
        $id = intval($this->getOption('id'));
        if (!$id) {
            throw new InvalidArgumentException('Invalid product ID');
        }

        $this->product = $this->getCompany()->getProduct($id);
        if ($this->product == null) {
            throw new Playgine_Exception('Could not find product');
        }
    }

    public function run()
    {
        $amount = intval($this->getOption('amount'));

        $this->product->output = min($amount, $this->product->getMaxOutput());
        $this->product->save();
    }

    public function getMessageParams()
    {
        return array(
            $this->translate('ProductType:' . $this->product->type),
            $this->getOption('amount')
        );
    }
}