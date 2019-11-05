<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Block_Checkout_Payment_Review_Container extends Enterprise_Pbridge_Block_Checkout_Payment_Review_Container
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        try {
            return parent::_toHtml();
        } catch (\Exception $exception) {
            return '';
        }
    }
}
