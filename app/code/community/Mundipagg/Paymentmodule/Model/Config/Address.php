<?php

class Mundipagg_Paymentmodule_Model_Config_Address
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function getStreet()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/address_group/street',
            $this->storeId
        );
    }

    public function getNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/address_group/number',
            $this->storeId
        );
    }

    public function getComplement()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/address_group/complement',
            $this->storeId
        );
    }

    public function getNeighborhood()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/address_group/neighborhood',
            $this->storeId
        );
    }
}
