<?php

class Mundipagg_Paymentmodule_WalletController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        Mage::helper('paymentmodule/exception')->initExceptionHandler();
    }

    public function indexAction()
    {
        $savedCreditCardsHelper = Mage::helper('paymentmodule/savedcreditcard');
        $isUserLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();

        if (
            !$isUserLoggedIn ||
            !$savedCreditCardsHelper->isSavedCreditCardsEnabled()
        ) {
            $this->_redirect('customer/account/', array('_secure' => true));
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function deleteAction()
    {
        $savedCreditCardsHelper = Mage::helper('paymentmodule/savedcreditcard');
        $isUserLoggedIn = Mage::getSingleton('customer/session')->isLoggedIn();

        if (
            $isUserLoggedIn &&
            $savedCreditCardsHelper->isSavedCreditCardsEnabled()
        ) {
            $savedCreditCards = $savedCreditCardsHelper->getCurrentCustomerSavedCards();

            $deleteCardId = $this->getRequest()->getParam('cardId');
            if ($deleteCardId !== null) {
                $deleteCardId = intval($deleteCardId);
            }

            foreach ($savedCreditCards as $savedCreditCard) {
                if (intval($savedCreditCard->getId()) === $deleteCardId) {
                    $savedCreditCardsHelper
                        ->deleteByMundipaggCardId(
                            $savedCreditCard->getMundipaggCardId()
                        );
                    break;
                }
            }
        }

        $this->_redirect('mp-paymentmodule/wallet/', array('_secure' => true));
    }
}