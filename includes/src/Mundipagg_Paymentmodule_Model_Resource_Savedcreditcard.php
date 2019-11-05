<?php

class Mundipagg_Paymentmodule_Model_Resource_Savedcreditcard extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('paymentmodule/savedcreditcard', 'id');
    }
}