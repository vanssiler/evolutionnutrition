<?php

class Mundipagg_Paymentmodule_Block_Customer_Account_Wallet extends Mundipagg_Paymentmodule_Block_Base
{
    protected $savedCreditCards = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paymentmodule/customer/account/wallet.phtml');
    }

    public function getTitle()
    {
        return "My Cards";
    }

    public function getSavedCreditCards()
    {
        if (!$this->savedCreditCards) {
            $savedCreditCardsHelper = Mage::helper('paymentmodule/savedcreditcard');
            $this->savedCreditCards = $savedCreditCardsHelper->getCurrentCustomerSavedCards();
        }

        return $this->savedCreditCards;
    }

    public function hasSavedCards()
    {
        return count($this->getSavedCreditCards()) > 0;
    }
}