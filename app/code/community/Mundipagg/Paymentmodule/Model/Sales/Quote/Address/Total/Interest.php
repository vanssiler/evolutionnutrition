<?php

class Mundipagg_Paymentmodule_Model_Sales_Quote_Address_Total_Interest extends
    Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'mundipagg_interest';

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amt = $address->getMundipaggInterest();
        if ($amt > 0) {
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>"Juros de parcelas",
                'value'=> $amt
            ));
        }
        return $this;
    }
}