<?php

class Mundipagg_Paymentmodule_Model_CreditCardOperationType
{
    public function toOptionArray()
    {
        return array(
                'AuthAndCapture' => 'Auth and capture',
                'AuthOnly' => 'Auth only',
            );
    }
}
