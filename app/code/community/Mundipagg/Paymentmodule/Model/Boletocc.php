<?php

class Mundipagg_Paymentmodule_Model_Boletocc extends Mundipagg_Paymentmodule_Model_Standard
{
    protected $_code = 'paymentmodule_boletocc';
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
        return Mage::getModel('paymentmodule/config_boletocc');
    }

    public function getPaymentStructure()
    {
        return array(
            'boleto',
            'creditcard'
        );
    }
}
