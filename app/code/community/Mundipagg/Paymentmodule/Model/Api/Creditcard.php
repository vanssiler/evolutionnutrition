<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use MundiAPILib\Models\CreateCustomerRequest;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;

class Mundipagg_Paymentmodule_Model_Api_Creditcard extends Mundipagg_Paymentmodule_Model_Api_Standard
{
    public function getPayment($paymentInfo)
    {
        $monetary = Mage::helper('paymentmodule/monetary');

        $result = array();

        foreach ($paymentInfo as $payment) {
            $paymentRequest = new CreatePaymentRequest();

            $creditCardPaymentRequest = new CreateCreditCardPaymentRequest();

            $creditCardPaymentRequest->installments = $payment['creditCardInstallments'];
            $creditCardPaymentRequest->cardToken = '';
            $creditCardPaymentRequest->capture = $this->getCaptureValue();

            if (isset($payment['token'])) {
                $creditCardPaymentRequest->cardToken = $payment['token'];
            }

            if (
                isset($payment['SavedCreditCard']) &&
                $this->validateSavedCreditCard($payment['SavedCreditCard'])
            ) {
                $creditCardPaymentRequest->cardId = $payment['SavedCreditCard'];
            }

            $paymentRequest->paymentMethod = 'credit_card';
            $paymentRequest->creditCard = $creditCardPaymentRequest;
            $paymentRequest->amount = round($monetary->toCents($payment['value']));
            $paymentRequest->customer = $this->getCustomer($payment);
            $paymentRequest->currency = $this->getCurrentCurrencyCode();

            $result[] = $paymentRequest;
        }

        return $result;
    }

    protected function getCaptureValue()
    {
        return $this->getConfigCardModel()->getOperationTypeFlag();
    }

    protected function getConfigCardModel()
    {
        return Mage::getModel('paymentmodule/config_card');
    }

    protected function validateSavedCreditCard($mundipaggCardId)
    {
        $session = Mage::getSingleton('customer/session');
        $model = Mage::getModel('paymentmodule/savedcreditcard');

        $customerId = $session->getCustomer()->getId();
        $card = $model->loadByMundipaggCardId($mundipaggCardId);

        if($card->getCustomerId() == $customerId) {
            return true;
        }

        return false;
    }
}
