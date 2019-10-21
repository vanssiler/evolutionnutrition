<?php

class Mundipagg_Paymentmodule_Model_Config_Multibuyer
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    const basePath = 'mundipagg_config/multibuyer_group/';

    public function isEnabled()
    {
        return Mage::getStoreConfig(
            self::basePath . 'multibuyer_status',
                $this->storeId
            ) == 1;
    }
}
