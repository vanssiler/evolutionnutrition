<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */
class Amasty_Scheckout_Model_Country extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('amscheckout/country');
    }
}