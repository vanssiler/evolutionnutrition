<?php

class Mundipagg_Paymentmodule_Model_Enum_Orderhistory
{
    protected $chargeCreated  = 'MP - Charge created';
    protected $chargePaid  = 'MP - Charge paid: ';
    protected $chargeOverpaid = 'MP - Charge orverpaid: ';
    protected $chargeUnderpaid = 'MP - Charge underpaid: ';
    protected $chargeCanceled = 'MP - Charge canceled: ';
    protected $chargeRefunded = 'MP - Charge refunded: ';
    protected $notAuthorized = 'Payment not authorized ';
    protected $chargePartialCanceled = 'MP - Partial canceled amount: ';

    public function __call($name, $arguments)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return '';
    }
}