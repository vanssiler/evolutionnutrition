<?php

class Mundipagg_Paymentmodule_Helper_Phone extends Mage_Core_Helper_Abstract
{
    public function extractPhoneVarienFromRawPhoneNumber($rawPhoneNumber)
    {
        $cleanPhone = preg_replace( '/[^0-9]/', '', $rawPhoneNumber);
        $cleanPhone = ltrim($cleanPhone, '0');

        $phone = new Varien_Object();

        $phone->setCountryCode('55');
        $phone->setAreacode(substr($cleanPhone, 0, 2));
        $phone->setNumber(substr($cleanPhone, 2));

        return $phone;
    }
}