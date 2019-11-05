<?php

class Mundipagg_Paymentmodule_Block_Adminhtml_Version
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $metadata = Mage::helper('paymentmodule')->getMetaData();
        $version = (string)$metadata['module_version'];

        $integrityBlock = Mage::getBlockSingleton('paymentmodule/adminhtml_notification_integrityviolation');
        if ($integrityBlock->isViolated()) {
            $version = "<i>$version</i>";
        }

        return $version;
    }
}
