<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Model_Config_Form_Log_Path extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        MPSetup::bootstrap();
        $storeId = MPSetup::getCurrentStoreId();

        if (Mage::getStoreConfig('mundipagg_config/log_group/non_default_dir', $storeId) == '1') {
            $logPath = $this->getValue();

            $checkLogFile = $logPath . DS . 'mundipagg_checklogfile';

            $havePermissions = true;
            if (!is_dir($logPath)) {
                if (!mkdir($logPath)) {
                    $havePermissions = false;
                }
            }

            if (file_put_contents($checkLogFile, '') === false) {
                $havePermissions = false;
            }
            unlink($checkLogFile);

            $exceptionMessage =
                "Unable to configure '%s' as log saving directory: " .
                "The directory does not have write permissions for the server user.";

            if (!$havePermissions) {
                Mage::throwException(sprintf($exceptionMessage,$logPath));
            }
        }

        return parent::save();
    }

    public function getCommentText($element, $currentValue)
    {
        MPSetup::bootstrap();
        $storeId = MPSetup::getCurrentStoreId();

        $comment = 'Directory in which log files will be saved.';

        $permissionWarning = "<br /><br /><span style='color:red;'>Warning! The server user does not have write ";
        $permissionWarning .= " permissions on directory <strong>'%s'</strong>!<br />";
        $permissionWarning .= "To enable the correct saving of the log files, please change this permission.</span>";

        if (Mage::getStoreConfig('mundipagg_config/log_group/non_default_dir', $storeId) == '1') {
            $logPath = Mage::getStoreConfig('mundipagg_config/log_group/log_path', $storeId);

            $checkLogFile = $logPath . DS . 'mundipagg_checklogfile';

            $havePermissions = true;
            if (!is_dir($logPath)) {
                if (!mkdir($logPath)) {
                    $havePermissions = false;
                }
            }

            if (file_put_contents($checkLogFile, '') === false) {
                $havePermissions = false;
            }
            unlink($checkLogFile);

            if (!$havePermissions) {
                $comment .= sprintf($permissionWarning,$logPath);
            }
        }

        return $comment;
    }
}