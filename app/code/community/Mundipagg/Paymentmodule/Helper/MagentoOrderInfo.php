<?php

use Mundipagg\Integrity\AbstractOrderInfo;

class Mundipagg_Paymentmodule_Helper_MagentoOrderInfo extends AbstractOrderInfo
{
    protected function _loadOrder($id)
    {
        $order = \Mage::getModel('sales/order')->loadByIncrementId($id);
        if (!$order->getIncrementId()) {
            return null;
        }
        return $order;
    }

    protected function _getOrderInfo()
    {
        return $this->getOrder()->getData();
    }

    protected function _getOrderHistory()
    {
        $orderHistoryCollection = $this->order->getStatusHistoryCollection(true);
        $orderHistory = array();
        foreach ($orderHistoryCollection as $history) {
            $orderHistory[] =  $history->getData();
        }
        return $orderHistory;
    }

    protected function _getOrderCharges()
    {
        return $this->order
                ->getPayment()
                ->getAdditionalInformation('mundipagg_payment_module_charges');
    }

    protected function _getOrderInvoices()
    {
        $invoicesCollection = Mage::getModel('sales/order_invoice')
            ->getCollection()
            ->addAttributeToFilter('order_id', array('eq' => $this->order->getEntityId()));
        $invoices = array();
        foreach ($invoicesCollection as $invoice) {
            $invoices[] = $invoice->getData();
        }

        return $invoices;
    }
}