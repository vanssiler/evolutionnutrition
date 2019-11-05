<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreateVoucherPaymentRequest;

class Mundipagg_Paymentmodule_Model_Api_Voucher extends Mundipagg_Paymentmodule_Model_Api_Standard
{
    public function getPayment($paymentInfo)
    {
        $monetary = Mage::helper('paymentmodule/monetary');

        $result = array();

        foreach ($paymentInfo as $payment) {
            $paymentRequest = new CreatePaymentRequest();

            $voucherPaymentRequest = new CreateVoucherPaymentRequest();

            $voucherPaymentRequest->cardToken = '';
            $voucherPaymentRequest->capture = $this->getCaptureValue();

            if (isset($payment['token'])) {
                $voucherPaymentRequest->cardToken = $payment['token'];
            }

            if (
                $payment['SavedCreditCard'] &&
                $this->validateSavedVoucher($payment['SavedCreditCard'])
            ) {
                $voucherPaymentRequest->cardId = $payment['SavedCreditCard'];
            }

            $paymentRequest->paymentMethod = 'voucher';
            $paymentRequest->voucher = $voucherPaymentRequest;
            $paymentRequest->amount = round($monetary->toCents($payment['value']));
            $paymentRequest->customer = $this->getCustomer($payment);
            $paymentRequest->currency = $this->getCurrentCurrencyCode();

            $result[] = $paymentRequest;
        }

        return $result;
    }

    protected function getCaptureValue()
    {
        return $this->getConfigVoucherModel()->getOperationTypeFlag();
    }

    protected function getConfigVoucherModel()
    {
        return Mage::getModel('paymentmodule/config_voucher');
    }

    protected function validateSavedVoucher($mundipaggCardId)
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
