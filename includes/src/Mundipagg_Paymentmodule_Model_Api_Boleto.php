<?php

require_once Mage::getBaseDir('lib') . '/autoload.php';

use MundiAPILib\Models\CreateCustomerRequest;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreateBoletoPaymentRequest;

class Mundipagg_Paymentmodule_Model_Api_Boleto extends Mundipagg_Paymentmodule_Model_Api_Standard
{
    public function getPayment($paymentInfo)
    {
        $boletoConfig = Mage::getModel('paymentmodule/config_boleto');
        $monetary = Mage::helper('paymentmodule/monetary');

        $bank = $boletoConfig->getBank();
        $instructions = $boletoConfig->getInstructions();
        $dueAt = $boletoConfig->getDueAt();

        $result = array();

        foreach ($paymentInfo as $payment) {
            $paymentRequest = new CreatePaymentRequest();

            $boletoPaymentRequest = new CreateBoletoPaymentRequest();

            $boletoPaymentRequest->bank = $bank;
            $boletoPaymentRequest->instructions = $instructions;
            $boletoPaymentRequest->dueAt = $dueAt;

            $paymentRequest->paymentMethod = 'boleto';
            $paymentRequest->boleto = $boletoPaymentRequest;
            $paymentRequest->amount = round($monetary->toCents($payment['value']));
            $paymentRequest->customer = $this->getCustomer($payment);
            $paymentRequest->currency = $this->getCurrentCurrencyCode();

            $result[] = $paymentRequest;
        }

        return $result;
    }
}
