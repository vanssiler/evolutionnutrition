<?php

class Mundipagg_Paymentmodule_Helper_StoreUrl extends Mage_Core_Helper_Abstract
{
    public function getUrlByScope($configData)
    {
        switch ($configData->getScope()) {
            case 'stores':
                return $this->getBaseUrlByStore($configData);
            case 'websites':
                return $this->getBaseUrlByWebsiteCode($configData->getScopeCode());
            default:
                return $this->getBaseUrlByWebsiteId();
        }
    }

    public function getBaseUrlByStore($configData)
    {
        $storeId = $configData->getScopeId();

        return Mage::app()->getStore($storeId)
            ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
    }

    public function getBaseUrlByWebsiteCode($code)
    {
        $website = Mage::getModel('core/website')->load(
            $code
        );
        $storeId =  $website->getDefaultGroup()->getDefaultStoreId();

        return Mage::app()->getStore($storeId)
            ->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
    }

    public function getBaseUrlByWebsiteId($id = null, $route = "")
    {
        $website = Mage::app()->getWebsite(true);
        if ($id) {
            $website = Mage::app()->getWebsite($id);
        }

        $storeId = $website->getDefaultGroup()->getDefaultStoreId();
        $store = Mage::app()->getStore($storeId);

        if (!empty($route)) {
            return $store->getUrl($route);
        }

        return $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
    }
}