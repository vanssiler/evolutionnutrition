<?php

class Mundipagg_Paymentmodule_Helper_Invoice extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function cancelInvoices($order)
    {
        $invoices = $this->getInvoicesAllowedToCancel($order);

        // Refund invoices and Credit Memo
        if (!empty($invoices)) {

            foreach ($invoices as $invoice) {
                $this->closeInvoice($invoice);
                $totalRefunded = $order->getBaseTotalRefunded();
                if (!$totalRefunded) {
                    $this->createCreditMemo($invoice, $order);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     */
    public function closeInvoice($invoice)
    {
        $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_CANCELED);
        $invoice->save();
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param Mage_Sales_Model_Order $order
     */
    public function createCreditMemo($invoice, $order)
    {
        $service = Mage::getModel('sales/service_order', $order);

        $creditmemo = $service->prepareInvoiceCreditmemo($invoice);
        $creditmemo->setOfflineRequested(true);
        $creditmemo->register()->save();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getInvoicesAllowedToCancel($order)
    {
        $invoices = array();

        foreach ($order->getInvoiceCollection() as $invoice) {
            // We check if invoice can be refunded
            if ($invoice->canRefund() && !$invoice->isCanceled()) {
                $invoices[] = $invoice;
            }
        }

        return $invoices;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param float $amount
     * @throws Exception
     */
    public function addInvoiceToOrder($order, $amount)
    {
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
        $invoice->register();
        $invoice->setBaseGrandTotal($amount);
        $invoice->setGrandTotal($amount);
        $invoice->setRequestedCaptureCase('online')->setCanVoidFlag(false)->pay();
        $order->save();

        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();
    }
}