<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Model_Config_Form_Log_Enabled extends Mage_Core_Model_Config_Data
{
    const DISABLED = 0;
    const ENABLED  = 1;

    public function save()
    {

        MPSetup::bootstrap();
        $storeId = MPSetup::getCurrentStoreId();

        $isModuleLogsEnabled = $this->getValue();
        $magentoLogsEnabledConfigPath = 'dev/log/active';
        $isMagentoLogsEnabled = Mage::getStoreConfig($magentoLogsEnabledConfigPath, $storeId);

        if ($isModuleLogsEnabled == self::ENABLED && $isMagentoLogsEnabled == self::DISABLED) {
            try {
                Mage::getConfig()->saveConfig(
                    $magentoLogsEnabledConfigPath,
                    self::ENABLED,
                    'default',
                    $storeId
                );
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return parent::save();
    }
}