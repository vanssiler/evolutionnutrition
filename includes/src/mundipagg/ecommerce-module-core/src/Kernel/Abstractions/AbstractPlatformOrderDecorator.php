<?php

namespace Mundipagg\Core\Kernel\Abstractions;

use Mundipagg\Core\Kernel\Interfaces\PlatformOrderInterface;
use Mundipagg\Core\Kernel\Repositories\OrderRepository;
use Mundipagg\Core\Kernel\Services\MoneyService;
use Mundipagg\Core\Kernel\Services\OrderLogService;
use Mundipagg\Core\Kernel\ValueObjects\OrderState;
use Mundipagg\Core\Kernel\ValueObjects\OrderStatus;
use Exception;

abstract class AbstractPlatformOrderDecorator implements PlatformOrderInterface
{
    protected $platformOrder;
    private $logService;

    public function __construct()
    {
        $this->logService = new OrderLogService();
    }

    public function addHistoryComment($message)
    {
        $message = 'MP - ' . $message;
        $this->addMPHistoryComment($message);
    }

    public function getPlatformOrder()
    {
        return $this->platformOrder;
    }

    public function setPlatformOrder($platformOrder)
    {
        $this->platformOrder = $platformOrder;
    }

    public function setStatus(OrderStatus $status)
    {
        $currentStatus = '';
        try {
            $currentStatus = $this->getStatus();
        } catch(\Exception $e) {

        } catch(\Throwable $e) {

        }

        $statusInfo = (object)[
            "from" => $currentStatus,
            "to" => $status,

        ];
       $this->logService->orderInfo(
           $this->getCode(),
           'Status Change',
           $statusInfo
       );

       $this->setStatusAfterLog($status);
    }

    public function setState(OrderState $state)
    {
        $currentState = '';
        try {
            $currentState = $this->getState();
        } catch(\Exception $e) {

        } catch(\Throwable $e) {

        }

        $stateInfo = (object)[
            "from" => $currentState,
            "to" => $state,

        ];
        $this->logService->orderInfo(
            $this->getCode(),
            'State Change',
            $stateInfo
        );

        $this->setStateAfterLog($state);
    }

    public function payAmount($amount)
    {
        $platformOrder = $this->getPlatformOrder();

        /*
         * @todo this format operations should be made by a currency format service.
         *      But before doing this, check if a decorator can depend on a service.
         */

        $amountInCurrency = number_format($amount / 100, 2);
        $grandTotal = number_format($platformOrder->getGrandTotal(), 2);
        $totalPaid = number_format($platformOrder->getTotalPaid(), 2);
        $totalDue = number_format($platformOrder->getTotalDue(), 2);

        $totalPaid += $amountInCurrency;
        if ($totalPaid > $grandTotal) {
            $totalPaid = $grandTotal;
        }

        $totalDue -= $amountInCurrency;
        if ($totalDue < 0) {
            $totalDue = 0;
        }

        $platformOrder->setTotalPaid($totalPaid);
        $platformOrder->setBaseTotalPaid($totalPaid);
        $platformOrder->setTotalDue($totalDue);
        $platformOrder->setBaseTotalDue($totalDue);

        return $amountInCurrency;
    }

    public function cancelAmount($amount)
    {
        $platformOrder = $this->getPlatformOrder();

        /*
         * @todo this format operations should be made by a currency format service.
         *      But before doing this, check if a decorator can depend on a service.
         */

        $amountInCurrency = number_format($amount / 100, 2);
        $grandTotal = number_format($platformOrder->getGrandTotal(), 2);
        $totalCanceled = number_format($platformOrder->getTotalCanceled(), 2);

        $totalCanceled += $amountInCurrency;
        if ($totalCanceled > $grandTotal) {
            $totalCanceled = $grandTotal;
        }

        $platformOrder->setTotalCanceled($totalCanceled);
        $platformOrder->setBaseTotalCanceled($totalCanceled);

        return $amountInCurrency;
    }

    public function refundAmount($amount)
    {
        $platformOrder = $this->getPlatformOrder();

        /*
         * @todo this format operations should be made by a currency format service.
         *      But before doing this, check if a decorator can depend on a service.
         */

        $amountInCurrency = number_format($amount / 100, 2);
        $grandTotal = number_format($platformOrder->getGrandTotal(), 2);
        $totalRefunded = number_format($platformOrder->getTotalRefunded(), 2);

        $totalRefunded += $amountInCurrency;
        if ($totalRefunded > $grandTotal) {
            $totalRefunded = $grandTotal;
        }

        $platformOrder->setTotalRefunded($totalRefunded);
        $platformOrder->setBaseTotalRefunded($totalRefunded);

        return $amountInCurrency;
    }

    public function getTotalPaidFromCharges()
    {
        $mpOrderId = $this->getMundipaggId();
        $grandTotal = $this->getGrandTotal();
        if ($mpOrderId === null) {
            return $grandTotal;
        }

        $orderRepository = new OrderRepository();
        $mpOrder = $orderRepository->findByMundipaggId($mpOrderId);
        if ($mpOrder === null) {
            return $grandTotal;
        }

        $grandTotal = 0;
        foreach ($mpOrder->getCharges() as $charge) {
            $grandTotal += $charge->getPaidAmount();
        }
        $moneyService = new MoneyService();
        $grandTotal = $moneyService->centsToFloat($grandTotal);

        return $grandTotal;
    }

    abstract protected function addMPHistoryComment($message);
    abstract protected function setStatusAfterLog(OrderStatus $status);
    abstract protected function setStateAfterLog(OrderState $state);
}