<?php

namespace Mundipagg\Magento\Concrete;


use Mundipagg\Core\Kernel\Abstractions\AbstractPlatformOrderDecorator;
use Mundipagg\Core\Kernel\Interfaces\PlatformInvoiceInterface;
use Mundipagg\Core\Kernel\ValueObjects\Id\OrderId;
use Mundipagg\Core\Kernel\ValueObjects\OrderState;
use Mundipagg\Core\Kernel\ValueObjects\OrderStatus;
use Mundipagg\Core\Payment\Aggregates\Customer;
use Mundipagg\Core\Payment\Aggregates\Item;
use Mundipagg\Core\Payment\Aggregates\Payments\AbstractPayment;
use Mundipagg\Core\Payment\Aggregates\Shipping;

final class MagentoOrderDecorator extends AbstractPlatformOrderDecorator
{

    protected function addMPHistoryComment($message)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement addMPHistoryComment() method.
    }

    public function save()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement save() method.
    }

    public function setState(OrderState $state)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setState() method.
    }

    /**
     *
     * @return OrderState
     */
    public function getState()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getState() method.
    }

    public function setStatus(OrderStatus $status)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setStatus() method.
    }

    public function getStatus()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getStatus() method.
    }

    public function loadByIncrementId($incrementId)
    {
        $this->platformOrder = \Mage::getModel('sales/order')->loadByIncrementId($incrementId);
    }

    public function getHistoryCommentCollection()
    {
        $orderHistoryCollection = $this->getPlatformOrder()
            ->getStatusHistoryCollection(true);

        $orderHistory = array();
        foreach ($orderHistoryCollection as $history) {
            $historyData = $history->getData();

            $historyData['comment'] = htmlspecialchars($historyData["comment"]);

            $orderHistory[] = $historyData;
        }
        return $orderHistory;
    }

    public function setIsCustomerNotified()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setIsCustomerNotified() method.
    }

    public function canInvoice()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement canInvoice() method.
    }

    public function canUnhold()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement canUnhold() method.
    }

    public function isPaymentReview()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement isPaymentReview() method.
    }

    public function isCanceled()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement isCanceled() method.
    }

    public function getIncrementId()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getIncrementId() method.
    }

    public function getGrandTotal()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getGrandTotal() method.
    }

    public function getTotalPaid()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getTotalPaid() method.
    }

    public function getTotalDue()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getTotalDue() method.
    }

    public function setTotalPaid($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setTotalPaid() method.
    }

    public function setBaseTotalPaid($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setBaseTotalPaid() method.
    }

    public function setTotalDue($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setTotalDue() method.
    }

    public function setBaseTotalDue($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setBaseTotalDue() method.
    }

    public function setTotalCanceled($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setTotalCanceled() method.
    }

    public function setBaseTotalCanceled($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setBaseTotalCanceled() method.
    }

    public function getTotalRefunded()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getTotalRefunded() method.
    }

    public function setTotalRefunded($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setTotalRefunded() method.
    }

    public function setBaseTotalRefunded($amount)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setBaseTotalRefunded() method.
    }

    public function getCode()
    {
        return $this->getPlatformOrder()->getIncrementId();
    }

    public function getData()
    {
        return $this->getPlatformOrder()->getData();
    }

    /**
     *
     * @return OrderId
     */
    public function getMundipaggId()
    {
        // @todo return the correct Mundipagg OrderId for this order.
        return null ;
    }

    /**
     *
     * @return PlatformInvoiceInterface[]
     */
    public function getInvoiceCollection()
    {
        $invoicesCollection = \Mage::getModel('sales/order_invoice')
            ->getCollection()
            ->addAttributeToFilter('order_id', array('eq' => $this->getPlatformOrder()->getEntityId()));
        $invoices = array();
        foreach ($invoicesCollection as $invoice) {
            $invoices[] = new MagentoInvoiceDecorator($invoice);
        }

        return $invoices;
    }

    public function getTransactionCollection()
    {
        // @todo there is no transaction in m1m1.
        //        To fulfill this method purpose, check where the module saves the info
        //        related to NSU, Acquirer return message, etc.
        //        See Magento2OrderDecorator::getTransactionCollection in Mage2 Module.
        return [];
    }

    public function getPaymentCollection()
    {
        return [
            $this->getPlatformOrder()->getPayment()->getData()
        ];
    }

    protected function setStatusAfterLog(OrderStatus $status)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setStatusAfterLog() method.
    }

    protected function setStateAfterLog(OrderState $state)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setStateAfterLog() method.
    }

    public function getBaseTaxAmount()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getBaseTaxAmount() method.
    }

    /** @return Customer */
    public function getCustomer()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getCustomer() method.
    }

    /** @return Item[] */
    public function getItemCollection()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getItemCollection() method.
    }

    /** @return AbstractPayment[] */
    public function getPaymentMethodCollection()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getPaymentMethodCollection() method.
    }

    /** @return null|Shipping */
    public function getShipping()
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement getShipping() method.
    }

    public function setPlatformOrder($platformOrder)
    {
        throw new \Exception(__METHOD__ . ' not implemented!'); // @TODO: Implement setPlatformOrder() method.
    }

    /** @since  1.6.5 */
    public function getTotalCanceled()
    {
        //@TODO: Implement getTotalCanceled() method.
    }
}