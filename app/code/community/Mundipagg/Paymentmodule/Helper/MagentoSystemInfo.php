<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Integrity\SystemInfoInterface;
use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as CoreSetup;

class Mundipagg_Paymentmodule_Helper_MagentoSystemInfo implements SystemInfoInterface
{
    private $moduleConfig;
    private $storeId;

    public function __construct()
    {
        CoreSetup::bootstrap();
        $this->moduleConfig = CoreSetup::getModuleConfiguration();

        $this->storeId = CoreSetup::getCurrentStoreId();
    }

    public function getModuleVersion()
    {
        $data = \Mage::helper('paymentmodule')->getMetaData();
        return $data['module_version'];
    }

    public function getPlatformVersion()
    {
        $data = \Mage::helper('paymentmodule')->getMetaData();
        return 'magento ' .  $data['magento_version'];
    }

    public function getPlatformRootDir()
    {
        return \Mage::getBaseDir();
    }

    public function getDirectoriesIgnored()
    {
        return array(
            "./lib/",
            "./var/connect/"
        );
    }

    public function getModmanPath()
    {
        return $this->getPlatformRootDir() . '/app/code/community/Mundipagg/Paymentmodule/etc/maintenance/modman';
    }

    public function getIntegrityCheckPath()
    {
        return $this->getPlatformRootDir() . '/app/code/community/Mundipagg/Paymentmodule/etc/maintenance/integrityCheck';
    }

    public function getInstallType()
    {
        $installType = 'package';
        if (is_dir('./.modman')) {
            $installType = 'modman';
        }

        return $installType;
    }

    public function getLogsDirs()
    {
        return array(
            'magentoLogsDirectory' => $this->getDefaultLogDir(),
            'moduleLogsDirectory' => $this->getModuleLogDir()
        );
    }

    public function getDefaultLogDir()
    {
        return \Mage::getBaseDir('log');
    }

    public function getModuleLogDir()
    {
        return \Mage::getBaseDir('log');
    }

    public function getDefaultLogFiles()
    {
        return array(
            \Mage::getStoreConfig('dev/log/file', $this->storeId),
            \Mage::getStoreConfig('dev/log/exception_file', $this->storeId),
        );
    }

    public function getModuleLogFilenamePrefix()
    {
        return \Mage::helper('paymentmodule/log')->getModuleLogFilenamePrefix();
    }

    public function getSecretKey()
    {
        return $this->moduleConfig->getSecretKey()->getValue();
    }

    public function getRequestParams()
    {
        return \Mage::app()->getRequest()->getParams();
    }

    public function getDownloadRouter()
    {
        return '/mp-paymentmodule/maintenance/downloadLog?';
    }

    public function getRequestParam($param)
    {
        return \Mage::app()->getRequest()->getParam($param);
    }
}
