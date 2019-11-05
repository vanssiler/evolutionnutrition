<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Helper_Logger extends Mage_Core_Helper_Abstract
{


    /**
     * @param string $message
     * @param integer $level
     * @param string $file
     * @param string $logDir
     * @param bool $forceLog
     */
    public static function log($message, $level = null, $file = '', $logDir = null, $forceLog = false)
    {
        MPSetup::bootstrap();
        $storeId = MPSetup::getCurrentStoreId();

        if (!Mage::getConfig()) {
            return;
        }

        try {
            $logActive = Mage::getStoreConfig('dev/log/active', $storeId);
            if (empty($file)) {
                $file = Mage::getStoreConfig('dev/log/file', $storeId);
            }
        }
        catch (Exception $e) {
            $logActive = true;
        }

        if (!Mage::getIsDeveloperMode() && !$logActive && !$forceLog) {
            return;
        }

        static $loggers = array();

        $level  = is_null($level) ? Zend_Log::DEBUG : $level;
        $file = empty($file) ? 'system.log' : basename($file);

        // Validate file extension before save. Allowed file extensions: log, txt, html, csv
        if (!self::isLogFileExtensionValid($file)) {
            return;
        }

        try {
            if (!isset($loggers[$file])) {
                $logDir  = $logDir ? $logDir : Mage::getBaseDir('var') . DS . 'log';
                $logFile = $logDir . DS . $file;

                if (!is_dir($logDir)) {
                    mkdir($logDir);
                    chmod($logDir, 0750);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    chmod($logFile, 0640);
                }

                $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                $formatter = new Zend_Log_Formatter_Simple($format);
                $writerModel = (string)Mage::getConfig()->getNode('global/log/core/writer_model');
                if (!Mage::app() || !$writerModel) {
                    $writer = new Zend_Log_Writer_Stream($logFile);
                }
                else {
                    $writer = new $writerModel($logFile);
                }
                $writer->setFormatter($formatter);
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $loggers[$file]->log($message, $level);
        }
        catch (Exception $e) {
        }
    }

    public static function isLogFileExtensionValid($file)
    {
        $result = false;
        $validatedFileExtension = pathinfo($file, PATHINFO_EXTENSION);
        if ($validatedFileExtension && in_array($validatedFileExtension, self::getAllowedFileExtensions())) {
            $result = true;
        }

        return $result;
    }

    protected static function getAllowedFileExtensions()
    {
        return array('log', 'txt', 'html', 'csv');
    }
}
