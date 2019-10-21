<?php

class Mundipagg_Paymentmodule_Model_Config_Boletocc
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function isEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_config_status',
                $this->storeId
            ) == 1;
    }

    public function getPaymentTitle()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_creditcard_payment_title',
                $this->storeId
        );
    }

    public function getName()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_name',
                $this->storeId
        );
    }

    public function getBank()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_bank',
                $this->storeId
        );
    }

    /**
     * This method returns a string date formatted according to iso-8601
     *
     * @return string
     */
    public function getDueAt()
    {
        $term = Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_due_at',
                $this->storeId
        );
        return new DateTime(date('Y-m-d', strtotime('+' . $term . ' days')));
    }

    public function getInstructions()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_instructions',
                $this->storeId
        );
    }

    /** Card configs */

    public function getInvoiceName()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_invoice_name',
                $this->storeId
        );
    }

    public function getOperationType()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/boleto_cards_operation_type',
                $this->storeId
        );
    }

    public function getOperationTypeFlag()
    {
        return $this->getOperationType() === 'AuthAndCapture';
    }

    public function getSortOrder()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boletocreditcard_group/sort_order',
            $this->storeId
        );
    }
}
