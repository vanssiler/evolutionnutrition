<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use Mundipagg\Magento\Concrete\MagentoModuleCoreSetup as MPSetup;

class Mundipagg_Paymentmodule_Block_Form_Partial_Voucher extends Mundipagg_Paymentmodule_Block_Base
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paymentmodule/form/partial/voucher.phtml');
    }

    public function getPublicKey()
    {
        $moduleConfig = MPSetup::getModuleConfiguration();
        if (!empty($moduleConfig->getPublicKey())) {
            return $moduleConfig->getPublicKey()->getValue();
        }
        return null;
    }


    /**
     * Return saved cards for logged in customers
     * @param boolean $onlyEnabledCards Only enabled card brands in admin panel
     * @return array
     */
    public function getSavedCreditCards($onlyEnabledCards = true)
    {
        $session = Mage::getSingleton('customer/session');
        $savedCreditCardsHelper =
            Mage::helper('paymentmodule/savedcreditcard');

        if (
            $session->isLoggedIn() &&
            $savedCreditCardsHelper->isSavedCreditCardsEnabled()
        ) {
            if ($onlyEnabledCards) {
                $savedCards = $savedCreditCardsHelper->enabledSavedCreditCards('voucher');
                return $this->filterAllowedSavedCreditCardBrands($savedCards);
            }

            return $savedCreditCardsHelper->getCurrentCustomerSavedCards();
        }

        return array();
    }

    private function filterAllowedSavedCreditCardBrands($savedCards)
    {
        $enabledBrands = $this->getEnabledBrands();
        $customerSavedCreditCards = array();

        foreach ($savedCards as $card) {
            if (in_array(strtolower($card->getBrandName()), $enabledBrands)) {
                $customerSavedCreditCards[] = $card;
            }
        }

        return $customerSavedCreditCards;
    }

    public function isSavedCreditCardsEnabled()
    {
        return Mage::helper('paymentmodule/savedcreditcard')
            ->isSavedCreditCardsEnabled();
    }

    public function getEnabledBrands()
    {
        return Mage::getModel('paymentmodule/config_voucher')
            ->getEnabledBrands();
    }

    public function getCustomerDocument()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getData('taxvat');
    }
}
