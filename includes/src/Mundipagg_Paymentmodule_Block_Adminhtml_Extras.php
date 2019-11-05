<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Block_Adminhtml_Extras extends Mage_Page_Block_Html_Head
{
    public function __construct(array $args = array())
    {
        parent::_construct($args);
        $this->setTemplate('paymentmodule/extras.phtml');
        MPSetup::bootstrap();
    }

    public function getStoreUrl()
    {
        $useStoreUrl = Mage::getStoreConfig('web/url/use_store');
        $configData = Mage::getSingleton('adminhtml/config_data');

        $storeUrlHelper =  Mage::helper('paymentmodule/storeUrl');

        if ($useStoreUrl) {
            return $this->cleanUrl(
                $storeUrlHelper->getUrlByScope($configData)
            );
        }

        $storeId = MPSetup::getCurrentStoreId();
        $url = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        return $this->cleanUrl(
            $this->getBaseUrl($url)
        );
    }

    public function getHubValidateUrl()
    {
        return $this->getStoreUrl() . "mp-paymentmodule/hub/validateInstall/";
    }

    public function getHubStatusUrl()
    {
        return $this->getStoreUrl() . "mp-paymentmodule/hub/status/";
    }

    public function getHubGenerateIntegrationUrl()
    {
        return $this->getStoreUrl() . "mp-paymentmodule/hub/generateintegrationtoken/";
    }

    public function cleanUrl($url)
    {
        return str_replace(
            "/index.php",
            "",
            $url
        );
    }
}