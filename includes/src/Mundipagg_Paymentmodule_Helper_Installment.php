<?php

class Mundipagg_Paymentmodule_Helper_Installment extends Mage_Core_Helper_Abstract
{
    public function getInstallments($total, $cards = null)
    {
        $cardConfig = Mage::getModel('paymentmodule/config_card');

        if ($cardConfig->isDefaultConfigurationEnabled()) {

            $brand = '';
            if (isset($cards[0])) {
                $brand = $cards[0];
            }

            return $this->getDefaultInstallments($total, $brand);
        }

        return $this->getCardsInstallments($total, $cards);
    }

    protected function getDefaultInstallments($total, $brand = '')
    {
        $cardConfig = Mage::getModel('paymentmodule/config_card');

        if (in_array(strtolower($brand), $cardConfig->getEnabledBrands())) {
            $max = $cardConfig->getDefaultMaxInstallmentNumber();
            $interest = $cardConfig->getDefaultInterest();
            $inc = $cardConfig->getDefaultIncrementalInterest();
            $minAmount = $cardConfig->getDefaultMinAmount();

            $maxWithout =
                $cardConfig->getDefaultMaxInstallmentNumberWithoutInterest();

            return array(
                'default' => array_merge(
                    $this->getInstallmentsWithoutInterest($total, $maxWithout, $minAmount),
                    $this->getInstallmentsWithInterest($total, $maxWithout, $max, $interest, $inc, $minAmount)
                )
            );
        }

        return array();
    }

    protected function getCardsInstallments($total, $cards = null)
    {
        $cardConfig = Mage::getModel('paymentmodule/config_card');

        if(!$cards) {
            $cards = array('Visa', 'Mastercard', 'Hipercard', 'Diners', 'Amex', 'Elo');
        }
        $installments = array();

        foreach ($cards as $card) {
            $enabled = 'is' . $card . 'Enabled';

            if ($cardConfig->$enabled()) {
                $max = $cardConfig->{'get' . $card . 'MaxInstallmentsNumber'}();
                $maxWithout = $cardConfig->{'get' . $card . 'MaxInstallmentsWithoutInterest'}();
                $interest = $cardConfig->{'get' . $card . 'Interest'}();
                $inc = $cardConfig->{'get' . $card . 'IncrementalInterest'}();
                $minAmount = $cardConfig->{'get' . $card . 'MinAmount'}();

                $installments[$card] = array_merge(
                    $this->getInstallmentsWithoutInterest($total, $maxWithout, $minAmount),
                    $this->getInstallmentsWithInterest(
                        $total,
                        $maxWithout,
                        $max,
                        $interest,
                        $inc,
                        $minAmount
                    )
                );
            }
        }
        return $installments;
    }

    protected function getInstallmentsWithoutInterest($total, $max, $minAmount)
    {
        $installments = array();
        $monetary = Mage::helper('paymentmodule/monetary');
        $currencySymbol = $monetary->getCurrentCurrencySymbol();
        $minAmount = $monetary->formatDecimals($minAmount);

        for ($i = 1; $i <= $max; $i++) {
            $totalAmount = $monetary->formatDecimals($total);
            $amount = $monetary->formatDecimals($total / $i);

            if ($this->isAmountLessThanMinAmount($amount, $minAmount, $monetary)) {
                continue;
            }

            $installments[] = array(
                'amount' =>  $currencySymbol . $amount,
                'times' => $i,
                'interest' => 0,
                'totalAmount' => $currencySymbol . $totalAmount
            );
        }

        return $installments;
    }

    protected function isAmountLessThanMinAmount($amout, $minAmount, $monetary)
    {
        if (empty($minAmount)) {
            return false;
        }

        if ($monetary->toCents($amout) < $monetary->toCents($minAmount)) {
            return true;
        }

        return false;
    }

    protected function getInstallmentsWithInterest(
        $total,
        $maxWithout,
        $max,
        $interest,
        $increment = 0,
        $minAmount
    ) {
        $installments = array();
        $monetary = Mage::helper('paymentmodule/monetary');
        $minAmount = $monetary->formatDecimals($minAmount);

        for ($i = $maxWithout + 1; $i <= $max; $i++) {
            $totalAmount = $monetary->formatDecimals($total * (1 + ($interest / 100)));
            $amount = $monetary->formatDecimals($total / $i);
            $currencySymbol = $monetary->getCurrentCurrencySymbol();

            if ($this->isAmountLessThanMinAmount($amount, $minAmount, $monetary)) {
                continue;
            }

            $interest = round($interest,2);
            $installments[] = array(
                'amount' =>  $currencySymbol . $amount,
                'times' => $i,
                'interest' => $interest,
                'totalAmount' => $currencySymbol . $totalAmount
            );

            $interest += $increment;
        }

        return $installments;
    }
}
