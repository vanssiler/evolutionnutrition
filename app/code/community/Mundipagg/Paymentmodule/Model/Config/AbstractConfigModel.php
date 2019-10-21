<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

abstract class Mundipagg_Paymentmodule_Model_Config_AbstractConfigModel
{
    protected $storeId;

    public function __construct()
    {
        $this->storeId = MPSetup::getCurrentStoreId();
    }
}