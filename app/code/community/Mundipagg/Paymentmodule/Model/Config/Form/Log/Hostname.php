<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Model_Config_Form_Log_Hostname extends Mage_Core_Model_Config_Data
{
    public function getCommentText($element, $currentValue)
    {
        MPSetup::bootstrap();
        $storeId = MPSetup::getCurrentStoreId();

        $comment  = 'Enabling this option will add the host name to the log filename. ';
        $comment .= "With the host name being '<strong>%s</strong>', ";
        $comment .= 'the log file will be saved with the following name: <br /><br />';
        $comment .= '<span id="mundipagg_log_file_name_example"></span>/';

        $isNonDefaultDir = Mage::getStoreConfig(
            'mundipagg_config/log_group/non_default_dir',
                $storeId
            ) == '1';
        $isNonDefaultDir = $isNonDefaultDir ? 'true' : 'false';

        $hostname = gethostname();

        $logPrefixFilenamePrefix =
            Mage::helper('paymentmodule/log')
                ->getModuleLogFilenamePrefix();
        $date = date('Y-m-d');
        $fileName = "<em>{$logPrefixFilenamePrefix}{$date}_<strong>$hostname</strong>.log</em>";

        $platformDefaultLogDir = 'var/log';
        $mundipaggLogFileNameExampleId = 'mundipagg_log_file_name_example';
        $mundipaggLogPathId = 'mundipagg_config_log_group_log_path';
        $mundipaggLogNonDefaultDirId = 'mundipagg_config_log_group_non_default_dir';

        $script = "
            <script>
            document.addEventListener('DOMContentLoaded', 
            function() {
                document.querySelector('#". $mundipaggLogFileNameExampleId ."').innerHTML = '" . $platformDefaultLogDir . "'; 
                if (" . $isNonDefaultDir . ") {
                     document.querySelector('#". $mundipaggLogFileNameExampleId ."').innerHTML = 
                        document.querySelector('#". $mundipaggLogPathId ."').value;         
                }
                document.querySelector('#". $mundipaggLogNonDefaultDirId ."').onchange = function () {
                    document.querySelector('#". $mundipaggLogPathId ."').dispatchEvent(new Event('input'));
                };
                document.querySelector('#". $mundipaggLogPathId ."').oninput = function() {
                    document.querySelector('#". $mundipaggLogFileNameExampleId ."').innerHTML = '" . $platformDefaultLogDir . "'; 
                    var isNonDefaultDir = 
                            document.querySelector('#". $mundipaggLogNonDefaultDirId ."').value == '1';                        
                    if (isNonDefaultDir) {
                        document.querySelector('#". $mundipaggLogFileNameExampleId ."').innerHTML = this.value;
                    }
                };
            },false);
            </script>
        ";

        $commentRendered = sprintf($comment, $hostname) . $fileName .  $script;

        return $commentRendered;
    }
}