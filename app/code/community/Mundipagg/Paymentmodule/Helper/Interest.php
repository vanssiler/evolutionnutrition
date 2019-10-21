<?php

class Mundipagg_Paymentmodule_Helper_Interest extends Mage_Core_Helper_Abstract
{
    public function getInterestValue($installmentNum, $orderTotal, $cards = null, $cardBrand = 'default')
    {
        $installmentHelper = Mage::helper('paymentmodule/installment');
        $monetary = Mage::helper('paymentmodule/monetary');

        $orderTotal = $monetary->toFloat($orderTotal);

        $allInstallments = $installmentHelper->getInstallments($orderTotal, $cards);

        $installmentInterest = 0;

        $cardBrand = isset($allInstallments[$cardBrand]) ? $cardBrand : 'default';
        foreach($allInstallments[$cardBrand] as $installment) {
            if ($installment['times'] == $installmentNum) {
                $installmentInterest = $installment['interest'];
                break;
            }
        }
        $interest = $orderTotal * ($installmentInterest / 100);

        return round($interest,2);
    }

    public function getTotalInterestFromOrder($order)
    {
        $additionalInformation = $order->getPayment()->getAdditionalInformation();

        $method = $additionalInformation['mundipagg_payment_method'];
        $paymentData = $additionalInformation[$method];

        $creditCardData = array();
        if (isset($paymentData['creditcard'])) {
            $creditCardData = $paymentData['creditcard'];
        }

        $interest = 0;

        foreach ($creditCardData as $data) {
            $interest += $data['interest'];
        }

        return $interest;
    }
}
