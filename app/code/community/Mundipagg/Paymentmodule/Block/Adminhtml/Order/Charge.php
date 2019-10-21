<?php

class Mundipagg_Paymentmodule_Block_Adminhtml_Order_Charge extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'paymentmodule';
        $this->_controller = 'adminhtml_order_charge';
        $this->_headerText = Mage::helper('paymentmodule')
                                ->__('Charges');
 
        parent::__construct();
        $this->_removeButton('add');
    }
}
