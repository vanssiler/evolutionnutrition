<?php

class Mundipagg_Paymentmodule_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMetaData()
    {
        return array(
            'module_name' => 'magento_module',
            'module_version' => Mage::getConfig()
                ->getNode('modules/Mundipagg_Paymentmodule/version')->asArray(),
            'magento_version' => Mage::getVersion()
        );
    }
}
