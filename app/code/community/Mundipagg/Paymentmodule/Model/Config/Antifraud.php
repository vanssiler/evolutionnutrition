<?php

class Mundipagg_Paymentmodule_Model_Config_Antifraud
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    const basePath = 'mundipagg_config/antifraud_group/';

    public function isEnabled()
    {
        return Mage::getStoreConfig(
            self::basePath . 'antifraud_status',
                $this->storeId
            ) == 1;
    }

    public function getMinimumValue()
    {
        $value = Mage::getStoreConfig(
            self::basePath . 'antifraud_minimum',
            $this->storeId
        );

        return !empty($value) ? $value : 0;
    }

    public function shouldApplyAntifraud($amountInCents)
    {
        return $this->isEnabled() && $amountInCents >= ($this->getMinimumValue() * 100);
    }
}
