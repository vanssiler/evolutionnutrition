<?php

class Mundipagg_Paymentmodule_Model_Config_Boleto
    extends Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    public function isEnabled()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boleto_group/boleto_status',
                $this->storeId
            ) == 1;
    }

    public function getPaymentTitle()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boleto_group/boleto_payment_title',
                $this->storeId
        );
    }

    public function getName()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boleto_group/boleto_name',
                $this->storeId
        );
    }

    public function getBank()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boleto_group/boleto_bank',
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
            'mundipagg_config/boleto_group/boleto_due_at',
                $this->storeId
        );
        return new DateTime(date('Y-m-d', strtotime('+' . $term . ' days')));
    }

    public function getInstructions()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boleto_group/boleto_instructions',
                $this->storeId
        );
    }

    public function getSortOrder()
    {
        return Mage::getStoreConfig(
            'mundipagg_config/boleto_group/sort_order',
            $this->storeId
        );
    }
}
