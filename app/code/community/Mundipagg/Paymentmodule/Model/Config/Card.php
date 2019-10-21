<?php

//@todo refact this class. A lot of methods that could be substituted by a single method.
class Mundipagg_Paymentmodule_Model_Config_Card
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function isEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/creditcard_group/cards_config_status',
                $this->storeId
        );
    }

    public function getTitle()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/creditcard_group/creditcard_payment_title',
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
            'mundipagg_config/creditcard_group/invoice_name',
                $this->storeId
        );
    }

    public function getOperationType()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/creditcard_group/operation_type',
                $this->storeId
        );
    }

    public function getSortOrder()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/creditcard_group/sort_order',
            $this->storeId
        );
    }

    public function getOperationTypeFlag()
    {
        return $this->getOperationType() === 'AuthAndCapture';
    }

    public function getInstallmentsConfig()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group',
                $this->storeId
        );
    }

    public function getBrandStatuses()
    {
        $installmentsConfig = $this->getInstallmentsConfig();

        return array(
            'amex' => $installmentsConfig['amex_status'],
            'diners' => $installmentsConfig['diners_status'],
            'elo' => $installmentsConfig['elo_status'],
            'hipercard' => $installmentsConfig['hipercard_status'],
            'mastercard' => $installmentsConfig['mastercard_status'],
            'visa' => $installmentsConfig['visa_status']
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

    public function isDefaultConfigurationEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/default_status',
                $this->storeId
        );
    }

    public function getDefaultMaxInstallmentNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/default_max_installments',
                $this->storeId
        );
    }

    public function getDefaultMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/default_min_amount',
            $this->storeId
        );
    }

    public function getDefaultMaxInstallmentNumberWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/default_max_without_interest',
                $this->storeId
        );
    }

    public function getDefaultInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/default_interest',
                $this->storeId
        );
    }

    public function getDefaultIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/default_incremental_interest',
                $this->storeId
        );
    }

    public function isVisaEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/visa_status',
                $this->storeId
        );
    }

    public function getVisaInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/visa_interest',
                $this->storeId
        );
    }

    public function getVisaMaxInstallmentsWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/visa_max_no_interest',
                $this->storeId
        );
    }

    public function getVisaMaxInstallmentsNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/visa_max_installments',
                $this->storeId
        );
    }

    public function getVisaMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/visa_min_amount',
            $this->storeId
        );
    }

    public function getVisaIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/visa_incremental_interest',
                $this->storeId
        );
    }

    public function isMastercardEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/mastercard_status',
                $this->storeId
        );
    }

    public function getMastercardInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/master_interest',
                $this->storeId
        );
    }

    public function getMastercardMaxInstallmentsWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/mastercard_max_no_interest',
                $this->storeId
        );
    }

    public function getMastercardMaxInstallmentsNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/mastercard_max_installments',
                $this->storeId
        );
    }

    public function getMastercardMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/mastercard_min_amount',
            $this->storeId
        );
    }

    public function getMastercardIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/mastercard_incremental_interest',
                $this->storeId
        );
    }

    public function isHipercardEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/hipercard_status',
                $this->storeId
        );
    }

    public function getHipercardInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/hipercard_interest',
                $this->storeId
        );
    }

    public function getHipercardMaxInstallmentsWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/hipercard_max_no_interest',
                $this->storeId
        );
    }

    public function getHipercardMaxInstallmentsNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/hipercard_max_installments',
                $this->storeId
        );
    }

    public function getHipercardMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/hipercard_min_amount',
            $this->storeId
        );
    }

    public function getHipercardIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/hipercard_incremental_interest',
                $this->storeId
        );
    }

    public function isDinersEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/diners_status',
                $this->storeId
        );
    }

    public function getDinersInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/diners_interest',
                $this->storeId
        );
    }

    public function getDinersMaxInstallmentsWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/diners_max_no_interest',
                $this->storeId
        );
    }

    public function getDinersMaxInstallmentsNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/diners_max_installments',
                $this->storeId
        );
    }

    public function getDinersMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/diners_min_amount',
            $this->storeId
        );
    }

    public function getDinersIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/diners_incremental_interest',
                $this->storeId
        );
    }

    public function isAmexEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/amex_status',
                $this->storeId
        );
    }

    public function getAmexInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/amex_interest',
                $this->storeId
        );
    }

    public function getAmexMaxInstallmentsWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/amex_max_no_interest',
                $this->storeId
        );
    }

    public function getAmexMaxInstallmentsNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/amex_max_installments',
                $this->storeId
        );
    }

    public function getAmexMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/amex_min_amount',
            $this->storeId
        );
    }

    public function getAmexIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/amex_incremental_interest',
                $this->storeId
        );
    }

    public function isEloEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/elo_status',
                $this->storeId
        );
    }

    public function getEloInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/elo_interest',
                $this->storeId
        );
    }

    public function getEloMaxInstallmentsWithoutInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/elo_max_no_interest',
                $this->storeId
        );
    }

    public function getEloMaxInstallmentsNumber()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/elo_max_installments',
                $this->storeId
        );
    }

    public function getEloMinAmount()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/elo_min_amount',
            $this->storeId
        );
    }

    public function getEloIncrementalInterest()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/installments_group/elo_incremental_interest',
                $this->storeId
        );
    }

    public function isSavedCreditCardsEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/creditcard_group/saved_cards_status',
                $this->storeId
        );
    }
}
