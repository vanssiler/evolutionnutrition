<?php

class PagarMe_Core_Model_Sdk_Adapter extends Mage_Core_Model_Abstract
{
    /**
     * @var \PagarMe\Sdk\PagarMe
     */
    private $pagarMeSdk;

    /**
     * Timeout in seconds
     *
     * @var int
     */
    const SDK_TIMEOUT = 15;

    public function _construct()
    {
        parent::_construct();

        $apiKey = Mage::getStoreConfig(
            'payment/pagarme_configurations/general_api_key'
        );
        $this->pagarMeSdk = new \PagarMe\Sdk\PagarMe(
            $apiKey,
            self::SDK_TIMEOUT,
            $this->getUserAgent()
        );
    }

    /**
     * @return \PagarMe\Sdk\PagarMe
     */
    public function getPagarMeSdk()
    {
        return $this->pagarMeSdk;
    }

    /**
     * @return array
     */
    public function getUserAgent()
    {
        $userAgentValue = sprintf(
            'Magento/%s PagarMe/%s PHP/%s',
            Mage::getVersion(),
            Mage::getConfig()->getNode()->modules->PagarMe_Core->version,
            phpversion()
        );

        return [
            'User-Agent' => $userAgentValue,
            'X-PagarMe-User-Agent' => $userAgentValue
        ];
    }
}
