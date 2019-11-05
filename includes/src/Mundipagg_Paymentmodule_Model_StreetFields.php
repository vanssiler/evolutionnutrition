<?php

class Mundipagg_Paymentmodule_Model_StreetFields
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function toOptionArray()
    {
        $streetLines = array();
        $fieldsNumber = Mage::getStoreConfig(
            'customer/address/street_lines',
            $this->storeId
        );

        for ($i = 1; $i <= $fieldsNumber; $i++) {
            $streetLines[$i -1] = 'street_' . $i;
        }

        return $streetLines;
    }
}
