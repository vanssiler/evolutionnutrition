<?php

namespace Mundipagg\Magento\Concrete;


use JsonSerializable;
use Mundipagg\Core\Kernel\Abstractions\AbstractInvoiceDecorator;
use Mundipagg\Core\Kernel\Interfaces\PlatformOrderInterface;
use Mundipagg\Core\Kernel\ValueObjects\InvoiceState;

final class MagentoInvoiceDecorator
    extends AbstractInvoiceDecorator
    implements JsonSerializable
{

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function setState(InvoiceState $state)
    {
        // TODO: Implement setState() method.
    }

    public function loadByIncrementId($incrementId)
    {
        // TODO: Implement loadByIncrementId() method.
    }

    public function getIncrementId()
    {
        return $this->platformInvoice->getIncrementId();
    }

    public function prepareFor(PlatformOrderInterface $order)
    {
        // TODO: Implement prepareFor() method.
    }

    public function createFor(PlatformOrderInterface $order)
    {
        // TODO: Implement createFor() method.
    }

    public function canRefund()
    {
        return $this->platformInvoice->canRefund();
    }

    public function isCanceled()
    {
        return $this->platformInvoice->isCanceled();
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->platformInvoice->getData();
    }

    /**
     * @since 1.7.2
     */
    protected function addMPComment($comment)
    {
        // TODO: Implement addMPComment() method.
    }
}