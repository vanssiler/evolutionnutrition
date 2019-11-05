<?php

class Mundipagg_Paymentmodule_Model_Config_Twocreditcards extends Mundipagg_Paymentmodule_Model_Config_Card
{
    public function isEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/twocreditcards_group/twocreditcards_status',
                $this->storeId
            ) == 1;
    }

    public function getPaymentTitle()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/twocreditcards_group/twocreditcards_payment_title',
                $this->storeId
            );
    }

    public function getSortOrder()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/twocreditcards_group/sort_order',
            $this->storeId
        );
    }
}
