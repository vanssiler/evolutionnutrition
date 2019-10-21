<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Helper_Log extends Mage_Core_Helper_Abstract
{
    protected $level;
    protected $method;
    protected $logLabel = '';
    protected $addHostName = false;
    protected $logger;
    protected $logPath;
    protected $storeId;

    public function __construct($method = '')
    {
        MPSetup::bootstrap();
        $this->storeId = MPSetup::getCurrentStoreId();

        $this->method = $method;
        $this->addHostName = Mage::getStoreConfig(
            'mundipagg_config/log_group/host_name',
            $this->storeId
        ) == '1';
        $this->logger = Mage::helper('paymentmodule/logger');

        $this->logPath = Mage::getStoreConfig(
            'mundipagg_config/log_group/log_path',
            $this->storeId
        );
        if (Mage::getStoreConfig(
            'mundipagg_config/log_group/non_default_dir',
            $this->storeId
        ) != '1') {
            $this->logPath = Mage::getBaseDir('var') . DS . 'log';
        }
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setLogLabel($logLabel)
    {
        $this->logLabel = $logLabel;
        return $this;
    }

    public function getLogLabel()
    {
        return $this->logLabel;
    }

    public function info($msg)
    {
        $this->level = Zend_Log::INFO;
        $this->write($msg);
    }

    public function debug($msg)
    {
        $this->level = Zend_Log::DEBUG;
        $this->write($msg);
    }

    public function warning($msg)
    {
        $this->level = Zend_Log::WARN;
        $this->write($msg);
    }

    public function error($msg, $logExceptionFile = false)
    {
        $exception = new Exception($msg);
        $this->level = Zend_Log::ERR;
        $this->write($msg);

        if ($logExceptionFile) {
            Mage::logException($exception);
        }
    }

    public function getModuleLogFilenamePrefix()
    {
        return "Mundipagg_PaymentModule_";
    }

    protected function write($msg)
    {
        $logIsEnabled = boolval(Mage::getStoreConfig(
            'mundipagg_config/log_group/enabled',
            $this->storeId
        ));

        if ($logIsEnabled === false) {
            return;
        }

        $metaData = Mage::helper('paymentmodule/data')->getMetaData();
        $version = $metaData['module_version'];

        $file =  $this->getModuleLogFilenamePrefix() . date('Y-m-d');
        if ($this->addHostName) {
            $file .= '_' . gethostname();
        }

        $file .= ".log";
        $method = $this->method;
        $newMsg = "v{$version} ";

        if (!empty($method)) {
            $logLabel = $this->logLabel;

            if (!empty($logLabel)) {
                $newMsg .= "[{$this->method}] {$this->logLabel} | {$msg}";
            } else {
                $newMsg .= "[{$this->method}] {$msg}";
            }
        } else {
            $newMsg .= $msg;
        }

        $this->logger->log($newMsg, $this->level, $file, $this->logPath);
    }
}