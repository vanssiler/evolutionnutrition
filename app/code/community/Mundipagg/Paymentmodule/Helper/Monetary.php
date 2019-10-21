<?php

class Mundipagg_Paymentmodule_Helper_Monetary extends Mage_Core_Helper_Abstract
{
    public function toCents($amount){
        return preg_replace('/[^0-9]/', '', $amount);
    }

    public function toFloat($amount)
    {
        return preg_replace('/[^0-9]/', '', $amount) / 100;
    }

    public function toCurrencyFormat($amountInCents)
    {
        return Mage::helper('core')->currency($amountInCents, true, false);
    }

    public function formatDecimals($number) {
        return number_format($number, 2, ',', '.');
    }

    public function getCurrentCurrencySymbol()
    {
        return Mage::app()
            ->getLocale()
            ->currency(
                Mage::app()
                    ->getStore()
                    ->getCurrentCurrencyCode()
            )
            ->getSymbol();
    }
}