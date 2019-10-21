<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Block_Adminhtml_HubIntegration
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        MPSetup::bootstrap();

        $moduleConfig = MPSetup::getModuleConfiguration();
        $hubPublicAppKey = MPSetup::getHubAppPublicAppKey();

        $hubDefaultConfig = MPSetup::loadModuleConfigurationByStore(
            MPSetup::getDefaultStoreId()
        );

        $storeId = MPSetup::getCurrentStoreId();

        $locale = strtolower(
            str_replace("_", "-", Mage::app()->getLocale()->getLocaleCode())
        );

        $initHubScript = "
            initHub(
                '$hubPublicAppKey',
                '$locale'
        ";

        $installScript = $this->getInstallScript($initHubScript, $storeId);
        $defaultInstallScript = $this->getDefaultInstallScript($initHubScript, $hubDefaultConfig);
        $storeInstallScript = $this->getStoreInstallScript($initHubScript, $moduleConfig, $storeId);

        $isHubEnable = $moduleConfig->isHubEnabled();

        return '
                <div id="hub-integation-button-container">
                    <div class="form-group">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-4">
                            <span id="mundipagg-hub"></span>
                        </div>
                    </div>
                </div>
            <style>
                #mundipagg-hub button:hover, button:active {
                    background: #178176;
                }
                #mundipagg-hub button {
                    background: #00b7b4
                }
            </style>
            <script>

                window.onload = function() {

                    var checkbox = document.getElementById("mundipagg_config_general_group_hub_integration_inherit");
                    var hasHubCurrentStore = ' . intval($isHubEnable).';
                    var useDefault = false;

                    if (checkbox !== null ) {
                        checkbox.onchange = function (e) {
                            useDefault = e.target.checked;
                            initScript(useDefault, hasHubCurrentStore);
                        };

                        if (checkbox.checked) {
                            useDefault = true;
                        }
                    }
                    initScript(useDefault, hasHubCurrentStore);
                    try {
                        document.querySelector("#mundipagg_config_general_group_hub_integration").value = 1;
                    } catch(e) {
                    }
                }

                var initScript = function (useDefault, hasInstalationStore) {

                    if(useDefault) {
                        ' . $defaultInstallScript . '
                        return;
                    }

                    if (hasInstalationStore) {
                         ' . $storeInstallScript . '
                         return;
                    }

                    ' . $installScript . '
                    return;
                }
            </script>
        ';
    }

    public function getInstallScript($initHubScript, $storeId)
    {
        if ($storeId !== null) {
            $initHubScript .= ",null,". $storeId;
        }

        $initHubScript .= ');';

        return $initHubScript;
    }

    public function getDefaultInstallScript($initHubScript, $hubDefaultConfig)
    {
        if($hubDefaultConfig !== null) {
            $hubDefaultId = "null";
            if ($hubDefaultConfig->isHubEnabled()) {
                $hubDefaultId = "'" . $hubDefaultConfig->getHubInstallId()->getValue() . "'";
            }

            $initHubScript .= ",". $hubDefaultId;
        }
        $initHubScript .= ',0);';

        return $initHubScript;

    }

    public function getStoreInstallScript($initHubScript, $moduleConfig, $storeId)
    {
        $isHubEnable = $moduleConfig->isHubEnabled();

        if ($isHubEnable) {
            $initHubScript .= ",'". $moduleConfig->getHubInstallId()->getValue() . "'";
        }

        if ($storeId !== null) {
            $withHub = !$isHubEnable ? ',null': "";
            $initHubScript .= $withHub . ",". $storeId;
        }

        $initHubScript .= ');';

        return $initHubScript;
    }
}