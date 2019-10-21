<?php

class Mundipagg_Paymentmodule_Model_Creditcard extends Mundipagg_Paymentmodule_Model_Standard
{
    protected $_code = 'paymentmodule_creditcard';
    protected $_formBlockType = 'paymentmodule/form_builder';
    protected $_isGateway = true;
    protected $_canOrder  = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = false;
    protected $_canManageRecurringProfiles = false;
    protected $_isInitializeNeeded = true;

    protected function getConfigModel()
    {
        return Mage::getModel('paymentmodule/config_card');
    }

    public function getPaymentStructure()
    {
        return array(
            'creditcard'
        );
    }

    public function validatePaymentData($paymentData)
    {
        foreach ($paymentData as $creditCard) {
            $enabledBrands = $this->getConfigModel()->getEnabledBrands();

            if (
                !in_array(
                    strtolower($creditCard['brand']),
                    $enabledBrands
                ) && !$this->validateInstallments($creditCard)

            ) {
                return false;
            }

            return true;
        }
    }

    private function validateInstallments($card)
    {
        $configModel = $this->getConfigModel();
        $default = $configModel->isDefaultConfigurationEnabled();

        $installments = $card['creditCardInstallments'];
        $brand = $card['brand'];

        if (
            $default &&
            $installments <= $configModel->getDefaultMaxInstallmentNumber()
        ) {
            return true;
        }

        $brandMaxInstallments = 'get' . $brand . 'MaxInstallmentsNumber';

        if ($installments > $configModel->$brandMaxInstallments()) {
            return false;
        }

        return true;
    }
}
