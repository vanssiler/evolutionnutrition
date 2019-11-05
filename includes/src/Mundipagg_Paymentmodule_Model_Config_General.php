<?php

class Mundipagg_Paymentmodule_Model_Config_General
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function isEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/general_group/module_status',
                $this->storeId
        ) == 1;
    }

    public function isLogEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/log_group/enabled',
                $this->storeId
        ) == 1;
    }

    protected function getProdSecretKey()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/general_group/sk_prod',
                $this->storeId
        );
    }

    protected function getTestSecretKey()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/general_group/sk_test',
                $this->storeId
        );
    }

    protected function getProdPublicKey()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/general_group/pk_prod',
                $this->storeId
        );
    }

    protected function getTestPublicKey()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/general_group/pk_test',
                $this->storeId
        );
    }

    public function isTestModeEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/general_group/test_mode',
                $this->storeId
        ) == 1;
    }

    public function getSecretKey()
    {
        if ($this->isTestModeEnabled()) {
            return $this->getTestSecretKey();
        }

        return $this->getProdSecretKey();
    }

    public function getPublicKey()
    {
        if ($this->isTestModeEnabled()) {
            return $this->getTestPublicKey();
        }

        return $this->getProdPublicKey();
    }

    public function getPassword()
    {
        return '';
    }
}
