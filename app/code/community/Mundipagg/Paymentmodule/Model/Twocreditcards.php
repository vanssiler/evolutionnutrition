<?php

class Mundipagg_Paymentmodule_Model_Twocreditcards extends Mundipagg_Paymentmodule_Model_Standard
{
    protected $_code = 'paymentmodule_twocreditcards';
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
        return Mage::getModel('paymentmodule/config_twocreditcards');
    }

    public function getPaymentStructure()
    {
        return array(
            'creditcard',
            'creditcard'
        );
    }

    protected function getBaseKey()
    {
        return 'mundipagg_payment_module_';
    }
}
