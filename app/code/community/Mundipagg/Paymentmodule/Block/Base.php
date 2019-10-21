<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Block_Base extends Mage_Payment_Block_Form
{
    public function __construct()
    {
        MPSetup::bootstrap();
        parent::__construct();
    }

    public function getCurrentCurrencySymbol()
    {
        return Mage::helper('paymentmodule/monetary')->getCurrentCurrencySymbol();
    }
}