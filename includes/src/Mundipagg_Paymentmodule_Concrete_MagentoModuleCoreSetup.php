<?php

namespace Mundipagg\Magento\Concrete;

use Mage;
use Mundipagg\Core\Kernel\Abstractions\AbstractDatabaseDecorator;
use Mundipagg\Core\Kernel\Abstractions\AbstractModuleCoreSetup;
use Mundipagg\Core\Kernel\Factories\ConfigurationFactory;
use Mundipagg\Core\Kernel\Repositories\ConfigurationRepository;

use Mundipagg_Paymentmodule_Model_Boleto as MagentoPlatformCartDecorator;
use Mundipagg_Paymentmodule_Model_Boleto as MagentoPlatformProductDecorator;
use Mundipagg_Paymentmodule_Model_Boleto as MagentoPlatformFormatService;
use MundipaggModuleBackend\Core\Repositories\Decorators\AbstractPlatformDatabaseDecorator;

final class MagentoModuleCoreSetup extends AbstractModuleCoreSetup
{
    const DEFAULT_STORE_DB_PLACEHOLDER = '(mp_default_store_id)';

    protected function setConfig()
    {
        self::$config = [
            AbstractModuleCoreSetup::CONCRETE_CART_DECORATOR_CLASS => MagentoPlatformCartDecorator::class,
            AbstractModuleCoreSetup::CONCRETE_DATABASE_DECORATOR_CLASS => MagentoPlatformDatabaseDecorator::class,
            AbstractModuleCoreSetup::CONCRETE_PRODUCT_DECORATOR_CLASS => MagentoPlatformProductDecorator::class,
            AbstractModuleCoreSetup::CONCRETE_FORMAT_SERVICE => MagentoPlatformFormatService::class,
            AbstractModuleCoreSetup::CONCRETE_PLATFORM_ORDER_DECORATOR_CLASS => MagentoOrderDecorator::class,
            AbstractModuleCoreSetup::CONCRETE_PLATFORM_INVOICE_DECORATOR_CLASS => MagentoInvoiceDecorator::class
        ];
    }

    static public function getDatabaseAccessObject()
    {
        return Mage::getSingleton('core/resource');
    }

    static protected function getPlatformHubAppPublicAppKey()
    {
        return "2d2db409-fed0-4bd8-ac1e-43eeff33458d";
    }

    public function loadModuleConfigurationFromPlatform()
    {
        static::fixDefaultStoreDbPlaceHolders();

        $store = self::getCurrentStoreId();

        $configurationRepository = new ConfigurationRepository;

        $savedConfig = $configurationRepository->findByStore($store);
        if ($savedConfig !== null) {
            self::$moduleConfig = $savedConfig;
            return;
        }

        $configData = new \stdClass;
        $configData->boletoEnabled =
            Mage::getModel('paymentmodule/config_boleto')->isEnabled();
        $configData->creditCardEnabled =
            Mage::getModel('paymentmodule/config_card')->isEnabled();
        $configData->boletoCreditCardEnabled =
            Mage::getModel('paymentmodule/config_boletocc')->isEnabled();
        $configData->twoCreditCardsEnabled =
            Mage::getModel('paymentmodule/config_twocreditcards')->isEnabled();
        $configData->hubInstallId = null;
        $configData->storeId = $store;

        $configData->cardConfigs = [];//self::getCardConfigs($storeConfig);

        $configurationFactory = new ConfigurationFactory();
        $config = $configurationFactory->createFromJsonData(
            json_encode($configData)
        );

        self::$moduleConfig = $config;
    }

    /**
     * Set all configuration table store_id that are the default value to the defaultStoreId.
     * In other words, fix all the entries in this table that were created without the store_id.
     *
     * @throws \Exception
     */
    protected static function fixDefaultStoreDbPlaceHolders()
    {
        $dbDecorator = new MagentoPlatformDatabaseDecorator(
            self::getDatabaseAccessObject()
        );
        $table = $dbDecorator->getTable(
            AbstractDatabaseDecorator::TABLE_MODULE_CONFIGURATION
        );
        $defaultStoreId = self::getDefaultStoreId();
        $defaultStoreDbPlaceHolder = self::DEFAULT_STORE_DB_PLACEHOLDER;

        $query = "
          UPDATE $table 
            SET store_id = '$defaultStoreId' 
          WHERE store_id = '$defaultStoreDbPlaceHolder';
        ";
        $dbDecorator->query($query);
    }

    public static function loadModuleConfigurationByStore($storeId)
    {
        $configurationRepository = new ConfigurationRepository;

        $savedConfig = $configurationRepository->findByStore($storeId);
        if ($savedConfig !== null) {
            return $savedConfig;
        }
    }

    protected function setModuleVersion()
    {
        $data = \Mage::helper('paymentmodule')->getMetaData();
        self::$moduleVersion = $data['module_version'];
    }

    protected function setLogPath()
    {
        self::$logPath = [
            \Mage::getBaseDir('log')
        ];
    }

    protected function _getDashboardLanguage()
    {
        // TODO: Implement _getDashboardLanguage() method.
    }

    protected function _getStoreLanguage()
    {
        // TODO: Implement _getStoreLanguage() method.
    }

    protected function _formatToCurrency($price)
    {
        // TODO: Implement _formatToCurrency() method.
    }

    protected function setPlatformVersion()
    {
        self::$platformVersion = Mage::getVersion();
    }

    public static function getCurrentStoreId()
    {
        $store = Mage::getSingleton('adminhtml/config_data')->getScopeId();
        $configData = Mage::getSingleton('adminhtml/config_data');

        if ($configData->getScope() == 'websites') {
            $store = $configData->getScopeId();
        }

        if ($configData->getScope() == 'stores') {
            $website = Mage::getModel('core/website')->load(
                $configData->getWebsite()
            );
            $store =  $website->getId();
        }

        //to fix hub endpoint issue
        $params = Mage::app()->getRequest()->getParams();
        if (isset($params['storeId'])) {
            $store = $params['storeId'];
        }

        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return $store;
    }

    public static function getDefaultStoreId()
    {
        return Mage::app()
            ->getWebsite(0)
            ->getDefaultGroup()
            ->getDefaultStoreId();
    }

    /**
     * @since 1.7.1
     *
     */
    protected function getPlatformStoreTimezone()
    {
        //@TODO: Implement getPlatformStoreTimezone() method.
    }
}