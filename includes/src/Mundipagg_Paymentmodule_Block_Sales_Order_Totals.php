<?php

class Mundipagg_Paymentmodule_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();

        $interestHelper = Mage::helper('paymentmodule/interest');
        $interest = $interestHelper->getTotalInterestFromOrder($this->getOrder());

        if ($interest > 0) {
            $this->addTotalBefore(new Varien_Object(
                array(
                    'code'  => 'mundipagg_interest',
                    'field' => 'interest',
                    'value' => $interest,
                    'label' => $this->__('Interest')
                )
            ), 'grand_total');
        }
        
        return $this;
    }
}

