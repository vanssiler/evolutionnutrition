<?php

class  Mundipagg_Paymentmodule_Block_Adminhtml_Order_Charge_Popup extends Mage_Adminhtml_Block_Widget_Form
{
    protected $orderId = 0;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paymentmodule/chargePopup.phtml');
        $this->orderId = Mage::app()->getRequest()->get('order_id');

        $adminUser = Mage::getSingleton('admin/session')->getUser();
        $this->adminUsername = $adminUser->getUsername();
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getAdminUsername()
    {
        return $this->adminUsername;
    }
}