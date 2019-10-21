<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Integrity\IntegrityController;

class Mundipagg_Paymentmodule_Block_Adminhtml_Notification_Integrityviolation extends Mage_Adminhtml_Block_Template
{
    public function __construct(array $args = array())
    {
        parent::_construct($args);
        $this->setTemplate('paymentmodule/notifications/integrityViolation.phtml');
    }

    public function isViolated()
    {
        return false; //disabling integrity violation functionality;
        /**
        MagentoModuleCoreSetup::bootstrap();
        $integrityService = new IntegrityInfoRetrieverService();
        $integrity = $integrityService->retrieveInfo("");

        return $this->isModuleViolated($integrity->module) ||
            $this->isModuleCoreViolated($integrity->core);
         */
    }

    public function isModuleViolated($moduleInfo)
    {
        return
            count($moduleInfo->altered) > 0 ||
            count($moduleInfo->removed) > 0 ||
            count($moduleInfo->added) > 0;
    }

    public function isModuleCoreViolated($moduleCoreInfo)
    {
        return
            count($moduleCoreInfo->altered) > 0 ||
            count($moduleCoreInfo->removed) > 0 ||
            count($moduleCoreInfo->added) > 0;
    }
}
