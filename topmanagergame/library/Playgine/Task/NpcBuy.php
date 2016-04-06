<?php

class Playgine_Task_NpcBuy extends Playgine_Task_Abstract
{
    protected $_offers;

    public function beforeRun()
    {
        $this->_offers = $this->getCompany()->getWarehouseRowsByStatus(Model_Warehouse::ON_MARKET);
    }

    public function run()
    {
        $npcParams = Model_Param::get('npc');

        $cost = 0;
        $company = $this->getCompany();
        $modelAnalyst = new Model_Analyst();
        foreach ($this->_offers as $offer) {
            $partsCost = Model_Param::get('product.' . $offer->type . '.parts');
            $margin = ($offer->price - $partsCost) / $partsCost;
            /**
             * @var $offer Model_WarehouseRow
             */
            if ($margin > 0 && $margin <= 0.5) {
                $tmp = array();
                $averagePrice = $modelAnalyst->getAveragePriceForType($offer->type);
                $tmp['srednia cena'] = $averagePrice;
                $amount = rand($npcParams['buy']['min'], $npcParams['buy']['max'])
                        + $modelAnalyst->getPredictionForType($offer->type)
                        + intval(Model_Param::get('product.' . $offer->type . '.quality.bonus.' . $offer->quality));
                $tmp['bazowa wartosc'] = $amount;
                // delta < 0: moja cena jest niższa - sprzedaj 3% więcej
                // delta > 0: moja cena jest wyższa - sprzedaj 5% mniej
                $delta = ($offer->price - $averagePrice) * 100 / $averagePrice;
                $tmp['delta'] = $delta;
                $amount += abs($delta) * ($delta < 0 ? $npcParams['increase'] : -$npcParams['decrease']);
                $tmp['ile % sie sprzeda'] = $amount;
                $amount = min($offer->amount, round($offer->amount * $amount / 100));
                $tmp['ilosc'] = $amount;
                $tmp['offer'] = $offer->toArray();
//                file_put_contents(APPLICATION_PATH . '/logs/debug.txt', print_r($tmp, 1), FILE_APPEND);
//                echo '<pre>';print_r($tmp);die();
                $cost += ($offer->price * $amount);
//                if ($amount > 0)
                    $offer->sellToNPC($amount, $company);
            } else {
                $offer->sellToNPC(0, $company);
            }
        }
        $this->setCost(-1 * $cost);
    }

    public function getMessageParams($type)
    {
        return array(
            $this->currency(-1 * $this->getCost())
        );
    }
}