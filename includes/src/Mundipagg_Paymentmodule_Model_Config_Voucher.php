<?php

class Mundipagg_Paymentmodule_Model_Config_Voucher
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function isEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_group/voucher_config_status',
                $this->storeId
            );
    }

    public function getTitle()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_group/voucher_payment_title',
                $this->storeId
            );
    }

    public function getPaymentTitle()
    {
        return $this->getTitle();
    }

    public function getInvoiceName()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_group/invoice_name',
                $this->storeId
            );
    }

    public function getOperationType()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_group/operation_type',
                $this->storeId
            );
    }

    public function getOperationTypeFlag()
    {
        return $this->getOperationType() === 'AuthAndCapture';
    }


    public function getBrandStatuses()
    {
        return array(
            'vr' => $this->isVrEnabled(),
            'sodexo' => $this->isSodexoEnabled()
        );
    }

    public function getEnabledBrands()
    {
        $brandStatuses = $this->getBrandStatuses();
        $enabledBrands = array();

        foreach ($brandStatuses as $brand => $status) {
            if ($status == 1) {
                $enabledBrands[] = $brand;
            }
        }

        return $enabledBrands;
    }

    public function isVrEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_brands_group/vr_status',
                $this->storeId
            );
    }

    public function isSodexoEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_brands_group/sodexo_status',
                $this->storeId
            );
    }

    public function getSortOrder()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/voucher_group/sort_order',
            $this->storeId
        );
    }
}
